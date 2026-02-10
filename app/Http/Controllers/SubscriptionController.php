<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionRequest;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function requestUpgrade(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|in:pro,enterprise',
            'phone' => 'required|string|max:20',
            'message' => 'nullable|string|max:500',
        ]);

        $existing = SubscriptionRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('alert_error', 'You already have a pending upgrade request.');
        }

        SubscriptionRequest::create([
            'user_id' => auth()->id(),
            'requested_plan' => $validated['plan'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
            'status' => 'pending',
        ]);

        return back()->with('alert_success', 'Upgrade request submitted! We will contact you at ' . htmlspecialchars($validated['phone'], ENT_QUOTES, 'UTF-8') . ' within 24 hours.');
    }

    public function showUpgradePage()
    {
        $user = auth()->user();
        $pendingRequest = SubscriptionRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        return view('subscription.upgrade', compact('user', 'pendingRequest'));
    }

    // Admin methods
    public function adminIndex()
    {
        $requests = SubscriptionRequest::with('user')->latest()->paginate(20);
        return view('admin.subscriptions', compact('requests'));
    }

    public function approve($id)
    {
        $request = SubscriptionRequest::findOrFail($id);
        
        if ($request->status !== 'pending') {
            return back()->with('alert_error', 'This request has already been processed.');
        }
        
        $user = $request->user;

        $limits = [
            'pro' => 500,
            'enterprise' => 999999,
        ];

        $user->update([
            'plan' => $request->requested_plan,
            'voucher_limit' => $limits[$request->requested_plan],
        ]);

        $request->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('alert_success', 'Subscription approved for ' . $user->name);
    }

    public function reject($id)
    {
        $request = SubscriptionRequest::findOrFail($id);
        
        if ($request->status !== 'pending') {
            return back()->with('alert_error', 'This request has already been processed.');
        }
        
        $request->update(['status' => 'rejected']);

        return back()->with('alert_success', 'Request rejected');
    }
}
