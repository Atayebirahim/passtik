@extends('layouts.admin')

@section('content')
<div class="min-h-screen p-8">
    <div class="glass-effect rounded-2xl shadow-xl p-8 mb-8 animate-fadeInUp">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold gradient-text">{{ __('messages.subscription_requests') }}</h1>
                <p class="text-gray-600 mt-2">{{ __('messages.manage_upgrade_requests') }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn-premium px-6 py-3 bg-gradient-to-r from-gray-700 to-gray-900 text-white rounded-xl font-semibold shadow-lg">{{ __('messages.back_to_dashboard') }}</a>
        </div>
    </div>

    <div class="glass-effect rounded-2xl shadow-xl overflow-hidden animate-fadeInUp" style="animation-delay: 0.2s">
        <table class="min-w-full">
            <thead class="gradient-bg">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">{{ __('messages.user') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">{{ __('messages.current_plan') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">{{ __('messages.requested_plan') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">{{ __('messages.phone') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">{{ __('messages.status') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">{{ __('messages.date') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white/50 backdrop-blur-sm divide-y divide-gray-200">
                @forelse($requests as $request)
                <tr class="hover:bg-white/70 transition-all">
                    <td class="px-6 py-4">
                        <div class="font-medium">{{ $request->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $request->user->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100">{{ ucfirst($request->user->plan) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white">{{ ucfirst($request->requested_plan) }}</span>
                    </td>
                    <td class="px-6 py-4">{{ $request->phone }}</td>
                    <td class="px-6 py-4">
                        @if($request->status === 'pending')
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-yellow-500 to-orange-500 text-white">{{ __('messages.pending') }}</span>
                        @elseif($request->status === 'approved')
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-green-500 to-emerald-500 text-white">{{ __('messages.approved') }}</span>
                        @else
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-red-500 to-pink-500 text-white">{{ __('messages.rejected') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $request->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        @if($request->status === 'pending')
                            <form method="POST" action="{{ route('admin.subscription.approve', $request) }}" class="inline">
                                @csrf
                                <button type="submit" class="btn-premium px-5 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold shadow-lg mr-2">✓ {{ __('messages.approve') }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.subscription.reject', $request) }}" class="inline">
                                @csrf
                                <button type="submit" class="btn-premium px-5 py-2.5 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl font-semibold shadow-lg">✕ {{ __('messages.reject') }}</button>
                            </form>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">{{ __('messages.no_subscription_requests') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $requests->links() }}
    </div>
</div>
@endsection
