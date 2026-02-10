@extends('layouts.app')

@section('title', 'Edit Router - Passtik')
@section('page-title', 'Edit Router')
@section('page-subtitle', 'Update router configuration')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl p-8">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('routers.update', $router) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Router Name</label>
                <input type="text" name="name" value="{{ old('name', $router->name) }}" required
                       class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200 @error('name') border-2 border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Local IP Address</label>
                <input type="text" name="local_ip" value="{{ old('local_ip', $router->local_ip) }}" required
                       class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200 @error('local_ip') border-2 border-red-500 @enderror">
                @error('local_ip')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">API Username</label>
                    <input type="text" name="api_user" value="{{ old('api_user', $router->api_user) }}" required
                           class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200 @error('api_user') border-2 border-red-500 @enderror">
                    @error('api_user')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">API Password</label>
                    <input type="password" name="api_password" placeholder="Leave blank to keep current" 
                           class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200 @error('api_password') border-2 border-red-500 @enderror">
                    @error('api_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 px-6 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200">
                    Update Router
                </button>
                <a href="{{ route('routers.show', $router) }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection