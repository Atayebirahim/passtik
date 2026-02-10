@extends('layouts.admin')

@section('content')
<div class="min-h-screen p-8">
    <!-- Header -->
    <div class="glass-effect rounded-2xl shadow-xl p-8 mb-8 animate-fadeInUp">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-6">
                <div class="h-20 w-20 gradient-bg rounded-full flex items-center justify-center shadow-xl">
                    <span class="text-white font-bold text-3xl">{{ substr($user->name, 0, 1) }}</span>
                </div>
                <div>
                    <h1 class="text-4xl font-bold gradient-text">{{ $user->name }}</h1>
                    <p class="text-gray-600 mt-1">{{ $user->email }}</p>
                    <div class="flex items-center space-x-3 mt-2">
                        @if($user->is_admin)
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-red-500 to-pink-500 text-white shadow-lg">Admin</span>
                        @endif
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $user->plan === 'enterprise' ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white' : ($user->plan === 'pro' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($user->plan) }} Plan
                        </span>
                        @if($user->email_verified_at)
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-green-500 to-emerald-500 text-white">Verified</span>
                        @else
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-yellow-500 to-orange-500 text-white">Unverified</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn-premium px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold shadow-lg">Edit User</a>
                <a href="{{ route('admin.users') }}" class="btn-premium px-6 py-3 bg-gradient-to-r from-gray-700 to-gray-900 text-white rounded-xl font-semibold shadow-lg">Back</a>
            </div>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass-effect rounded-2xl shadow-lg p-6 card-hover animate-fadeInUp" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Routers</p>
                    <p class="text-3xl font-bold gradient-text mt-2">{{ number_format($stats['total_routers']) }}</p>
                    <p class="text-sm text-green-600 mt-2">{{ $stats['active_routers'] }} active</p>
                </div>
                <div class="gradient-bg rounded-2xl p-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl shadow-lg p-6 card-hover animate-fadeInUp" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Vouchers</p>
                    <p class="text-3xl font-bold gradient-text mt-2">{{ number_format($stats['total_vouchers']) }}</p>
                    <p class="text-sm text-gray-600 mt-2">Limit: {{ $user->voucher_limit }}</p>
                </div>
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl shadow-lg p-6 card-hover animate-fadeInUp" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Active Vouchers</p>
                    <p class="text-3xl font-bold gradient-text mt-2">{{ number_format($stats['active_vouchers']) }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ $stats['used_vouchers'] }} used</p>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl shadow-lg p-6 card-hover animate-fadeInUp" style="animation-delay: 0.4s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Expired Vouchers</p>
                    <p class="text-3xl font-bold gradient-text mt-2">{{ number_format($stats['expired_vouchers']) }}</p>
                    <p class="text-sm text-gray-600 mt-2">No longer valid</p>
                </div>
                <div class="bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl p-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information -->
    <div class="glass-effect rounded-2xl shadow-xl p-8 mb-8 animate-fadeInUp" style="animation-delay: 0.5s">
        <h2 class="text-2xl font-bold gradient-text mb-6">Account Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white/50 rounded-xl p-6">
                <p class="text-sm text-gray-600 font-medium mb-2">Member Since</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->created_at->format('F d, Y') }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
            </div>
            <div class="bg-white/50 rounded-xl p-6">
                <p class="text-sm text-gray-600 font-medium mb-2">Email Verified</p>
                <p class="text-lg font-semibold text-gray-900">
                    @if($user->email_verified_at)
                        {{ $user->email_verified_at->format('F d, Y') }}
                    @else
                        <span class="text-orange-600">Not Verified</span>
                    @endif
                </p>
                @if($user->email_verified_at)
                    <p class="text-sm text-gray-500 mt-1">{{ $user->email_verified_at->diffForHumans() }}</p>
                @endif
            </div>
            <div class="bg-white/50 rounded-xl p-6">
                <p class="text-sm text-gray-600 font-medium mb-2">Current Plan</p>
                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($user->plan) }}</p>
                <p class="text-sm text-gray-500 mt-1">Voucher Limit: {{ $user->voucher_limit }}</p>
            </div>
            <div class="bg-white/50 rounded-xl p-6">
                <p class="text-sm text-gray-600 font-medium mb-2">Last Updated</p>
                <p class="text-lg font-semibold text-gray-900">{{ $user->updated_at->format('F d, Y') }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $user->updated_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>

    <!-- Recent Routers -->
    <div class="glass-effect rounded-2xl shadow-xl p-8 mb-8 animate-fadeInUp" style="animation-delay: 0.6s">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold gradient-text">Recent Routers</h2>
            @if($stats['total_routers'] > 5)
                <span class="text-sm text-gray-600">Showing 5 of {{ $stats['total_routers'] }}</span>
            @endif
        </div>
        @if($recent_routers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="gradient-bg">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">VPN IP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Local IP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Vouchers</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Created</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/50 backdrop-blur-sm divide-y divide-gray-200">
                        @foreach($recent_routers as $router)
                        <tr class="hover:bg-white/70 transition-all">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $router->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $router->vpn_ip ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $router->local_ip ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $router->status === 'connected' ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($router->status ?? 'unknown') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $router->vouchers->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $router->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                </svg>
                <p class="mt-4 text-gray-600">No routers yet</p>
            </div>
        @endif
    </div>

    <!-- Recent Vouchers -->
    <div class="glass-effect rounded-2xl shadow-xl p-8 animate-fadeInUp" style="animation-delay: 0.7s">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold gradient-text">Recent Vouchers</h2>
            @if($stats['total_vouchers'] > 10)
                <span class="text-sm text-gray-600">Showing 10 of {{ $stats['total_vouchers'] }}</span>
            @endif
        </div>
        @if($recent_vouchers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="gradient-bg">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Router</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Profile</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Created</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/50 backdrop-blur-sm divide-y divide-gray-200">
                        @foreach($recent_vouchers as $voucher)
                        <tr class="hover:bg-white/70 transition-all">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono font-medium text-gray-900">{{ $voucher->code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $voucher->router->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $voucher->profile }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $voucher->getDurationFormatted() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-medium rounded-full 
                                    {{ $voucher->status === 'active' ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white' : 
                                       ($voucher->status === 'used' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white' : 
                                       ($voucher->status === 'expired' ? 'bg-gradient-to-r from-red-500 to-pink-500 text-white' : 
                                       'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst($voucher->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $voucher->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
                <p class="mt-4 text-gray-600">No vouchers yet</p>
            </div>
        @endif
    </div>
</div>
@endsection
