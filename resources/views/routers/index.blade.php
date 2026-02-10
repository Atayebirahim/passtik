@extends('layouts.app')

@section('title', __('messages.routers') . ' - ' . __('messages.app_name'))
@section('page-title', __('messages.routers'))
@section('page-subtitle', __('messages.manage_router'))

@section('header-actions')
    <a href="{{ route('routers.create') }}" class="btn-premium bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg">
        <span class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            {{ __('messages.add_router') }}
        </span>
    </a>
@endsection

@section('content')
<div class="glass-effect rounded-2xl shadow-xl overflow-hidden animate-fadeInUp">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="gradient-bg">
                <tr>
                    <th class="px-6 py-4 {{ $isRtl ? 'text-right' : 'text-left' }} text-xs font-semibold text-white uppercase tracking-wider">{{ __('messages.router_name') }}</th>
                    <th class="px-6 py-4 {{ $isRtl ? 'text-right' : 'text-left' }} text-xs font-semibold text-white uppercase tracking-wider">{{ __('messages.status') }}</th>
                    <th class="px-6 py-4 {{ $isRtl ? 'text-right' : 'text-left' }} text-xs font-semibold text-white uppercase tracking-wider">{{ __('messages.local_ip') }}</th>
                    <th class="px-6 py-4 {{ $isRtl ? 'text-right' : 'text-left' }} text-xs font-semibold text-white uppercase tracking-wider">VPN IP</th>
                    <th class="px-6 py-4 {{ $isRtl ? 'text-right' : 'text-left' }} text-xs font-semibold text-white uppercase tracking-wider">{{ __('messages.api_user') }}</th>
                    <th class="px-6 py-4 {{ $isRtl ? 'text-left' : 'text-right' }} text-xs font-semibold text-white uppercase tracking-wider">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white/50 backdrop-blur-sm divide-y divide-gray-200">
                @forelse($routers as $router)
                    <tr class="hover:bg-white/70 transition-all">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $router->name }}</p>
                                    <p class="text-sm text-gray-500">MikroTik</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $vpnStatus = null;
                                if ($router->vpn_public_key) {
                                    try {
                                        $wg = new \App\Services\WireGuardService();
                                        $vpnStatus = $wg->checkPeerStatus($router->vpn_public_key);
                                    } catch (Exception $e) {
                                        $vpnStatus = ['connected' => false];
                                    }
                                }
                            @endphp
                            @if($vpnStatus && $vpnStatus['connected'])
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow-lg">
                                    <div class="w-2 h-2 bg-white rounded-full {{ $isRtl ? 'ml-2' : 'mr-2' }} animate-pulse"></div>
                                    {{ __('messages.connected') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-gray-500 to-gray-600 text-white shadow-lg">
                                    <div class="w-2 h-2 bg-white rounded-full {{ $isRtl ? 'ml-2' : 'mr-2' }}"></div>
                                    {{ __('messages.disconnected') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $router->local_ip }}</td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $router->vpn_ip }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $router->api_user }}</td>
                        <td class="px-6 py-4">
                            <div class="flex {{ $isRtl ? 'justify-start' : 'justify-end' }} gap-2">
                                <a href="{{ route('routers.show', $router) }}" class="btn-premium p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="{{ __('messages.view') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('routers.manage', $router) }}" class="btn-premium p-2 text-purple-600 hover:bg-purple-50 rounded-lg" title="{{ __('messages.manage_router') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('routers.edit', $router) }}" class="btn-premium p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg" title="{{ __('messages.edit') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('routers.destroy', $router) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-premium p-2 text-red-600 hover:bg-red-50 rounded-lg" onclick="return confirm('{{ __('messages.confirm') }}?')" title="{{ __('messages.delete') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-lg font-medium text-gray-900">{{ __('messages.no_data') }}</p>
                                    <p class="text-gray-500">{{ __('messages.add_router') }}</p>
                                </div>
                                <a href="{{ route('routers.create') }}" class="btn-premium bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg">
                                    {{ __('messages.add_router') }}
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
