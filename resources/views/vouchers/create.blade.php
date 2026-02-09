@extends('layouts.app')

@section('title', 'Create Vouchers - Passtik')
@section('page-title', 'Create Vouchers')
@section('page-subtitle', 'Generate new hotspot vouchers')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <form action="{{ route('vouchers.store') }}" method="POST" class="space-y-6">
            @csrf
            
            @if($selectedRouter)
                <input type="hidden" name="router_id" value="{{ $selectedRouter->id }}">
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                    <p class="text-sm text-blue-700">Router: <strong>{{ $selectedRouter->name }}</strong></p>
                </div>
            @else
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Router</label>
                    <select name="router_id" id="router-select" required
                            class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200">
                        <option value="">Choose a router...</option>
                        @foreach($routers as $router)
                            <option value="{{ $router->id }}">{{ $router->name }} ({{ $router->local_ip }})</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Vouchers</label>
                    <input type="number" name="quantity" value="1" min="1" max="100" required
                           class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Code Length</label>
                    <select name="code_length" required
                            class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200">
                        <option value="4">4 characters</option>
                        <option value="5">5 characters</option>
                        <option value="6" selected>6 characters</option>
                        <option value="8">8 characters</option>
                        <option value="10">10 characters</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Voucher Type</label>
                    <select name="voucher_type" required
                            class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200">
                        <option value="numbers">Numbers Only (123456)</option>
                        <option value="mixed">Mixed Letters & Numbers (A1B2C3)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Authentication Type</label>
                    <select name="auth_type" required
                            class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200">
                        <option value="same">Username = Password</option>
                        <option value="different">Different Username & Password</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Duration (minutes)</label>
                    <select name="duration" required
                            class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200">
                        <option value="30">30 minutes</option>
                        <option value="60" selected>1 hour</option>
                        <option value="120">2 hours</option>
                        <option value="180">3 hours</option>
                        <option value="360">6 hours</option>
                        <option value="720">12 hours</option>
                        <option value="1440">1 day</option>
                        <option value="4320">3 days</option>
                        <option value="10080">7 days</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bandwidth</label>
                    <select name="bandwidth" required
                            class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200">
                        <option value="512k/512k">512 Kbps</option>
                        <option value="1M/1M" selected>1 Mbps</option>
                        <option value="2M/2M">2 Mbps</option>
                        <option value="5M/5M">5 Mbps</option>
                        <option value="10M/10M">10 Mbps</option>
                        <option value="20M/20M">20 Mbps</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Expires In (days)</label>
                    <select name="expires_in_days" required
                            class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200">
                        <option value="7">7 days</option>
                        <option value="15">15 days</option>
                        <option value="30" selected>30 days</option>
                        <option value="60">60 days</option>
                        <option value="90">90 days</option>
                        <option value="180">180 days</option>
                        <option value="365">1 year</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 px-6 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200">
                    Create Vouchers
                </button>
                <a href="{{ route('vouchers.index') }}{{ $selectedRouter ? '?router=' . $selectedRouter->id : '' }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection