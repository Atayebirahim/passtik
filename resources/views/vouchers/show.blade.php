@extends('layouts.app')

@section('title', 'Voucher Details - Passtik')
@section('page-title', 'Voucher Details')
@section('page-subtitle', 'View and print voucher information')

@section('header-actions')
<button onclick="printTicket()" 
        class="bg-green-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-green-700 transition-colors">
    Print Ticket
</button>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl p-8 mb-6" id="printable-ticket">
        <div class="text-center border-2 border-dashed border-gray-300 p-6 rounded-lg">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">WiFi Access Voucher</h2>
            <p class="text-gray-600 mb-4">{{ $voucher->router->name }}</p>
            
            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                <p class="text-sm text-gray-600">Access Code</p>
                <p class="text-3xl font-mono font-bold text-blue-600">{{ $voucher->code }}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Profile</p>
                    <p class="font-semibold">{{ $voucher->profile }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Status</p>
                    <span class="px-2 py-1 {{ $voucher->is_used ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }} rounded text-xs font-bold">
                        {{ $voucher->is_used ? 'USED' : 'ACTIVE' }}
                    </span>
                </div>
            </div>
            
            <p class="text-xs text-gray-400 mt-4">Generated: {{ $voucher->created_at->format('M d, Y H:i') }}</p>
        </div>
    </div>

    <div class="flex gap-4">
        <a href="{{ route('vouchers.edit', $voucher) }}" 
           class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700 transition-colors">
            Edit Voucher
        </a>
        <form action="{{ route('vouchers.destroy', $voucher) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="bg-red-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-red-700 transition-colors" 
                    onclick="return confirm('Are you sure?')">
                Delete Voucher
            </button>
        </form>
    </div>
</div>

<script>
function printTicket() {
    const printContent = document.getElementById('printable-ticket').innerHTML;
    const originalContent = document.body.innerHTML;
    
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
}
</script>
@endsection