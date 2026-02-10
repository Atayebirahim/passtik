@extends('layouts.admin')

@section('content')
<div class="min-h-screen p-8">
    <!-- Header -->
    <div class="glass-effect rounded-2xl shadow-xl p-8 mb-8 animate-fadeInUp">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold gradient-text">User Management</h1>
                <p class="text-gray-600 mt-2">Manage all users and their permissions</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn-premium px-6 py-3 bg-gradient-to-r from-gray-700 to-gray-900 text-white rounded-xl font-semibold shadow-lg">Back to Dashboard</a>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass-effect rounded-2xl shadow-lg p-6 card-hover animate-fadeInUp" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Users</p>
                    <p class="text-3xl font-bold gradient-text mt-2">{{ number_format($stats['total_users']) }}</p>
                    <p class="text-sm text-green-600 mt-2">+{{ $stats['users_today'] }} today</p>
                </div>
                <div class="gradient-bg rounded-2xl p-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl shadow-lg p-6 card-hover animate-fadeInUp" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Active Users</p>
                    <p class="text-3xl font-bold gradient-text mt-2">{{ number_format($stats['active_users']) }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ $stats['unverified_users'] }} unverified</p>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl shadow-lg p-6 card-hover animate-fadeInUp" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">This Week</p>
                    <p class="text-3xl font-bold gradient-text mt-2">{{ number_format($stats['users_this_week']) }}</p>
                    <p class="text-sm text-gray-600 mt-2">New signups</p>
                </div>
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl shadow-lg p-6 card-hover animate-fadeInUp" style="animation-delay: 0.4s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Admin Users</p>
                    <p class="text-3xl font-bold gradient-text mt-2">{{ number_format($stats['admin_users']) }}</p>
                    <p class="text-sm text-gray-600 mt-2">With admin access</p>
                </div>
                <div class="bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl p-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Plan Distribution -->
    <div class="glass-effect rounded-2xl shadow-xl p-6 mb-8 card-hover animate-fadeInUp" style="animation-delay: 0.5s">
        <h3 class="text-xl font-semibold gradient-text mb-4">Plan Distribution</h3>
        <div class="grid grid-cols-3 gap-4">
            <div class="text-center p-4 bg-white/50 rounded-xl">
                <p class="text-3xl font-bold text-gray-900">{{ $stats['free_plan'] }}</p>
                <p class="text-sm text-gray-600 mt-1">Free Plan</p>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl">
                <p class="text-3xl font-bold gradient-text">{{ $stats['pro_plan'] }}</p>
                <p class="text-sm text-gray-600 mt-1">Pro Plan</p>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl">
                <p class="text-3xl font-bold gradient-text">{{ $stats['enterprise_plan'] }}</p>
                <p class="text-sm text-gray-600 mt-1">Enterprise Plan</p>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="glass-effect rounded-2xl shadow-xl overflow-hidden animate-fadeInUp" style="animation-delay: 0.6s">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="gradient-bg">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">User</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Plan</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Routers</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Vouchers</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white/50 backdrop-blur-sm divide-y divide-gray-200">
                @foreach($users as $user)
                <tr class="hover:bg-white/70 transition-all">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12 gradient-bg rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-white font-semibold text-lg">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $user->plan === 'enterprise' ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white' : ($user->plan === 'pro' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($user->plan) }}
                        </span>
                        <div class="text-xs text-gray-500 mt-1">Limit: {{ $user->voucher_limit }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $user->routers_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $user->vouchers_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->is_admin)
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-red-500 to-pink-500 text-white shadow-lg">Admin</span>
                        @elseif($user->email_verified_at)
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-green-500 to-emerald-500 text-white">Verified</span>
                        @else
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-yellow-500 to-orange-500 text-white">Unverified</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn-premium text-blue-600 hover:text-blue-900 font-semibold px-4 py-2 rounded-lg hover:bg-blue-50 transition-all">
                            View
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-premium text-indigo-600 hover:text-indigo-900 font-semibold px-4 py-2 rounded-lg hover:bg-indigo-50 transition-all">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="inline">
                            @csrf
                            <button type="submit" class="btn-premium text-purple-600 hover:text-purple-900 font-semibold px-4 py-2 rounded-lg hover:bg-purple-50 transition-all">
                                {{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}
                            </button>
                        </form>
                        @if((!$user->is_admin || \App\Models\User::where('is_admin', true)->count() > 1) && $user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="inline" onsubmit="return confirm('Delete {{ $user->name }}? This will also delete all their routers and vouchers.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-premium text-red-600 hover:text-red-900 font-semibold px-4 py-2 rounded-lg hover:bg-red-50 transition-all">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection
