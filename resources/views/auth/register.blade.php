<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.register') }} - {{ __('messages.app_name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        [dir="rtl"] { text-align: right; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <!-- Language Switcher -->
            <div class="flex justify-end mb-4" x-data="{ open: false }">
                <div class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 bg-white rounded-lg shadow hover:shadow-md transition-all">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">{{ strtoupper(app()->getLocale()) }}</span>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute {{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'left-0' : 'right-0' }} mt-2 w-40 bg-white rounded-lg shadow-lg py-2 z-50">
                        <a href="?lang=en" class="block px-4 py-2 text-sm hover:bg-gray-100">English</a>
                        <a href="?lang=ar" class="block px-4 py-2 text-sm hover:bg-gray-100">العربية</a>
                        <a href="?lang=es" class="block px-4 py-2 text-sm hover:bg-gray-100">Español</a>
                        <a href="?lang=fr" class="block px-4 py-2 text-sm hover:bg-gray-100">Français</a>
                    </div>
                </div>
            </div>
            
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center space-x-2">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span class="text-3xl font-bold text-gray-900">{{ __('messages.app_name') }}</span>
                </a>
                <h2 class="mt-6 text-3xl font-bold text-gray-900">{{ __('messages.create_account') }}</h2>
                <p class="mt-2 text-sm text-gray-600">Start managing your WiFi vouchers today</p>
            </div>

            <div class="bg-white py-8 px-6 shadow-xl rounded-lg">
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
                        <p class="text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </p>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('messages.name') }}</label>
                        <input id="name" name="name" type="text" required value="{{ old('name') }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">{{ __('messages.email') }}</label>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">{{ __('messages.password') }}</label>
                        <input id="password" name="password" type="password" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('messages.confirm_password') }}</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <button type="submit" class="w-full gradient-bg text-white py-3 px-4 rounded-md font-semibold hover:opacity-90">
                        {{ __('messages.create_account') }}
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-gray-600">
                    {{ __('messages.already_have_account') }}
                    <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">{{ __('messages.login') }}</a>
                </p>
            </div>

            <p class="mt-8 text-center text-xs text-gray-500">
                By creating an account, you agree to our 
                <a href="{{ route('terms') }}" class="text-indigo-600 hover:text-indigo-500">Terms of Service</a> and 
                <a href="{{ route('privacy') }}" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>
            </p>
        </div>
    </div>
</body>
</html>
