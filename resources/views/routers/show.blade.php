@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Router Setup: {{ $router->name }}</h2>
            
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <p class="font-semibold text-green-800">✓ VPN Configuration Generated</p>
                <p class="text-green-700 text-sm mt-1">Download and run the script on your MikroTik router</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="font-semibold mb-2">Router Details</h3>
                    <div class="space-y-2 text-sm">
                        <p><span class="text-gray-600">Name:</span> {{ $router->name }}</p>
                        <p><span class="text-gray-600">Local IP:</span> {{ $router->local_ip }}</p>
                        <p><span class="text-gray-600">VPN IP:</span> <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $router->vpn_ip }}</span></p>
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold mb-2">VPN Status</h3>
                    <div class="space-y-2 text-sm">
                        <p><span class="text-gray-600">VPS IP:</span> <span class="font-mono bg-gray-100 px-2 py-1 rounded">10.0.0.1</span></p>
                        <p><span class="text-gray-600">API User:</span> {{ $router->api_user }}</p>
                        <p><span class="text-gray-600">Status:</span> <span class="text-yellow-600">⚠ Pending Setup</span></p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h3 class="font-semibold mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    MikroTik Setup Script
                </h3>
                <pre class="bg-gray-900 text-green-400 p-4 rounded text-xs overflow-x-auto mb-4" style="max-height: 400px;">{{ $script }}</pre>
                
                <div class="flex gap-3">
                    <button onclick="copyScript()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy Script
                    </button>
                    <a href="data:text/plain;charset=utf-8,{{ urlencode($script) }}" download="passtik-setup-{{ $router->id }}.rsc" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download .rsc
                    </a>
                </div>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <h4 class="font-semibold text-blue-800 mb-2">Setup Instructions</h4>
                <ol class="list-decimal list-inside space-y-2 text-sm text-blue-700">
                    <li>Download the script above or copy it</li>
                    <li>Open WinBox and connect to your MikroTik router</li>
                    <li>Go to <strong>System → Scripts</strong></li>
                    <li>Click <strong>Add New</strong>, paste the script, and click <strong>Run Script</strong></li>
                    <li>Wait 10-15 seconds for VPN to connect</li>
                    <li>Verify connection: <code class="bg-blue-100 px-1 rounded">/interface wireguard peers print</code></li>
                </ol>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('routers.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">Back to Routers</a>
                <a href="{{ route('routers.manage', $router) }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Manage Router</a>
            </div>
        </div>
    </div>
</div>

<script>
function copyScript() {
    const script = @json($script);
    navigator.clipboard.writeText(script).then(() => {
        alert('Script copied to clipboard!');
    });
}
</script>
@endsection
