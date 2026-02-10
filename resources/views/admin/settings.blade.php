@extends('layouts.admin')

@section('content')
<div class="min-h-screen p-8">
    <div class="glass-effect rounded-2xl shadow-xl p-8 mb-8 animate-fadeInUp">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold gradient-text">{{ __('messages.system_settings') }}</h1>
                <p class="text-gray-600 mt-2">{{ __('messages.configure_application_settings') }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn-premium px-6 py-3 bg-gradient-to-r from-gray-700 to-gray-900 text-white rounded-xl font-semibold shadow-lg">{{ __('messages.back_to_dashboard') }}</a>
        </div>
    </div>

    <div class="grid gap-6">
        <!-- Application Settings -->
        <div class="glass-effect rounded-2xl shadow-xl p-8 card-hover animate-fadeInUp" style="animation-delay: 0.2s">
            <h3 class="text-2xl font-semibold gradient-text mb-6">{{ __('messages.application_settings') }}</h3>
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.application_name') }}</label>
                    <input type="text" name="app_name" value="Passtik" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.support_email') }}</label>
                    <input type="email" name="support_email" value="support@passtik.net" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.support_phone') }}</label>
                    <input type="text" name="support_phone" value="0933003304" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
                <button type="submit" class="btn-premium px-8 py-3 gradient-bg text-white rounded-xl font-semibold shadow-lg">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ __('messages.save_settings') }}
                    </span>
                </button>
            </form>
        </div>

        <!-- Pricing Plans -->
        <div class="glass-effect rounded-2xl shadow-xl p-8 card-hover animate-fadeInUp" style="animation-delay: 0.3s">
            <h3 class="text-2xl font-semibold gradient-text mb-6">{{ __('messages.pricing_plans') }}</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-6 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl hover:shadow-lg transition-all">
                    <div>
                        <p class="font-semibold text-gray-900 text-lg">{{ __('messages.free_plan') }}</p>
                        <p class="text-sm text-gray-600">50 {{ __('messages.vouchers_limit') }}</p>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">$0</span>
                </div>
                <div class="flex justify-between items-center p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl hover:shadow-lg transition-all">
                    <div>
                        <p class="font-semibold text-gray-900 text-lg">{{ __('messages.pro_plan') }}</p>
                        <p class="text-sm text-gray-600">500 {{ __('messages.vouchers_limit') }}</p>
                    </div>
                    <span class="text-2xl font-bold gradient-text">$29</span>
                </div>
                <div class="flex justify-between items-center p-6 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl hover:shadow-lg transition-all">
                    <div>
                        <p class="font-semibold text-gray-900 text-lg">{{ __('messages.enterprise_plan') }}</p>
                        <p class="text-sm text-gray-600">{{ __('messages.unlimited_vouchers') }}</p>
                    </div>
                    <span class="text-2xl font-bold gradient-text">$99</span>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="glass-effect rounded-2xl shadow-xl p-8 card-hover animate-fadeInUp" style="animation-delay: 0.4s">
            <h3 class="text-2xl font-semibold gradient-text mb-6">{{ __('messages.system_information') }}</h3>
            <div class="space-y-4">
                <div class="flex justify-between p-4 bg-white/50 rounded-xl">
                    <span class="text-gray-600 font-medium">{{ __('messages.laravel_version') }}</span>
                    <span class="font-semibold text-gray-900">{{ app()->version() }}</span>
                </div>
                <div class="flex justify-between p-4 bg-white/50 rounded-xl">
                    <span class="text-gray-600 font-medium">{{ __('messages.php_version') }}</span>
                    <span class="font-semibold text-gray-900">{{ PHP_VERSION }}</span>
                </div>
                <div class="flex justify-between p-4 bg-white/50 rounded-xl">
                    <span class="text-gray-600 font-medium">{{ __('messages.database') }}</span>
                    <span class="font-semibold text-gray-900">{{ config('database.default') }}</span>
                </div>
                <div class="flex justify-between p-4 bg-white/50 rounded-xl">
                    <span class="text-gray-600 font-medium">{{ __('messages.environment') }}</span>
                    <span class="font-semibold text-gray-900">{{ app()->environment() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
