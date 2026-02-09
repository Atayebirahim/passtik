@extends('layouts.app')

@section('title', 'Voucher Reports - Passtik')
@section('page-title', 'Voucher Reports & Analytics')
@section('page-subtitle', 'Comprehensive voucher usage statistics and insights')

@section('header-actions')
<div class="flex gap-3">
    <select onchange="window.location.href='?router='+this.value" 
            class="px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-indigo-600 focus:outline-none">
        <option value="">All Routers</option>
        @foreach($routers as $router)
            <option value="{{ $router->id }}" {{ $selectedRouter && $selectedRouter->id == $router->id ? 'selected' : '' }}>
                {{ $router->name }}
            </option>
        @endforeach
    </select>
    <a href="{{ route('vouchers.index', ['router' => $selectedRouter->id ?? '']) }}" 
       class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-xl font-semibold hover:shadow-lg transition-all">
        Back to Vouchers
    </a>
</div>
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-xl p-6 border-t-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Vouchers</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $totalVouchers }}</h3>
            </div>
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-6 border-t-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Pending</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $pendingVouchers }}</h3>
            </div>
            <div class="p-3 bg-yellow-50 text-yellow-600 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-6 border-t-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Active</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $activeVouchers }}</h3>
            </div>
            <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-6 border-t-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Used</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $usedVouchers }}</h3>
            </div>
            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-6 border-t-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Expired</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $expiredVouchers }}</h3>
            </div>
            <div class="p-3 bg-red-50 text-red-600 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Redemptions Chart -->
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Redemptions (Last 30 Days)</h2>
        <canvas id="redemptionsChart"></canvas>
    </div>

    <!-- Status Distribution -->
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Voucher Status Distribution</h2>
        <canvas id="statusChart"></canvas>
    </div>
</div>

<!-- Recent Redemptions -->
<div class="bg-white rounded-2xl shadow-xl p-8">
    <h2 class="text-xl font-bold text-gray-900 mb-6">Recent Redemptions</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b-2 border-gray-200">
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Voucher Code</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Router</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">IP Address</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Device</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentRedemptions as $redemption)
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-3 px-4 font-mono font-bold">{{ $redemption->voucher->code }}</td>
                    <td class="py-3 px-4">{{ $redemption->voucher->router->name }}</td>
                    <td class="py-3 px-4 font-mono text-sm">{{ $redemption->ip_address }}</td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 bg-gray-100 rounded-lg text-xs">
                            {{ ucfirst($redemption->device_type) }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        @if($redemption->status === 'success')
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Success</span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Failed</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-600">{{ $redemption->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-500">No redemptions yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
// Redemptions Chart
const redemptionsCtx = document.getElementById('redemptionsChart').getContext('2d');
new Chart(redemptionsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($redemptionsByDay->pluck('date')) !!},
        datasets: [{
            label: 'Redemptions',
            data: {!! json_encode($redemptionsByDay->pluck('count')) !!},
            borderColor: 'rgb(99, 102, 241)',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Active', 'Used', 'Expired'],
        datasets: [{
            data: [{{ $pendingVouchers }}, {{ $activeVouchers }}, {{ $usedVouchers }}, {{ $expiredVouchers }}],
            backgroundColor: [
                'rgb(234, 179, 8)',
                'rgb(34, 197, 94)',
                'rgb(168, 85, 247)',
                'rgb(239, 68, 68)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});
</script>
@endsection
