@extends('layouts.app')

@section('title', 'Edit Voucher - Passtik')
@section('page-title', 'Edit Voucher')
@section('page-subtitle', 'Update voucher information')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <form action="{{ route('vouchers.update', $voucher) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Voucher Code</label>
                <input type="text" value="{{ $voucher->code }}" readonly
                       class="w-full px-4 py-3 bg-gray-100 border-0 rounded-xl text-gray-600 font-mono">
                <p class="text-xs text-gray-500 mt-1">Code cannot be changed</p>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Select Router</label>
                <select name="router_id" required
                        class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200">
                    @foreach($routers as $router)
                        <option value="{{ $router->id }}" {{ $voucher->router_id == $router->id ? 'selected' : '' }}>
                            {{ $router->name }} ({{ $router->local_ip }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Profile</label>
                <input type="text" name="profile" value="{{ $voucher->profile }}" required
                       class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_used" value="1" {{ $voucher->is_used ? 'checked' : '' }} 
                       class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500">
                <label class="ml-2 text-sm font-medium text-gray-700">Mark as Used</label>
            </div>

            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 px-6 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200">
                    Update Voucher
                </button>
                <a href="{{ route('vouchers.show', $voucher) }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection