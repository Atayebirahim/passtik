@extends('layouts.app')

@section('title', 'Edit Router - Passtik')
@section('page-title', 'Edit Router')
@section('page-subtitle', 'Update router configuration')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <form action="{{ route('routers.update', $router) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Router Name</label>
                <input type="text" name="name" value="{{ $router->name }}" required
                       class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Local IP Address</label>
                <input type="text" name="local_ip" value="{{ $router->local_ip }}" required
                       class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">API Username</label>
                    <input type="text" name="api_user" value="{{ $router->api_user }}" required
                           class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">API Password</label>
                    <input type="password" name="api_password" value="{{ $router->api_password }}" required
                           class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200">
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