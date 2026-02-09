@extends('layouts.app')

@section('title', 'Add Router - Passtik')
@section('page-title', 'Add New Router')
@section('page-subtitle', 'Connect your MikroTik device to Passtik')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <form action="{{ route('routers.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Router Name</label>
                <input type="text" name="name" required
                       class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200 placeholder-gray-400"
                       placeholder="e.g., Main Office Router">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Local IP Address</label>
                <input type="text" name="local_ip" required
                       class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200 placeholder-gray-400"
                       placeholder="192.168.88.1">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">API Username</label>
                    <input type="text" name="api_user" value="admin" required
                           class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">API Password</label>
                    <input type="password" name="api_password" required
                           class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl input-focus transition-all duration-200 placeholder-gray-400"
                           placeholder="Enter password">
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-700">
                        <p class="font-medium mb-1">Quick Setup Tips:</p>
                        <ul class="text-xs space-y-1 text-blue-600">
                            <li>• Enable API service in MikroTik</li>
                            <li>• Use admin credentials or create API user</li>
                            <li>• Ensure network connectivity</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 px-6 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200">
                    Add Router
                </button>
                <a href="{{ route('routers.index') }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection