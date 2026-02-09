<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Router;
use App\Models\VoucherRedemption;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    public function index()
    {
        $routerId = request('router');
        $search = request('search');
        $status = request('status');
        
        if (!$routerId) {
            return redirect()->route('routers.index')
                ->with('alert_error', 'Please select a router to view vouchers.');
        }
        
        $userRouterIds = Router::where('user_id', auth()->id())->pluck('id');
        $selectedRouter = Router::whereIn('id', $userRouterIds)->find($routerId);
        
        if (!$selectedRouter) {
            return redirect()->route('routers.index')
                ->with('alert_error', 'Router not found or access denied.');
        }
        
        $query = Voucher::with(['router', 'redemptions'])->where('router_id', $routerId);
        
        if ($search) {
            $query->where('code', 'like', '%' . $search . '%');
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $vouchers = $query->latest()->paginate(15);
        
        return view('vouchers.index', compact('vouchers', 'selectedRouter'));
    }

    public function create()
    {
        $routers = Router::where('user_id', auth()->id())->get();
        $selectedRouter = null;
        
        if (request('router')) {
            $selectedRouter = Router::where('user_id', auth()->id())->find(request('router'));
        }
        
        return view('vouchers.create', compact('routers', 'selectedRouter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'router_id' => 'required|exists:routers,id',
            'quantity' => 'required|integer|min:1|max:100',
            'voucher_type' => 'required|in:numbers,mixed',
            'auth_type' => 'required|in:same,different',
            'code_length' => 'required|integer|min:4|max:12',
            'duration' => 'required|integer|min:5|max:43200',
            'bandwidth' => 'required|string|max:50',
            'expires_in_days' => 'required|integer|min:1|max:365',
        ]);

        // Check voucher limit
        $user = auth()->user();
        $currentCount = Voucher::whereHas('router', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();

        if ($currentCount + $validated['quantity'] > $user->voucher_limit) {
            return back()->with('alert_error', "Voucher limit exceeded! Your plan allows {$user->voucher_limit} vouchers. You have {$currentCount} vouchers. Upgrade your plan to create more.");
        }

        $router = Router::find($validated['router_id']);
        $createdVouchers = [];

        DB::beginTransaction();
        try {
            for ($i = 0; $i < $validated['quantity']; $i++) {
                $code = $this->generateUniqueCode($validated['voucher_type'], $validated['code_length']);
                $password = $validated['auth_type'] === 'same' 
                    ? $code 
                    : $this->generateUniqueCode($validated['voucher_type'], $validated['code_length']);

                $voucher = Voucher::create([
                    'router_id' => $validated['router_id'],
                    'code' => $code,
                    'password' => $password,
                    'profile' => 'default',
                    'duration' => (int)$validated['duration'],
                    'bandwidth' => $validated['bandwidth'],
                    'status' => 'pending',
                    'expires_at' => now()->addDays((int)$validated['expires_in_days'])
                ]);

                $createdVouchers[] = $voucher;
            }

            DB::commit();
            return redirect()->route('vouchers.index', ['router' => $validated['router_id']])
                ->with('alert_success', count($createdVouchers) . ' vouchers created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('alert_error', 'Failed: ' . $e->getMessage());
        }
    }

    public function redeem(Request $request)
    {
        $key = 'redeem:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'success' => false,
                'error' => 'Too many attempts. Please try again later.'
            ], 429);
        }

        RateLimiter::hit($key, 300); // 5 minutes

        $validated = $request->validate([
            'code' => 'required|string',
            'mac' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $voucher = Voucher::where('code', $validated['code'])
                ->lockForUpdate()
                ->first();

            if (!$voucher) {
                $this->logRedemption(null, $request, 'failed', 'Voucher not found');
                return response()->json(['success' => false, 'error' => 'Invalid voucher code'], 404);
            }

            if (!$voucher->isRedeemable()) {
                $error = $voucher->isExpired() ? 'Voucher has expired' : 'Voucher already used';
                $this->logRedemption($voucher->id, $request, 'failed', $error);
                return response()->json(['success' => false, 'error' => $error], 400);
            }

            // MAC address binding check
            if ($voucher->device_mac && $voucher->device_mac !== $validated['mac']) {
                $this->logRedemption($voucher->id, $request, 'failed', 'Device mismatch');
                return response()->json(['success' => false, 'error' => 'This voucher is bound to another device'], 403);
            }

            // Create user on MikroTik
            $this->createMikrotikUser($voucher, $validated['mac']);

            // Update voucher status
            $voucher->update([
                'status' => 'active',
                'redeemed_at' => now(),
                'redeemed_ip' => $request->ip(),
                'device_mac' => $validated['mac'] ?? $voucher->device_mac,
                'device_info' => $request->userAgent(),
            ]);

            $this->logRedemption($voucher->id, $request, 'success');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Voucher activated successfully!',
                'data' => [
                    'username' => $voucher->code,
                    'password' => $voucher->password,
                    'duration' => $voucher->getDurationFormatted(),
                    'bandwidth' => $voucher->bandwidth,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            $this->logRedemption($voucher->id ?? null, $request, 'failed', $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Redemption failed: ' . $e->getMessage()], 500);
        }
    }

    private function createMikrotikUser($voucher, $mac = null)
    {
        $router = $voucher->router;
        $config = new \RouterOS\Config([
            'host' => $router->local_ip,
            'user' => $router->api_user,
            'pass' => $router->api_password,
            'port' => 8728,
            'timeout' => 5,
        ]);

        $client = new \RouterOS\Client($config);
        
        $addCommand = [
            '/ip/hotspot/user/add',
            '=name=' . $voucher->code,
            '=password=' . $voucher->password,
            '=profile=' . $voucher->profile,
            '=limit-uptime=' . $voucher->duration . 'm',
            '=comment=Passtik_Redeemed_' . now()->format('Y-m-d_H:i')
        ];

        if ($mac) {
            $addCommand[] = '=mac-address=' . $mac;
        }

        $client->query($addCommand)->read();
    }

    private function logRedemption($voucherId, $request, $status, $error = null)
    {
        if (!$voucherId) return;

        VoucherRedemption::create([
            'voucher_id' => $voucherId,
            'ip_address' => $request->ip(),
            'mac_address' => $request->input('mac'),
            'user_agent' => $request->userAgent(),
            'device_type' => $this->detectDeviceType($request->userAgent()),
            'status' => $status,
            'error_message' => $error,
            'metadata' => [
                'headers' => $request->headers->all(),
                'timestamp' => now()->toIso8601String(),
            ]
        ]);
    }

    private function detectDeviceType($userAgent)
    {
        if (preg_match('/mobile|android|iphone|ipad/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/tablet/i', $userAgent)) {
            return 'tablet';
        }
        return 'desktop';
    }

    private function generateUniqueCode($type, $length = 6)
    {
        do {
            if ($type === 'numbers') {
                $min = pow(10, $length - 1);
                $max = pow(10, $length) - 1;
                $code = str_pad(random_int($min, $max), $length, '0', STR_PAD_LEFT);
            } else {
                $code = strtoupper(Str::random($length));
            }
        } while (Voucher::where('code', $code)->exists());

        return $code;
    }

    public function show(Voucher $voucher)
    {
        $userRouterIds = Router::where('user_id', auth()->id())->pluck('id');
        
        if (!$userRouterIds->contains($voucher->router_id)) {
            return redirect()->route('vouchers.index')
                ->with('alert_error', 'Access denied.');
        }
        
        $voucher->load(['router', 'redemptions']);
        return view('vouchers.show', compact('voucher'));
    }

    public function destroy(Voucher $voucher)
    {
        $userRouterIds = Router::where('user_id', auth()->id())->pluck('id');
        
        if (!$userRouterIds->contains($voucher->router_id)) {
            return redirect()->route('vouchers.index')
                ->with('alert_error', 'Access denied.');
        }
        
        // Remove from MikroTik if active
        if ($voucher->status === 'active') {
            try {
                $this->removeMikrotikUser($voucher);
            } catch (\Exception $e) {
                // Continue even if removal fails
            }
        }
        
        $voucher->delete();
        return redirect()->route('vouchers.index')->with('alert_success', 'Voucher deleted successfully!');
    }

    private function removeMikrotikUser($voucher)
    {
        $router = $voucher->router;
        $config = new \RouterOS\Config([
            'host' => $router->local_ip,
            'user' => $router->api_user,
            'pass' => $router->api_password,
            'port' => 8728,
            'timeout' => 3,
        ]);

        $client = new \RouterOS\Client($config);
        $users = $client->query('/ip/hotspot/user/print', ['?name' => $voucher->code])->read();
        
        if (!empty($users)) {
            $client->query('/ip/hotspot/user/remove', ['.id' => $users[0]['.id']]);
        }
    }

    public function print(Request $request)
    {
        $routerId = $request->input('router');
        $status = $request->input('status', 'pending');
        
        $userRouterIds = Router::where('user_id', auth()->id())->pluck('id');
        $query = Voucher::with('router')->whereIn('router_id', $userRouterIds);
        
        if ($routerId) {
            if (!$userRouterIds->contains($routerId)) {
                return redirect()->route('vouchers.index')
                    ->with('alert_error', 'Access denied.');
            }
            $query->where('router_id', $routerId);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $vouchers = $query->latest()->get();
        $router = $routerId ? Router::find($routerId) : null;
        
        return view('vouchers.print', compact('vouchers', 'router', 'status'));
    }

    public function reports(Request $request)
    {
        $routerId = $request->input('router');
        $userRouterIds = Router::where('user_id', auth()->id())->pluck('id');
        
        $query = Voucher::with(['router', 'redemptions'])
            ->whereIn('router_id', $userRouterIds);
        
        if ($routerId) {
            $query->where('router_id', $routerId);
        }
        
        $totalVouchers = (clone $query)->count();
        $pendingVouchers = (clone $query)->where('status', 'pending')->count();
        $activeVouchers = (clone $query)->where('status', 'active')->count();
        $usedVouchers = (clone $query)->where('status', 'used')->count();
        $expiredVouchers = (clone $query)->where('status', 'expired')->count();
        
        $recentRedemptions = VoucherRedemption::with('voucher.router')
            ->whereHas('voucher', function($q) use ($userRouterIds) {
                $q->whereIn('router_id', $userRouterIds);
            })
            ->latest()
            ->limit(50)
            ->get();
        
        $redemptionsByDay = VoucherRedemption::whereHas('voucher', function($q) use ($userRouterIds) {
                $q->whereIn('router_id', $userRouterIds);
            })
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $routers = Router::where('user_id', auth()->id())->get();
        $selectedRouter = $routerId ? Router::find($routerId) : null;
        
        return view('vouchers.reports', compact(
            'totalVouchers', 'pendingVouchers', 'activeVouchers', 'usedVouchers', 
            'expiredVouchers', 'recentRedemptions', 'redemptionsByDay', 
            'routers', 'selectedRouter'
        ));
    }

    public function redeemPage()
    {
        return view('vouchers.redeem');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:vouchers,id'
        ]);

        $userRouterIds = Router::where('user_id', auth()->id())->pluck('id');
        $vouchers = Voucher::whereIn('id', $validated['ids'])
            ->whereIn('router_id', $userRouterIds)
            ->get();

        $deleted = 0;
        foreach ($vouchers as $voucher) {
            if ($voucher->status === 'active') {
                try {
                    $this->removeMikrotikUser($voucher);
                } catch (\Exception $e) {
                    // Continue even if removal fails
                }
            }
            $voucher->delete();
            $deleted++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$deleted} voucher(s) deleted successfully"
        ]);
    }

    public function bulkExport(Request $request)
    {
        $ids = explode(',', $request->input('ids', ''));
        $userRouterIds = Router::where('user_id', auth()->id())->pluck('id');
        
        $vouchers = Voucher::with('router')
            ->whereIn('id', $ids)
            ->whereIn('router_id', $userRouterIds)
            ->get();

        $csv = "Code,Password,Duration,Bandwidth,Status,Expires At,Router\n";
        foreach ($vouchers as $voucher) {
            $csv .= "{$voucher->code},{$voucher->password},{$voucher->getDurationFormatted()},{$voucher->bandwidth},{$voucher->status},";
            $csv .= ($voucher->expires_at ? $voucher->expires_at->format('Y-m-d H:i') : 'Never') . ",{$voucher->router->name}\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vouchers_' . date('Y-m-d_His') . '.csv"'
        ]);
    }

    public function bulkExpire(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:vouchers,id'
        ]);

        $userRouterIds = Router::where('user_id', auth()->id())->pluck('id');
        $updated = Voucher::whereIn('id', $validated['ids'])
            ->whereIn('router_id', $userRouterIds)
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        return response()->json([
            'success' => true,
            'message' => "{$updated} voucher(s) marked as expired"
        ]);
    }
}
