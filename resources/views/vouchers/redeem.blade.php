<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.redeem_voucher') }} - {{ __('messages.app_name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [dir="rtl"] { text-align: right; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Language Switcher -->
        <div class="flex justify-end mb-4">
            <div class="bg-white rounded-lg shadow-md p-2 flex gap-2">
                <a href="?lang=en" class="px-3 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} text-sm">EN</a>
                <a href="?lang=ar" class="px-3 py-1 rounded {{ app()->getLocale() === 'ar' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} text-sm">AR</a>
                <a href="?lang=es" class="px-3 py-1 rounded {{ app()->getLocale() === 'es' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} text-sm">ES</a>
                <a href="?lang=fr" class="px-3 py-1 rounded {{ app()->getLocale() === 'fr' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} text-sm">FR</a>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-2xl p-8">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('messages.redeem_voucher') }}</h1>
                <p class="text-gray-600">{{ __('messages.enter_voucher_code') }}</p>
            </div>

            <div id="alert" class="hidden mb-6 p-4 rounded-xl"></div>

            <form id="redeemForm" class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('messages.voucher_code') }}</label>
                    <input type="text" id="code" name="code" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-600 focus:outline-none text-lg font-mono uppercase"
                           placeholder="{{ __('messages.enter_code') }}">
                    <p class="text-xs text-gray-500 mt-2">{{ __('messages.use_code_for_login') }}</p>
                </div>

                <button type="submit" id="submitBtn"
                        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4 rounded-xl font-bold text-lg hover:shadow-lg transition-all">
                    {{ __('messages.activate_voucher') }}
                </button>
            </form>

            <div id="successInfo" class="hidden mt-6 p-6 bg-green-50 border-2 border-green-200 rounded-xl">
                <h3 class="text-lg font-bold text-green-900 mb-4">✓ {{ __('messages.voucher_activated') }}</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('messages.voucher_code') }}:</span>
                        <span class="font-mono font-bold" id="voucherCode"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('messages.duration') }}:</span>
                        <span class="font-bold" id="duration"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('messages.bandwidth') }}:</span>
                        <span class="font-bold" id="bandwidth"></span>
                    </div>
                </div>
                <p class="mt-4 text-sm text-gray-600">{{ __('messages.use_voucher_to_login') }}</p>
            </div>
        </div>

        <p class="text-center text-sm text-gray-600 mt-6">
            {{ __('messages.powered_by') }} <span class="font-bold">{{ __('messages.app_name') }}</span>
        </p>
    </div>

    <script>
        const form = document.getElementById('redeemForm');
        const alert = document.getElementById('alert');
        const successInfo = document.getElementById('successInfo');
        const submitBtn = document.getElementById('submitBtn');
        const translations = {
            activating: '{{ __('messages.activating') }}',
            activate_voucher: '{{ __('messages.activate_voucher') }}',
            network_error: '{{ __('messages.network_error') }}',
            redemption_failed: '{{ __('messages.redemption_failed') }}'
        };

        // Get MAC address (simplified - in production use proper method)
        function getMacAddress() {
            return null; // Will be handled by MikroTik
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const code = document.getElementById('code').value.trim().toUpperCase();
            
            submitBtn.disabled = true;
            submitBtn.textContent = translations.activating;
            alert.classList.add('hidden');
            successInfo.classList.add('hidden');

            try {
                const response = await fetch('https://www.passtik.net/api/vouchers/redeem', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        code: code,
                        mac: getMacAddress()
                    })
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById('voucherCode').textContent = data.data.username;
                    document.getElementById('duration').textContent = data.data.duration;
                    document.getElementById('bandwidth').textContent = data.data.bandwidth;
                    
                    successInfo.classList.remove('hidden');
                    form.classList.add('hidden');
                } else {
                    showAlert(data.error || translations.redemption_failed, 'error');
                }
            } catch (error) {
                showAlert(translations.network_error, 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = translations.activate_voucher;
            }
        });

        function showAlert(message, type) {
            alert.className = `mb-6 p-4 rounded-xl ${type === 'error' ? 'bg-red-50 border-2 border-red-200 text-red-800' : 'bg-green-50 border-2 border-green-200 text-green-800'}`;
            alert.textContent = message;
            alert.classList.remove('hidden');
        }
    </script>
</body>
</html>
