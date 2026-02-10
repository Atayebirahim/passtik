@extends('layouts.admin')

@section('content')
<div class="min-h-screen p-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="glass-effect rounded-2xl shadow-xl p-8 mb-8 animate-fadeInUp">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-4xl font-bold gradient-text">Edit User</h1>
                    <p class="text-gray-600 mt-2">Update user information and permissions</p>
                </div>
                <a href="{{ route('admin.users') }}" class="btn-premium px-6 py-3 bg-gradient-to-r from-gray-700 to-gray-900 text-white rounded-xl font-semibold shadow-lg">Back to Users</a>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="glass-effect rounded-2xl shadow-xl p-8 animate-fadeInUp" style="animation-delay: 0.1s">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plan</label>
                    <select name="plan" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        <option value="free" {{ $user->plan === 'free' ? 'selected' : '' }}>Free (50 vouchers)</option>
                        <option value="pro" {{ $user->plan === 'pro' ? 'selected' : '' }}>Pro (500 vouchers)</option>
                        <option value="enterprise" {{ $user->plan === 'enterprise' ? 'selected' : '' }}>Enterprise (Unlimited)</option>
                    </select>
                    @error('plan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Voucher Limit</label>
                    <input type="number" name="voucher_limit" value="{{ old('voucher_limit', $user->voucher_limit) }}" required min="0" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    <p class="text-sm text-gray-500 mt-1">Maximum number of vouchers this user can create</p>
                    @error('voucher_limit')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_admin" id="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="is_admin" class="ml-3 text-sm font-medium text-gray-700">Grant Admin Access</label>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm text-blue-700">
                            <p class="font-semibold">User Statistics:</p>
                            <p class="mt-1">Routers: {{ $user->routers()->count() }} | Vouchers: {{ $user->vouchers()->count() }}</p>
                            <p class="mt-1">Joined: {{ $user->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn-premium px-8 py-3 gradient-bg text-white rounded-xl font-semibold shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update User
                    </button>
                    <a href="{{ route('admin.users') }}" class="btn-premium px-8 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-xl font-semibold shadow-lg">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
