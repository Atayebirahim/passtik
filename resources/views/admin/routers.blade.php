@extends('layouts.admin')

@section('content')
<div class="min-h-screen p-8">
    <div class="glass-effect rounded-2xl shadow-xl p-8 mb-8 animate-fadeInUp">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold gradient-text">All Routers</h1>
                <p class="text-gray-600 mt-2">Manage all MikroTik routers in the system</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn-premium px-6 py-3 bg-gradient-to-r from-gray-700 to-gray-900 text-white rounded-xl font-semibold shadow-lg">Back to Dashboard</a>
        </div>
    </div>

    <div class="glass-effect rounded-2xl shadow-xl overflow-hidden animate-fadeInUp" style="animation-delay: 0.2s">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="gradient-bg">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">Router</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">Owner</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">IP Address</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">VPN</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">Created</th>
                </tr>
            </thead>
            <tbody class="bg-white/50 backdrop-blur-sm divide-y divide-gray-200">
                    @forelse($routers as $router)
                    <tr class="hover:bg-white/70 transition-all">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $router->name }}</div>
                            <div class="text-sm text-gray-500">{{ $router->location ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($router->user)
                                <div class="text-sm font-medium text-gray-900">{{ $router->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $router->user->email }}</div>
                            @else
                                <span class="text-gray-400">No owner</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $router->ip_address }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $router->status === 'connected' ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white' : 'bg-gradient-to-r from-red-500 to-pink-500 text-white' }}">
                                {{ ucfirst($router->status ?? 'unknown') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($router->vpn_public_key)
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 text-white">Enabled</span>
                            @else
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Disabled</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $router->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No routers found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $routers->links() }}
        </div>
    </div>
</div>
@endsection
