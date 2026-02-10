<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Router;
use App\Models\Voucher;
use App\Models\SubscriptionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_week' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'new_users_month' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'total_routers' => Router::count(),
            'active_routers' => Router::where('status', 'connected')->count(),
            'total_vouchers' => Voucher::count(),
            'active_vouchers' => Voucher::where('status', 'active')->count(),
            'redeemed_vouchers' => Voucher::where('status', 'used')->count(),
            'expired_vouchers' => Voucher::where('status', 'expired')->count(),
            'pending_subscriptions' => SubscriptionRequest::where('status', 'pending')->count(),
            'approved_subscriptions' => SubscriptionRequest::where('status', 'approved')->count(),
            'revenue_this_month' => SubscriptionRequest::where('status', 'approved')
                ->whereMonth('approved_at', now()->month)
                ->sum(DB::raw("CASE 
                    WHEN requested_plan = 'pro' THEN 29 
                    WHEN requested_plan = 'enterprise' THEN 99 
                    ELSE 0 END")),
            'revenue_total' => SubscriptionRequest::where('status', 'approved')
                ->sum(DB::raw("CASE 
                    WHEN requested_plan = 'pro' THEN 29 
                    WHEN requested_plan = 'enterprise' THEN 99 
                    ELSE 0 END")),
        ];

        $user_growth = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $voucher_stats = Voucher::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $plan_distribution = User::selectRaw('plan, COUNT(*) as count')
            ->groupBy('plan')
            ->get();

        $recent_users = User::latest()->take(5)->get();
        $recent_subscriptions = SubscriptionRequest::with('user')->latest()->take(5)->get();
        $recent_routers = Router::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'user_growth',
            'voucher_stats',
            'plan_distribution',
            'recent_users',
            'recent_subscriptions',
            'recent_routers'
        ));
    }

    public function users()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => User::whereNull('email_verified_at')->count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'free_plan' => User::where('plan', 'free')->count(),
            'pro_plan' => User::where('plan', 'pro')->count(),
            'enterprise_plan' => User::where('plan', 'enterprise')->count(),
            'users_today' => User::whereDate('created_at', today())->count(),
            'users_this_week' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'users_this_month' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];
        
        $users = User::withCount(['routers', 'vouchers'])
            ->latest()
            ->paginate(20);
        
        return view('admin.users', compact('users', 'stats'));
    }

    public function toggleAdmin(User $user)
    {
        $user->update(['is_admin' => !$user->is_admin]);
        return back()->with('alert_success', 'Admin status updated successfully');
    }

    public function deleteUser(User $user)
    {
        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            return back()->with('alert_error', 'Cannot delete the last admin user');
        }
        
        if ($user->id === auth()->id()) {
            return back()->with('alert_error', 'Cannot delete your own account');
        }
        
        $userName = $user->name;
        $user->delete();
        return back()->with('alert_success', "User '{$userName}' deleted successfully");
    }

    public function showUser(User $user)
    {
        $user->load(['routers.vouchers', 'vouchers']);
        
        $stats = [
            'total_routers' => $user->routers()->count(),
            'active_routers' => $user->routers()->where('status', 'connected')->count(),
            'total_vouchers' => $user->vouchers()->count(),
            'active_vouchers' => $user->vouchers()->where('status', 'active')->count(),
            'used_vouchers' => $user->vouchers()->where('status', 'used')->count(),
            'expired_vouchers' => $user->vouchers()->where('status', 'expired')->count(),
        ];
        
        $recent_routers = $user->routers()->latest()->take(5)->get();
        $recent_vouchers = $user->vouchers()->with('router')->latest()->take(10)->get();
        
        return view('admin.users-show', compact('user', 'stats', 'recent_routers', 'recent_vouchers'));
    }

    public function editUser(User $user)
    {
        return view('admin.users-edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'plan' => 'required|in:free,pro,enterprise',
            'voucher_limit' => 'required|integer|min:0',
            'is_admin' => 'boolean',
        ]);

        $user->update($validated);
        return redirect()->route('admin.users')->with('alert_success', 'User updated successfully');
    }

    public function routers()
    {
        $routers = Router::with('user')->latest()->paginate(20);
        return view('admin.routers', compact('routers'));
    }

    public function vouchers()
    {
        $vouchers = Voucher::with(['router', 'router.user'])->latest()->paginate(50);
        return view('admin.vouchers', compact('vouchers'));
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'support_email' => 'required|email',
            'support_phone' => 'required|string',
        ]);

        // Update .env file or use config cache
        return back()->with('alert_success', 'Settings updated successfully');
    }
}
