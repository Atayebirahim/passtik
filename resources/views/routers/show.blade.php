@extends('layouts.app')

@section('title', 'Router Details - Passtik')
@section('page-title', 'Router Details')
@section('page-subtitle', 'View and manage router information')

@section('header-actions')
<a href="{{ route('routers.manage', $router) }}" 
   class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-xl font-semibold hover:shadow-lg transition-all">
    Dashboard
</a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Router Name</label>
                    <p class="text-xl font-semibold text-gray-900">{{ $router->name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Local IP Address</label>
                    <p class="text-xl font-semibold text-gray-900">{{ $router->local_ip }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">VPN IP Address</label>
                    <p class="text-xl font-semibold text-gray-900">{{ $router->vpn_ip ?: 'Not set' }}</p>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">API Username</label>
                    <p class="text-xl font-semibold text-gray-900">{{ $router->api_user }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Created</label>
                    <p class="text-xl font-semibold text-gray-900">{{ $router->created_at->format('M d, Y') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                    <p class="text-xl font-semibold text-gray-900">{{ $router->updated_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap gap-4">
            <a href="{{ route('routers.edit', $router) }}" 
               class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700 transition-colors">
                Edit Router
            </a>
            <a href="{{ route('routers.manage', $router) }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                Router Dashboard
            </a>
            <a href="{{ route('routers.check', $router) }}" 
               class="bg-green-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-700 transition-colors">
                Test Connection
            </a>
            <form action="{{ route('routers.destroy', $router) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-red-700 transition-colors" 
                        onclick="return confirm('Are you sure you want to delete this router?')">
                    Delete Router
                </button>
            </form>
        </div>
    </div>
</div>
@endsection