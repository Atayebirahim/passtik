@extends('layouts.app')

@section('page-title', __('messages.profile'))
@section('page-subtitle', __('messages.manage_your_account'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Profile Information -->
    <div class="glass-effect rounded-2xl shadow-xl p-8 animate-fadeInUp">
        <h2 class="text-2xl font-bold gradient-text mb-6">{{ __('messages.profile_information') }}</h2>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.name') }}</label>
                    <input type="text" name="name" value="{{ auth()->user()->name }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.email') }}</label>
                    <input type="email" name="email" value="{{ auth()->user()->email }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.phone') }}</label>
                    <input type="tel" name="phone" value="{{ auth()->user()->phone }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition-all">
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="btn-premium px-8 py-3 gradient-bg text-white rounded-xl font-semibold shadow-lg">
                    {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Account Details -->
    <div class="glass-effect rounded-2xl shadow-xl p-8 animate-fadeInUp" style="animation-delay: 0.1s">
        <h2 class="text-2xl font-bold gradient-text mb-6">{{ __('messages.account_details') }}</h2>
        <div class="grid md:grid-cols-2 gap-6">
            <div class="p-4 bg-white/50 rounded-xl">
                <p class="text-sm text-gray-600">{{ __('messages.current_plan') }}</p>
                <p class="text-lg font-semibold gradient-text">{{ ucfirst(auth()->user()->plan) }}</p>
            </div>
            <div class="p-4 bg-white/50 rounded-xl">
                <p class="text-sm text-gray-600">{{ __('messages.limit') }}</p>
                <p class="text-lg font-semibold text-gray-900">{{ auth()->user()->voucher_limit }}</p>
            </div>
            <div class="p-4 bg-white/50 rounded-xl">
                <p class="text-sm text-gray-600">{{ __('messages.routers') }}</p>
                <p class="text-lg font-semibold text-gray-900">{{ auth()->user()->routers()->count() }}</p>
            </div>
            <div class="p-4 bg-white/50 rounded-xl">
                <p class="text-sm text-gray-600">{{ __('messages.vouchers') }}</p>
                <p class="text-lg font-semibold text-gray-900">{{ auth()->user()->routers()->withCount('vouchers')->get()->sum('vouchers_count') }}</p>
            </div>
        </div>
        @if(auth()->user()->plan === 'free')
        <div class="mt-6">
            <a href="{{ route('subscription.upgrade') }}" class="btn-premium px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-semibold shadow-lg inline-block">
                {{ __('messages.upgrade') }}
            </a>
        </div>
        @endif
    </div>

    <!-- Change Password -->
    <div class="glass-effect rounded-2xl shadow-xl p-8 animate-fadeInUp" style="animation-delay: 0.2s">
        <h2 class="text-2xl font-bold gradient-text mb-6">{{ __('messages.change_password') }}</h2>
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.current_password') }}</label>
                    <input type="password" name="current_password" required class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.new_password') }}</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.confirm_password') }}</label>
                    <input type="password" name="password_confirmation" required class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition-all">
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="btn-premium px-8 py-3 gradient-bg text-white rounded-xl font-semibold shadow-lg">
                    {{ __('messages.update_password') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="glass-effect rounded-2xl shadow-xl p-8 border-2 border-red-200 animate-fadeInUp" style="animation-delay: 0.3s">
        <h2 class="text-2xl font-bold text-red-600 mb-6">{{ __('messages.danger_zone') }}</h2>
        <p class="text-gray-600 mb-6">{{ __('messages.delete_account_warning') }}</p>
        <button onclick="confirmDelete()" class="btn-premium px-8 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl font-semibold shadow-lg">
            {{ __('messages.delete_account') }}
        </button>
    </div>
</div>

<script>
function confirmDelete() {
    Swal.fire({
        title: '{{ __("messages.are_you_sure") }}',
        text: '{{ __("messages.delete_account_confirmation") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6366f1',
        confirmButtonText: '{{ __("messages.yes_delete") }}',
        cancelButtonText: '{{ __("messages.cancel") }}',
        input: 'password',
        inputPlaceholder: '{{ __("messages.enter_password") }}',
        inputAttributes: {
            required: true
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("profile.delete") }}';
            form.innerHTML = `
                @csrf
                @method('DELETE')
                <input type="hidden" name="password" value="${result.value}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection
