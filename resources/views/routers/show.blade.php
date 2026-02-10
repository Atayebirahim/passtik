@extends('layouts.app')

@section('title', 'Router Setup - Passtik')
@section('page-title', 'Router VPN Setup')
@section('page-subtitle', 'Configure WireGuard VPN connection')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- VPN Status Card -->
    <div class="glass-effect rounded-2xl shadow-xl p-8 mb-6 animate-fadeInUp">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-3xl font-bold gradient-text">{{ $router->name }}</h2>
                <p class="text-gray-600 mt-1">WireGuard VPN Configuration</p>
            </div>
            <div>
                @if(isset($vpnStatus) && $vpnStatus['connected'])
                    @if($vpnStatus['active'])
                        <span class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-xl font-semibold shadow-lg flex items-center gap-2">
                            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                            {{ $vpnStatus['message'] }}
                        </span>
                    @else
                        <span class="px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-xl font-semibold shadow-lg">
                            {{ $vpnStatus['message'] }}
                        </span>
                    @endif
                @else
                    <span class="px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl font-semibold shadow-lg">
                        {{ $vpnStatus['message'] ?? 'VPN Not Connected' }}
                    </span>
                @endif
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white/50 backdrop-blur-sm rounded-xl p-4">
                <p class="text-sm text-gray-600 mb-1">Local IP</p>
                <p class="font-mono font-semibold text-gray-900">{{ $router->local_ip }}</p>
            </div>
            <div class="bg-white/50 backdrop-blur-sm rounded-xl p-4">
                <p class="text-sm text-gray-600 mb-1">VPN IP</p>
                <p class="font-mono font-semibold gradient-text">{{ $router->vpn_ip }}</p>
            </div>
            <div class="bg-white/50 backdrop-blur-sm rounded-xl p-4">
                <p class="text-sm text-gray-600 mb-1">VPS IP</p>
                <p class="font-mono font-semibold text-gray-900">10.0.0.1</p>
            </div>
        </div>
    </div>

    <!-- Setup Script Card -->
    <div class="glass-effect rounded-2xl shadow-xl p-8 mb-6 animate-fadeInUp" style="animation-delay: 0.1s">
        <h3 class="text-2xl font-bold gradient-text mb-4 flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
            </svg>
            MikroTik Setup Script
        </h3>
        
        <pre class="bg-gray-900 text-green-400 p-6 rounded-xl text-sm overflow-x-auto mb-4 font-mono" style="max-height: 400px;">{{ $script }}</pre>
        
        <div class="flex flex-wrap gap-3">
            <button onclick="copyScript()" class="btn-premium px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Copy Script
            </button>
            <a href="data:text/plain;charset=utf-8,{{ urlencode($script) }}" download="passtik-setup-{{ $router->id }}.rsc" class="btn-premium px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download .rsc
            </a>
            <button onclick="window.location.reload()" class="btn-premium px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Check Status
            </button>
        </div>
    </div>

    <!-- Instructions Card -->
    <div class="glass-effect rounded-2xl shadow-xl p-8 mb-6 animate-fadeInUp" style="animation-delay: 0.2s">
        <h3 class="text-2xl font-bold gradient-text mb-4 flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Setup Instructions
        </h3>
        
        <div class="space-y-4">
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 gradient-bg rounded-full flex items-center justify-center text-white font-bold">1</div>
                <div>
                    <p class="font-semibold text-gray-900">Download or Copy Script</p>
                    <p class="text-sm text-gray-600">Use the buttons above to get the configuration script</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 gradient-bg rounded-full flex items-center justify-center text-white font-bold">2</div>
                <div>
                    <p class="font-semibold text-gray-900">Open WinBox</p>
                    <p class="text-sm text-gray-600">Connect to your MikroTik router using WinBox</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 gradient-bg rounded-full flex items-center justify-center text-white font-bold">3</div>
                <div>
                    <p class="font-semibold text-gray-900">Run Script</p>
                    <p class="text-sm text-gray-600">Go to <span class="font-mono bg-gray-100 px-2 py-1 rounded">System → Scripts</span>, paste script, and click <span class="font-semibold">Run Script</span></p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 gradient-bg rounded-full flex items-center justify-center text-white font-bold">4</div>
                <div>
                    <p class="font-semibold text-gray-900">Verify Connection</p>
                    <p class="text-sm text-gray-600">Wait 10-15 seconds, then click "Check Status" button above</p>
                    <p class="text-xs text-gray-500 mt-1 font-mono">/interface wireguard peers print</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-3 animate-fadeInUp" style="animation-delay: 0.3s">
        <a href="{{ route('routers.index') }}" class="btn-premium px-6 py-3 bg-gradient-to-r from-gray-700 to-gray-900 text-white rounded-xl font-semibold shadow-lg">
            ← Back to Routers
        </a>
        <a href="{{ route('routers.manage', $router) }}" class="btn-premium px-6 py-3 gradient-bg text-white rounded-xl font-semibold shadow-lg">
            Manage Router →
        </a>
    </div>
</div>

<script>
function copyScript() {
    const script = @json($script);
    navigator.clipboard.writeText(script).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Script copied to clipboard',
            timer: 2000,
            showConfirmButton: false
        });
    }).catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Failed',
            text: 'Could not copy to clipboard',
            confirmButtonColor: '#6366f1'
        });
    });
}
</script>
@endsection
