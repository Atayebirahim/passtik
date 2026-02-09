@extends('layouts.app')

@section('title', 'Router Dashboard - Passtik')
@section('page-title', $router->name ?? 'Router Dashboard')
@section('page-subtitle', 'Monitor and manage your MikroTik router')

@section('header-actions')
<a href="{{ route('routers.show', $router) }}" 
   class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-xl font-semibold hover:shadow-lg transition-all">
    Back to Details
</a>
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if(isset($connectionError))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl flex items-center gap-4 text-red-700">
        <svg class="w-6 h-6 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        <p class="font-medium">Router connection failed: {{ $connectionError }}</p>
    </div>
@endif

@if($info)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-shadow">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Model</p>
                    <h3 class="text-lg font-bold text-gray-900">{{ $info['board-name'] ?? 'Unknown' }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-shadow">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Uptime</p>
                    <h3 class="text-lg font-bold text-gray-900">{{ $info['uptime'] ?? 'Unknown' }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-shadow">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-orange-50 text-orange-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">CPU Load</p>
                    <h3 class="text-lg font-bold text-gray-900">{{ $info['cpu-load'] ?? '0' }}%</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-shadow">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Version</p>
                    <h3 class="text-lg font-bold text-gray-900">{{ $info['version'] ?? 'Unknown' }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
        <!-- CPU Usage Chart -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">CPU Usage</h2>
                    <p class="text-sm text-gray-500">Real-time processor load</p>
                </div>
                <span class="text-2xl font-bold text-orange-600">{{ $info['cpu-load'] ?? '0' }}%</span>
            </div>
            <div class="relative h-64">
                <canvas id="cpuChart"></canvas>
            </div>
        </div>

        <!-- Memory Usage Chart -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Memory Usage</h2>
                    <p class="text-sm text-gray-500">RAM allocation breakdown</p>
                </div>
                @php
                    $totalMem = (int)($info['total-memory'] ?? 0);
                    $freeMem = (int)($info['free-memory'] ?? 0);
                    $usedPercent = $totalMem > 0 ? round((($totalMem - $freeMem) / $totalMem) * 100) : 0;
                @endphp
                <span class="text-2xl font-bold text-blue-600">{{ $usedPercent }}%</span>
            </div>
            <div class="relative h-64">
                <canvas id="memoryChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Network Traffic Chart -->
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-xl p-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Network Traffic</h2>
                    <p class="text-sm text-gray-500">Real-time data flow monitoring</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Download</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Upload</span>
                    </div>
                </div>
            </div>
            <div class="relative h-80">
                <canvas id="networkChart"></canvas>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-xl p-8 text-center border-t-4 border-green-500">
                <div class="w-16 h-16 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Vouchers</h3>
                <p class="text-sm text-gray-500 mb-6">Generate and manage hotspot access vouchers</p>
                <a href="{{ route('vouchers.index') }}?router={{ $router->id }}" 
                   class="block w-full py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition-colors">
                    Manage Vouchers
                </a>
            </div>
        </div>
    </div>
@endif

<script>
@if($info)
const cpuUsage = {{ $info['cpu-load'] ?? 0 }};
const memoryUsed = {{ $usedPercent }};
const memoryFree = {{ 100 - $usedPercent }};

// CPU Usage Doughnut Chart
const cpuCtx = document.getElementById('cpuChart').getContext('2d');
new Chart(cpuCtx, {
    type: 'doughnut',
    data: {
        labels: ['Used', 'Available'],
        datasets: [{
            data: [cpuUsage, 100 - cpuUsage],
            backgroundColor: ['#f97316', '#f3f4f6'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            }
        },
        cutout: '70%'
    }
});

// Memory Usage Doughnut Chart
const memoryCtx = document.getElementById('memoryChart').getContext('2d');
new Chart(memoryCtx, {
    type: 'doughnut',
    data: {
        labels: ['Used', 'Free'],
        datasets: [{
            data: [memoryUsed, memoryFree],
            backgroundColor: ['#3b82f6', '#f3f4f6'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            }
        },
        cutout: '70%'
    }
});

// Network Traffic Line Chart
const networkCtx = document.getElementById('networkChart').getContext('2d');
const timeLabels = [];
const downloadData = [];
const uploadData = [];

// Initialize with empty data
for (let i = 19; i >= 0; i--) {
    const time = new Date();
    time.setSeconds(time.getSeconds() - i * 3);
    timeLabels.push(time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' }));
    downloadData.push(0);
    uploadData.push(0);
}

const networkChart = new Chart(networkCtx, {
    type: 'line',
    data: {
        labels: timeLabels,
        datasets: [{
            label: 'Download (Mbps)',
            data: downloadData,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            fill: true,
            tension: 0.4
        }, {
            label: 'Upload (Mbps)',
            data: uploadData,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 20
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: '#f3f4f6'
                },
                ticks: {
                    callback: function(value) {
                        return value + ' Mbps';
                    }
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        elements: {
            point: {
                radius: 0,
                hoverRadius: 6
            }
        }
    }
});

// Update network traffic data every 3 seconds with performance optimizations
let isUpdating = false;
let updateInterval;

function updateNetworkTraffic() {
    if (isUpdating) return;
    isUpdating = true;
    
    fetch('{{ route('routers.traffic', $router) }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.length > 0) {
            // Sum all interfaces traffic
            let totalRx = 0;
            let totalTx = 0;
            
            data.data.forEach(interface => {
                totalRx += parseInt(interface.rx_bits_per_second) || 0;
                totalTx += parseInt(interface.tx_bits_per_second) || 0;
            });
            
            // Convert to Mbps
            const rxMbps = (totalRx / 1000000).toFixed(2);
            const txMbps = (totalTx / 1000000).toFixed(2);
            
            // Update chart data
            const now = new Date();
            networkChart.data.labels.push(now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' }));
            networkChart.data.datasets[0].data.push(parseFloat(rxMbps));
            networkChart.data.datasets[1].data.push(parseFloat(txMbps));
            
            // Keep only last 20 data points
            if (networkChart.data.labels.length > 20) {
                networkChart.data.labels.shift();
                networkChart.data.datasets[0].data.shift();
                networkChart.data.datasets[1].data.shift();
            }
            
            networkChart.update('none');
        }
    })
    .catch(error => {
        console.warn('Network traffic update failed:', error);
        // Reduce frequency on errors
        clearInterval(updateInterval);
        updateInterval = setInterval(updateNetworkTraffic, 10000); // 10 seconds on error
    })
    .finally(() => {
        isUpdating = false;
    });
}

// Start updates
updateInterval = setInterval(updateNetworkTraffic, 3000);

// Pause updates when tab is not visible
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        clearInterval(updateInterval);
    } else {
        updateInterval = setInterval(updateNetworkTraffic, 3000);
    }
});
@endif
</script>
@endsection