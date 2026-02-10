@extends('layouts.admin')

@section('content')
<div class="min-h-screen p-8">
    <!-- Header -->
    <div class="glass-effect rounded-2xl shadow-xl p-8 mb-8 animate-fadeInUp">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold gradient-text">{{ __('messages.admin_dashboard') }}</h1>
                <p class="text-gray-600 mt-2">{{ __('messages.welcome') }}, {{ auth()->user()->name }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.users') }}" class="btn-premium px-6 py-3 gradient-bg text-white rounded-xl font-semibold shadow-lg">{{ __('messages.users') }}</a>
                <a href="{{ route('admin.routers') }}" class="btn-premium px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold shadow-lg">{{ __('messages.routers') }}</a>
                <a href="{{ route('admin.vouchers') }}" class="btn-premium px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold shadow-lg">{{ __('messages.vouchers') }}</a>
                <a href="{{ route('admin.subscriptions') }}" class="btn-premium px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-600 text-white rounded-xl font-semibold shadow-lg">{{ __('messages.subscription') }}</a>
                <a href="{{ route('admin.settings') }}" class="btn-premium px-6 py-3 bg-gradient-to-r from-gray-700 to-gray-900 text-white rounded-xl font-semibold shadow-lg">{{ __('messages.settings') }}</a>
            </div>
        </div>
    </div>
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass-effect rounded-2xl shadow-lg p-6 border-l-4 border-blue-500 card-hover animate-fadeInUp" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">{{ __('messages.total') }} {{ __('messages.users') }}</p>
                    <p class="text-3xl font-bold gradient-text mt-2">{{ number_format($stats['total_users']) }}</p>
                    <p class="text-sm text-green-600 mt-2">+{{ $stats['new_users_today'] }} {{ __('messages.day') }}</p>
                </div>
                <div class="gradient-bg rounded-2xl p-4 animate-float">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl shadow-lg p-6 border-l-4 border-green-500 card-hover animate-fadeInUp" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">{{ __('messages.total') }} {{ __('messages.vouchers') }}</p>
                    <p class="text-3xl font-bold gradient-text mt-2">{{ number_format($stats['total_vouchers']) }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ $stats['active_vouchers'] }} {{ __('messages.active') }}</p>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-4 animate-float" style="animation-delay: 0.5s">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl shadow-lg p-6 border-l-4 border-purple-500 card-hover animate-fadeInUp" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">{{ __('messages.total') }} {{ __('messages.routers') }}</p>
                    <p class="text-3xl font-bold gradient-text mt-2">{{ number_format($stats['total_routers']) }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ $stats['active_routers'] ?? 0 }} {{ __('messages.active') }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl p-4 animate-float" style="animation-delay: 1s">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl shadow-lg p-6 border-l-4 border-yellow-500 card-hover animate-fadeInUp" style="animation-delay: 0.4s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">{{ __('messages.month') }}</p>
                    <p class="text-3xl font-bold gradient-text mt-2">${{ number_format($stats['revenue_this_month']) }}</p>
                    <p class="text-sm text-yellow-600 mt-2">{{ $stats['pending_subscriptions'] }} {{ __('messages.pending') }}</p>
                </div>
                <div class="bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl p-4 animate-float" style="animation-delay: 1.5s">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="gradient-bg rounded-2xl p-6 text-white shadow-lg card-hover animate-fadeInUp" style="animation-delay: 0.5s">
            <p class="text-sm opacity-90">{{ __('messages.users') }} ({{ __('messages.week') }})</p>
            <p class="text-3xl font-bold mt-2">{{ $stats['new_users_week'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-6 text-white shadow-lg card-hover animate-fadeInUp" style="animation-delay: 0.6s">
            <p class="text-sm opacity-90">{{ __('messages.vouchers') }}</p>
            <p class="text-3xl font-bold mt-2">{{ number_format($stats['redeemed_vouchers']) }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-6 text-white shadow-lg card-hover animate-fadeInUp" style="animation-delay: 0.7s">
            <p class="text-sm opacity-90">{{ __('messages.subscription') }}</p>
            <p class="text-3xl font-bold mt-2">{{ $stats['approved_subscriptions'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg card-hover animate-fadeInUp" style="animation-delay: 0.8s">
            <p class="text-sm opacity-90">{{ __('messages.total') }}</p>
            <p class="text-3xl font-bold mt-2">${{ number_format($stats['revenue_total']) }}</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- User Growth Chart -->
        <div class="glass-effect rounded-2xl shadow-lg p-6 card-hover animate-fadeInUp" style="animation-delay: 0.9s">
            <h3 class="text-lg font-semibold gradient-text mb-4">{{ __('messages.users') }} (30 {{ __('messages.days') }})</h3>
            <div class="h-64 flex items-end justify-between space-x-2">
                @foreach($user_growth as $day)
                <div class="flex-1 bg-gradient-to-t from-indigo-500 to-purple-500 rounded-t hover:from-indigo-600 hover:to-purple-600 transition relative group" 
                     style="height: {{ $day->count > 0 ? ($day->count / $user_growth->max('count') * 100) : 5 }}%">
                    <div class="absolute bottom-full mb-2 hidden group-hover:block bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($day->date)->format('M d') }}: {{ $day->count }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Plan Distribution -->
        <div class="glass-effect rounded-2xl shadow-lg p-6 card-hover animate-fadeInUp" style="animation-delay: 1s">
            <h3 class="text-lg font-semibold gradient-text mb-4">{{ __('messages.plan') }}</h3>
            <div class="space-y-4">
                @foreach($plan_distribution as $plan)
                @php
                    $percentage = ($plan->count / $stats['total_users']) * 100;
                    $colors = ['free' => 'bg-gray-500', 'pro' => 'bg-blue-500', 'enterprise' => 'bg-purple-500'];
                @endphp
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ ucfirst($plan->plan) }}</span>
                        <span class="text-sm text-gray-600">{{ $plan->count }} ({{ number_format($percentage, 1) }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="{{ $colors[$plan->plan] ?? 'bg-gray-500' }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="glass-effect rounded-2xl shadow-lg p-6 card-hover animate-fadeInUp" style="animation-delay: 1.1s">
            <h3 class="text-lg font-semibold gradient-text mb-4">{{ __('messages.users') }}</h3>
            <div class="space-y-3">
                @foreach($recent_users as $user)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    </div>
                    <div class="{{ $isRtl ? 'text-left' : 'text-right' }}">
                        <span class="px-2 py-1 text-xs rounded {{ $user->plan === 'enterprise' ? 'bg-purple-100 text-purple-800' : ($user->plan === 'pro' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($user->plan) }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Subscription Requests -->
        <div class="glass-effect rounded-2xl shadow-lg p-6 card-hover animate-fadeInUp" style="animation-delay: 1.2s">
            <h3 class="text-lg font-semibold gradient-text mb-4">{{ __('messages.subscription') }}</h3>
            <div class="space-y-3">
                @foreach($recent_subscriptions as $request)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">{{ $request->user->name }}</p>
                        <p class="text-sm text-gray-600">{{ ucfirst($request->requested_plan) }}</p>
                    </div>
                    <div class="{{ $isRtl ? 'text-left' : 'text-right' }}">
                        <span class="px-2 py-1 text-xs rounded {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($request->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                            {{ __('messages.' . $request->status) }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">{{ $request->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
