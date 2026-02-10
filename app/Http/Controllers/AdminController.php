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
        // Optimize revenue calculation with single query
        $revenueData = SubscriptionRequest::where('status', 'approved')
            ->selectRaw('
                SUM(CASE 
                    WHEN requested_plan = "pro" THEN 29 
                    WHEN requested_plan = "enterprise" THEN 99 
                    ELSE 0 
                END) as total_revenue,
                SUM(CASE 
                    WHEN requested_plan = "pro" AND MONTH(approved_at) = ? AND YEAR(approved_at) = ? THEN 29 
                    WHEN requested_plan = "enterprise" AND MONTH(approved_at) = ? AND YEAR(approved_at) = ? THEN 99 
                    ELSE 0 
                END) as month_revenue
            ', [now()->month, now()->year, now()->month, now()->year])
            ->first();

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
            'revenue_this_month' => $revenueData->month_revenue ?? 0,
            'revenue_total' => $revenueData->total_revenue ?? 0,
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
        $statsData = User::selectRaw('
            COUNT(*) as total_users,
            SUM(CASE WHEN email_verified_at IS NOT NULL THEN 1 ELSE 0 END) as active_users,
            SUM(CASE WHEN email_verified_at IS NULL THEN 1 ELSE 0 END) as unverified_users,
            SUM(CASE WHEN is_admin = 1 THEN 1 ELSE 0 END) as admin_users,
            SUM(CASE WHEN plan = "free" THEN 1 ELSE 0 END) as free_plan,
            SUM(CASE WHEN plan = "pro" THEN 1 ELSE 0 END) as pro_plan,
            SUM(CASE WHEN plan = "enterprise" THEN 1 ELSE 0 END) as enterprise_plan,
            SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END) as users_today,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as users_this_week,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as users_this_month
        ', [today(), now()->subDays(7), now()->subDays(30)])->first();
        
        $stats = [
            'total_users' => $statsData->total_users ?? 0,
            'active_users' => $statsData->active_users ?? 0,
            'unverified_users' => $statsData->unverified_users ?? 0,
            'admin_users' => $statsData->admin_users ?? 0,
            'free_plan' => $statsData->free_plan ?? 0,
            'pro_plan' => $statsData->pro_plan ?? 0,
            'enterprise_plan' => $statsData->enterprise_plan ?? 0,
            'users_today' => $statsData->users_today ?? 0,
            'users_this_week' => $statsData->users_this_week ?? 0,
            'users_this_month' => $statsData->users_this_month ?? 0,
        ];
        
        $users = User::select('users.*')
            ->selectRaw('(SELECT COUNT(*) FROM routers WHERE routers.user_id = users.id) as routers_count')
            ->selectRaw('(SELECT COUNT(*) FROM vouchers WHERE vouchers.user_id = users.id) as vouchers_count')
            ->latest()
            ->paginate(20);
        
        return view('admin.users', compact('users', 'stats'));
    }

    public function toggleAdmin(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('alert_error', 'Cannot modify your own admin status');
        }
        
        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            return back()->with('alert_error', 'Cannot remove the last admin user');
        }
        
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'plan' => 'required|in:free,pro,enterprise',
                'voucher_limit' => 'required|integer|min:0|max:100000',
                'is_admin' => 'sometimes|boolean',
            ]);

            if (isset($validated['is_admin']) && $user->id === auth()->id()) {
                return back()->with('alert_error', 'Cannot modify your own admin status');
            }

            if (isset($validated['is_admin']) && !$validated['is_admin']) {
                if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
                    return back()->with('alert_error', 'Cannot remove the last admin user');
                }
            }

            $user->update($validated);
            return redirect()->route('admin.users')->with('alert_success', 'User updated successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to update user', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return back()->withInput()->with('alert_error', 'Failed to update user. Please try again.');
        }
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
