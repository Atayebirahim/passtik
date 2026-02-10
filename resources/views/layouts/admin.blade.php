<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ?? false ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('messages.admin_dashboard') . ' - ' . __('messages.app_name'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        [dir="rtl"] { text-align: right; }
        [dir="rtl"] .lg\:ml-64 { margin-left: 0 !important; margin-right: 16rem !important; }
        [dir="rtl"] aside { left: auto; right: 0; }
        [dir="rtl"] aside.transform.-translate-x-full { transform: translateX(100%); }
        [dir="rtl"] aside.lg\:translate-x-0 { transform: translateX(0) !important; }
        [dir="rtl"] .text-left { text-align: right; }
        [dir="rtl"] .text-right { text-align: left; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideIn { from { transform: translateX(-20px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.8; } }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        @keyframes shimmer { 0% { background-position: -1000px 0; } 100% { background-position: 1000px 0; } }
        .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
        .animate-slideIn { animation: slideIn 0.4s ease-out forwards; }
        .animate-pulse-slow { animation: pulse 3s ease-in-out infinite; }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .gradient-text { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .glass-effect { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); }
        .btn-premium { position: relative; overflow: hidden; transition: all 0.3s ease; }
        .btn-premium:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); }
        .btn-premium:active { transform: translateY(0); }
        .btn-premium::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); transition: left 0.5s; }
        .btn-premium:hover::before { left: 100%; }
        .btn-shimmer { background: linear-gradient(90deg, currentColor 0%, rgba(255,255,255,0.5) 50%, currentColor 100%); background-size: 200% 100%; animation: shimmer 2s infinite; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-indigo-50 to-purple-50 min-h-screen">
    @include('components.notifications')
    
    <!-- Mobile overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>
    
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen gradient-bg text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow-2xl">
        <div class="h-full px-6 py-8 flex flex-col">
            <!-- Logo -->
            <div class="flex items-center justify-between mb-12 animate-slideIn">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center animate-float">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-2xl font-bold">{{ __('messages.app_name') }}</span>
                        <p class="text-xs text-white/70">{{ __('messages.admin_dashboard') }}</p>
                    </div>
                </div>
                <button id="close-sidebar" class="lg:hidden text-white/70 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="space-y-2 flex-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }} rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>{{ __('messages.dashboard') }}</span>
                </a>
                
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.users') ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }} rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>{{ __('messages.users') }}</span>
                </a>

                <a href="{{ route('admin.routers') }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.routers') ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }} rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                    </svg>
                    <span>{{ __('messages.routers') }}</span>
                </a>

                <a href="{{ route('admin.vouchers') }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.vouchers') ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }} rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                    <span>{{ __('messages.vouchers') }}</span>
                </a>

                <a href="{{ route('admin.subscriptions') }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.subscriptions') ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }} rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ __('messages.subscription') }}</span>
                </a>

                <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.settings') ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }} rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>{{ __('messages.settings') }}</span>
                </a>
            </nav>

            <!-- User Info -->
            <div class="mt-auto pt-6 border-t border-white/20">
                <div class="flex items-center gap-3 p-3 bg-white/10 rounded-xl">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-white/70">{{ __('messages.admin_dashboard') }}</p>
                    </div>
                </div>
                <a href="{{ route('routers.index') }}" class="mt-3 flex items-center justify-center gap-2 p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span class="text-sm">{{ __('messages.back') }}</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-64">
        <!-- Top Bar -->
        <header class="glass-effect shadow-lg px-4 lg:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button id="mobile-menu-btn" class="lg:hidden text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-xl lg:text-2xl font-bold gradient-text">{{ __('messages.admin_dashboard') }}</h1>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Language Switcher -->
                    <div class="relative z-50" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">{{ strtoupper($currentLocale ?? 'en') }}</span>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute {{ $isRtl ?? false ? 'left-0' : 'right-0' }} mt-2 w-40 bg-white rounded-lg shadow-lg py-2 z-[100]" style="display: none;" x-transition>
                            <a href="?lang=en" class="block px-4 py-2 text-sm hover:bg-gray-100 {{ ($currentLocale ?? 'en') === 'en' ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700' }}">English</a>
                            <a href="?lang=ar" class="block px-4 py-2 text-sm hover:bg-gray-100 {{ ($currentLocale ?? 'en') === 'ar' ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700' }}">العربية</a>
                            <a href="?lang=es" class="block px-4 py-2 text-sm hover:bg-gray-100 {{ ($currentLocale ?? 'en') === 'es' ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700' }}">Español</a>
                            <a href="?lang=fr" class="block px-4 py-2 text-sm hover:bg-gray-100 {{ ($currentLocale ?? 'en') === 'fr' ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700' }}">Français</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        @yield('content')
    </main>

    <script>
        // Mobile menu functionality
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');
        const closeSidebar = document.getElementById('close-sidebar');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        }

        function closeSidebarFn() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }

        mobileMenuBtn?.addEventListener('click', openSidebar);
        closeSidebar?.addEventListener('click', closeSidebarFn);
        overlay?.addEventListener('click', closeSidebarFn);

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                closeSidebarFn();
            }
        });
    </script>
</body>
</html>
