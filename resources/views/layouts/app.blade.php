<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ?? false ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('messages.app_name') . ' - Premium MikroTik SaaS')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [dir="rtl"] { text-align: right; }
        [dir="rtl"] .lg\:ml-64 { margin-left: 0 !important; margin-right: 16rem !important; }
        [dir="rtl"] aside { left: auto; right: 0; }
        [dir="rtl"] aside.transform.-translate-x-full { transform: translateX(100%); }
        [dir="rtl"] aside.lg\:translate-x-0 { transform: translateX(0) !important; }
        [dir="rtl"] .text-left { text-align: right; }
        [dir="rtl"] .text-right { text-align: left; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes shimmer { 0% { background-position: -1000px 0; } 100% { background-position: 1000px 0; } }
        .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .gradient-text { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .glass-effect { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .btn-premium { position: relative; overflow: hidden; transition: all 0.3s ease; }
        .btn-premium:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); }
        .btn-premium:active { transform: translateY(0); }
        .btn-premium::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); transition: left 0.5s; }
        .btn-premium:hover::before { left: 100%; }
        .input-focus:focus { box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); border-color: #6366f1; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-indigo-50 to-purple-50">
    @include('components.notifications')
    
    <!-- Mobile overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>
    
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen gradient-bg text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow-2xl">
        <div class="h-full px-6 py-8 flex flex-col">
            <!-- Logo -->
            <div class="flex items-center justify-between mb-12">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-lg rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold">{{ __('messages.app_name') }}</span>
                </div>
                <button id="close-sidebar" class="lg:hidden text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="space-y-2 flex-1">
                @if(auth()->user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }} rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>{{ __('messages.admin_dashboard') }}</span>
                </a>
                @endif
                
                <a href="{{ route('routers.index') }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('routers.*') && !request()->routeIs('routers.profiles') && !request()->routeIs('routers.manage') ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }} rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ __('messages.routers') }}</span>
                </a>
                
                @if(request()->routeIs('routers.manage'))
                    @php
                        $currentRouter = request()->route('router');
                    @endphp
                    @if($currentRouter && ($currentRouter->user_id == auth()->id() || !$currentRouter->user_id))
                        <a href="{{ route('routers.manage', $currentRouter) }}" class="flex items-center gap-3 p-3 bg-white/20 text-white rounded-xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span>{{ __('messages.dashboard') }}</span>
                        </a>
                    @endif
                @endif
                
                @if(request()->routeIs('routers.manage') || request()->routeIs('vouchers.*'))
                    @php
                        $currentRouter = request()->route('router');
                    @endphp
                    @if($currentRouter && ($currentRouter->user_id == auth()->id() || !$currentRouter->user_id))
                        <a href="{{ route('vouchers.index') }}?router={{ $currentRouter->id }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('vouchers.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white hover:bg-white/10' }} rounded-xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                            </svg>
                            <span>{{ __('messages.vouchers') }}</span>
                        </a>
                    @endif
                @endif
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-64 min-h-screen">
        <!-- Top Bar -->
        <header class="glass-effect shadow-lg px-4 lg:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button id="mobile-menu-btn" class="lg:hidden text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-xl lg:text-2xl font-bold gradient-text">@yield('page-title')</h1>
                        <p class="text-sm text-gray-600 hidden sm:block">@yield('page-subtitle')</p>
                    </div>
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
                    @yield('header-actions')
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="p-4 lg:p-6">
            @yield('content')
        </div>
    </main>

    <script>
        // Mobile menu functionality
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');
        const closeSidebar = document.getElementById('close-sidebar');

        function openSidebar() {
            if (sidebar && overlay) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }
        }

        function closeSidebarFn() {
            if (sidebar && overlay) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', openSidebar);
        }
        
        if (closeSidebar) {
            closeSidebar.addEventListener('click', closeSidebarFn);
        }
        
        if (overlay) {
            overlay.addEventListener('click', closeSidebarFn);
        }

        // Close sidebar on window resize to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                closeSidebarFn();
            }
        });
        
        @if(session('alert_success'))
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ addslashes(session('alert_success')) }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        @endif

        @if(session('alert_error'))
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ addslashes(session('alert_error')) }}',
                    confirmButtonColor: '#6366f1'
                });
            }
        @endif
        
        @if(session('alert_warning'))
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: '{{ addslashes(session('alert_warning')) }}',
                    confirmButtonColor: '#6366f1'
                });
            }
        @endif
    </script>
</body>
</html>