@extends('layouts.app')

@section('title', __('messages.vouchers') . ' - ' . __('messages.app_name'))
@section('page-title')
    @if($selectedRouter)
        {{ __('messages.vouchers') }} - {{ $selectedRouter->name }}
    @else
        {{ __('messages.vouchers') }}
    @endif
@endsection
@section('page-subtitle', __('messages.manage_router'))

@section('header-actions')
<div class="flex gap-2">
    <a href="{{ route('vouchers.reports') }}{{ $selectedRouter ? '?router=' . $selectedRouter->id : '' }}" 
       class="bg-purple-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-purple-700 transition-colors">
        📊 {{ __('messages.reports') }}
    </a>
    <a href="{{ route('vouchers.create') }}{{ $selectedRouter ? '?router=' . $selectedRouter->id : '' }}" 
       class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-xl font-semibold hover:shadow-lg transition-all">
        {{ __('messages.create_vouchers') }}
    </a>
    @if($selectedRouter)
        <a href="{{ route('vouchers.print') }}?router={{ $selectedRouter->id }}&status=pending" target="_blank" 
           class="bg-green-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-green-700 transition-colors">
            {{ __('messages.print') }}
        </a>
    @endif
</div>
@endsection

@section('content')
@if($selectedRouter)
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">{{ __('messages.statistics') }}</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="p-4 bg-green-50 rounded-lg">
                <p class="text-green-600 text-sm font-medium">{{ __('messages.total') }}</p>
                <p class="text-2xl font-bold text-green-700">{{ $selectedRouter->vouchers->count() }}</p>
            </div>
            <div class="p-4 bg-yellow-50 rounded-lg">
                <p class="text-yellow-600 text-sm font-medium">{{ __('messages.pending') }}</p>
                <p class="text-2xl font-bold text-yellow-700">{{ $selectedRouter->vouchers->where('status', 'pending')->count() }}</p>
            </div>
            <div class="p-4 bg-blue-50 rounded-lg">
                <p class="text-blue-600 text-sm font-medium">{{ __('messages.active') }}</p>
                <p class="text-2xl font-bold text-blue-700">{{ $selectedRouter->vouchers->where('status', 'active')->count() }}</p>
            </div>
            <div class="p-4 bg-red-50 rounded-lg">
                <p class="text-red-600 text-sm font-medium">{{ __('messages.used') }}</p>
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
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.search') }}</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search') }}..." 
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.status') }}</label>
            <select name="status" class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">{{ __('messages.all') }}</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                <option value="used" {{ request('status') === 'used' ? 'selected' : '' }}>{{ __('messages.used') }}</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>{{ __('messages.expired') }}</option>
            </select>
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                {{ __('messages.search') }}
            </button>
            <a href="{{ request()->url() }}{{ request('router') ? '?router=' . request('router') : '' }}" 
               class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                {{ __('messages.cancel') }}
            </a>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 {{ $isRtl ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase">{{ __('messages.code') }}</th>
                    <th class="px-6 py-3 {{ $isRtl ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">{{ __('messages.router_name') }}</th>
                    <th class="px-6 py-3 {{ $isRtl ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase hidden md:table-cell">{{ __('messages.duration') }}</th>
                    <th class="px-6 py-3 {{ $isRtl ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase">{{ __('messages.status') }}</th>
                    <th class="px-6 py-3 {{ $isRtl ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">{{ __('messages.expires_at') }}</th>
                    <th class="px-6 py-3 {{ $isRtl ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 uppercase">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($vouchers as $voucher)
                    <tr class="hover:bg-gray-50">
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
                                {{ __('messages.' . $voucher->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm hidden lg:table-cell">
                            {{ $voucher->expires_at ? $voucher->expires_at->diffForHumans() : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2">
                                <a href="{{ route('vouchers.show', $voucher) }}" class="text-blue-600 hover:text-blue-900">{{ __('messages.view') }}</a>
                                <form action="{{ route('vouchers.destroy', $voucher) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('messages.confirm') }}?')">{{ __('messages.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">{{ __('messages.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($vouchers->hasPages())
    <div class="mt-6 flex justify-center">
        {{ $vouchers->appends(request()->query())->links() }}
    </div>
@endif
@endsection
