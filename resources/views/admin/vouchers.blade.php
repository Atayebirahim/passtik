@extends('layouts.admin')

@section('content')
<div class="min-h-screen p-8">
    <div class="glass-effect rounded-2xl shadow-xl p-8 mb-8 animate-fadeInUp">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold gradient-text">{{ __('messages.all_vouchers') }}</h1>
                <p class="text-gray-600 mt-2">{{ __('messages.system_wide_voucher_overview') }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn-premium px-6 py-3 bg-gradient-to-r from-gray-700 to-gray-900 text-white rounded-xl font-semibold shadow-lg">{{ __('messages.back_to_dashboard') }}</a>
        </div>
    </div>

    <div class="glass-effect rounded-2xl shadow-xl overflow-hidden animate-fadeInUp" style="animation-delay: 0.2s">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="gradient-bg">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">{{ __('messages.code') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">{{ __('messages.router') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">{{ __('messages.owner') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">{{ __('messages.duration') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">{{ __('messages.status') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase">{{ __('messages.created_at') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white/50 backdrop-blur-sm divide-y divide-gray-200">
                    @forelse($vouchers as $voucher)
                    <tr class="hover:bg-white/70 transition-all">
                        <td class="px-6 py-4">
                            <div class="font-mono text-sm font-medium text-gray-900">{{ $voucher->code }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($voucher->router)
                                <div class="text-sm font-medium text-gray-900">{{ $voucher->router->name }}</div>
                                <div class="text-sm text-gray-500">{{ $voucher->router->ip_address }}</div>
                            @else
                                <span class="text-gray-400">{{ __('messages.no_data') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($voucher->router && $voucher->router->user)
                                <div class="text-sm text-gray-900">{{ $voucher->router->user->name }}</div>
                            @else
                                <span class="text-gray-400">{{ __('messages.no_data') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $voucher->duration }}</td>
                        <td class="px-6 py-4">
                            @if($voucher->status === 'active')
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-green-500 to-emerald-500 text-white">{{ __('messages.active') }}</span>
                            @elseif($voucher->status === 'used')
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 text-white">{{ __('messages.used') }}</span>
                            @elseif($voucher->status === 'expired')
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-red-500 to-pink-500 text-white">{{ __('messages.expired') }}</span>
                            @else
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ __('messages.' . $voucher->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $voucher->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">{{ __('messages.no_vouchers_found') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $vouchers->links() }}
        </div>
    </div>
</div>
@endsection
