@extends('layouts.app')

@section('title', 'Vouchers - Passtik')
@section('page-title')
    @if($selectedRouter)
        Vouchers - {{ $selectedRouter->name }}
    @else
        All Vouchers
    @endif
@endsection
@section('page-subtitle', 'Manage hotspot access vouchers')

@section('header-actions')
<div class="flex gap-2">
    <a href="{{ route('vouchers.reports') }}{{ $selectedRouter ? '?router=' . $selectedRouter->id : '' }}" 
       class="bg-purple-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-purple-700 transition-colors">
        📊 Reports
    </a>
    <a href="{{ route('vouchers.create') }}{{ $selectedRouter ? '?router=' . $selectedRouter->id : '' }}" 
       class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-xl font-semibold hover:shadow-lg transition-all">
        Create Voucher
    </a>
    @if($selectedRouter)
        <a href="{{ route('vouchers.print') }}?router={{ $selectedRouter->id }}&status=pending" target="_blank" 
           class="bg-green-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-green-700 transition-colors">
            Print Pending
        </a>
    @endif
</div>
@endsection

@section('content')
@if($selectedRouter)
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Statistics</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="p-4 bg-green-50 rounded-lg">
                <p class="text-green-600 text-sm font-medium">Total</p>
                <p class="text-2xl font-bold text-green-700">{{ $selectedRouter->vouchers->count() }}</p>
            </div>
            <div class="p-4 bg-yellow-50 rounded-lg">
                <p class="text-yellow-600 text-sm font-medium">Pending</p>
                <p class="text-2xl font-bold text-yellow-700">{{ $selectedRouter->vouchers->where('status', 'pending')->count() }}</p>
            </div>
            <div class="p-4 bg-blue-50 rounded-lg">
                <p class="text-blue-600 text-sm font-medium">Active</p>
                <p class="text-2xl font-bold text-blue-700">{{ $selectedRouter->vouchers->where('status', 'active')->count() }}</p>
            </div>
            <div class="p-4 bg-red-50 rounded-lg">
                <p class="text-red-600 text-sm font-medium">Used</p>
                <p class="text-2xl font-bold text-red-700">{{ $selectedRouter->vouchers->where('status', 'used')->count() }}</p>
            </div>
        </div>
    </div>
@endif

<div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        @if(request('router'))
            <input type="hidden" name="router" value="{{ request('router') }}">
        @endif
        
        <div class="flex-1 min-w-64">
            <label class="block text-sm font-medium text-gray-700 mb-1">Search Vouchers</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code..." 
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="used" {{ request('status') === 'used' ? 'selected' : '' }}>Used</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Search
            </button>
            <a href="{{ request()->url() }}{{ request('router') ? '?router=' . request('router') : '' }}" 
               class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                Clear
            </a>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-xl overflow-hidden">
    <!-- Bulk Actions Bar -->
    <div id="bulk-actions-bar" class="hidden bg-indigo-50 border-b border-indigo-200 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-sm font-semibold text-gray-700">
                    <span id="selected-count">0</span> selected
                </span>
                <button onclick="selectAll()" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Select All
                </button>
                <button onclick="deselectAll()" class="text-sm text-gray-600 hover:text-gray-800 font-medium">
                    Deselect All
                </button>
            </div>
            <div class="flex gap-2">
                <button onclick="bulkDelete()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm font-semibold">
                    🗑️ Delete Selected
                </button>
                <button onclick="bulkExport()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-semibold">
                    📥 Export Selected
                </button>
                <button onclick="bulkExpire()" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition text-sm font-semibold">
                    ⏰ Mark as Expired
                </button>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" id="select-all-checkbox" onchange="toggleSelectAll(this)" 
                               class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Router</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Duration</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Expires</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($vouchers as $voucher)
                    <tr class="hover:bg-gray-50 voucher-row">
                        <td class="px-6 py-4">
                            <input type="checkbox" class="voucher-checkbox w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" 
                                   value="{{ $voucher->id }}" data-status="{{ $voucher->status }}" onchange="updateBulkActions()">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-blue-600">
                            {{ $voucher->code }}
                            <div class="sm:hidden text-xs text-gray-500 mt-1">{{ $voucher->router->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 hidden sm:table-cell">{{ $voucher->router->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 hidden md:table-cell">{{ $voucher->getDurationFormatted() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'active' => 'bg-green-100 text-green-700',
                                    'used' => 'bg-purple-100 text-purple-700',
                                    'expired' => 'bg-red-100 text-red-700'
                                ];
                            @endphp
                            <span class="px-2 py-1 {{ $statusColors[$voucher->status] ?? 'bg-gray-100 text-gray-700' }} rounded text-xs font-bold uppercase">
                                {{ $voucher->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm hidden lg:table-cell">
                            {{ $voucher->expires_at ? $voucher->expires_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2">
                                <a href="{{ route('vouchers.show', $voucher) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                <form action="{{ route('vouchers.destroy', $voucher) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">No vouchers created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($vouchers->hasPages())
    <div class="mt-6 flex justify-center">
        <nav class="flex items-center space-x-1">
            @if($vouchers->onFirstPage())
                <span class="px-3 py-2 text-gray-400 bg-gray-100 rounded">Prev</span>
            @else
                <a href="{{ $vouchers->appends(request()->query())->previousPageUrl() }}" 
                   class="px-3 py-2 text-blue-600 bg-white border rounded hover:bg-blue-50">Prev</a>
            @endif
            
            <span class="px-3 py-2 text-gray-600">{{ $vouchers->currentPage() }} of {{ $vouchers->lastPage() }}</span>
            
            @if($vouchers->hasMorePages())
                <a href="{{ $vouchers->appends(request()->query())->nextPageUrl() }}" 
                   class="px-3 py-2 text-blue-600 bg-white border rounded hover:bg-blue-50">Next</a>
            @else
                <span class="px-3 py-2 text-gray-400 bg-gray-100 rounded">Next</span>
            @endif
        </nav>
    </div>
@endif

<script>
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.voucher-checkbox:checked');
    const count = checkboxes.length;
    const bulkBar = document.getElementById('bulk-actions-bar');
    const countSpan = document.getElementById('selected-count');
    
    countSpan.textContent = count;
    bulkBar.classList.toggle('hidden', count === 0);
    
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const allCheckboxes = document.querySelectorAll('.voucher-checkbox');
    selectAllCheckbox.checked = allCheckboxes.length > 0 && count === allCheckboxes.length;
}

function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.voucher-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateBulkActions();
}

function selectAll() {
    document.querySelectorAll('.voucher-checkbox').forEach(cb => cb.checked = true);
    updateBulkActions();
}

function deselectAll() {
    document.querySelectorAll('.voucher-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('select-all-checkbox').checked = false;
    updateBulkActions();
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.voucher-checkbox:checked')).map(cb => cb.value);
}

function bulkDelete() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    
    Swal.fire({
        title: 'Delete Vouchers?',
        text: `Are you sure you want to delete ${ids.length} voucher(s)? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Yes, delete them!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("vouchers.bulk.delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.error || 'Failed to delete vouchers', 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Network error occurred', 'error');
            });
        }
    });
}

function bulkExport() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    
    const url = '{{ route("vouchers.bulk.export") }}?ids=' + ids.join(',');
    window.open(url, '_blank');
}

function bulkExpire() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    
    Swal.fire({
        title: 'Mark as Expired?',
        text: `Mark ${ids.length} voucher(s) as expired?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ea580c',
        confirmButtonText: 'Yes, expire them!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("vouchers.bulk.expire") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Updated!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.error || 'Failed to update vouchers', 'error');
                }
            });
        }
    });
}
</script>
@endsection