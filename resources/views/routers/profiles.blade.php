@extends('layouts.app')

@section('title', 'User Profiles - Passtik')
@section('page-title')
User Profiles - {{ $router->name }}
@endsection
@section('page-subtitle', 'Manage hotspot user profiles and bandwidth limits')

@section('header-actions')
<div class="flex gap-2">
    <a href="{{ route('routers.manage', $router) }}" 
       class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
        Back to Dashboard
    </a>
    <button onclick="showCreateProfileForm()" 
            class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-xl font-semibold hover:shadow-lg transition-all">
        + Create Profile
    </button>
</div>
@endsection

@section('content')
@if(isset($connectionError))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl flex items-center gap-4 text-red-700">
        <svg class="w-6 h-6 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        <p class="font-medium">Router connection failed: {{ $connectionError }}</p>
    </div>
@endif

<div class="bg-white rounded-2xl shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Shared Users</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Rate Limit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Session Timeout</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($profiles as $profile)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-purple-600">
                            {{ $profile['name'] ?? 'N/A' }}
                            <div class="sm:hidden text-xs text-gray-500 mt-1">
                                Users: {{ $profile['shared-users'] ?? 'Unlimited' }} | 
                                Rate: {{ $profile['rate-limit'] ?? 'None' }} | 
                                Timeout: {{ $profile['session-timeout'] ?? 'None' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-900 hidden sm:table-cell">{{ $profile['shared-users'] ?? 'Unlimited' }}</td>
                        <td class="px-6 py-4 text-gray-900 hidden md:table-cell">{{ $profile['rate-limit'] ?? 'None' }}</td>
                        <td class="px-6 py-4 text-gray-900 hidden lg:table-cell">{{ $profile['session-timeout'] ?? 'None' }}</td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex gap-2">
                                <button onclick="editProfile('{{ $profile['.id'] }}', @json($profile))" 
                                        class="text-blue-600 hover:text-blue-900">
                                    Edit
                                </button>
                                <button onclick="deleteProfile('{{ $profile['.id'] }}', '{{ $profile['name'] }}')" 
                                        class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No profiles found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function showCreateProfileForm() {
    Swal.fire({
        title: 'Create User Profile',
        html: `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profile Name *</label>
                    <input id="profile-name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter profile name" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Shared Users</label>
                    <input id="shared-users" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Number of concurrent users" type="number">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rate Limit</label>
                    <input id="rate-limit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 1M/1M">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Session Timeout</label>
                    <input id="session-timeout" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 1h, 30m">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Idle Timeout</label>
                    <input id="idle-timeout" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 10m">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keepalive Timeout</label>
                    <input id="keepalive-timeout" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 2m">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Autorefresh</label>
                    <input id="status-autorefresh" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 1m">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transparent Proxy</label>
                    <select id="transparent-proxy" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Option</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Create Profile',
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#6b7280',
        width: '800px',
        customClass: {
            popup: 'rounded-2xl',
            title: 'text-xl font-bold text-gray-900',
            confirmButton: 'rounded-xl px-6 py-3 font-semibold',
            cancelButton: 'rounded-xl px-6 py-3 font-semibold'
        },
        preConfirm: () => {
            const name = document.getElementById('profile-name').value;
            if (!name) {
                Swal.showValidationMessage('Profile name is required');
                return false;
            }
            return {
                name: name,
                shared_users: document.getElementById('shared-users').value,
                rate_limit: document.getElementById('rate-limit').value,
                session_timeout: document.getElementById('session-timeout').value,
                idle_timeout: document.getElementById('idle-timeout').value,
                keepalive_timeout: document.getElementById('keepalive-timeout').value,
                status_autorefresh: document.getElementById('status-autorefresh').value,
                transparent_proxy: document.getElementById('transparent-proxy').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            createProfile(result.value);
        }
    });
}

function createProfile(data) {
    fetch('{{ route('routers.profiles.create', $router) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            Swal.fire('Success!', result.message, 'success');
            location.reload();
        } else {
            Swal.fire('Error!', result.error, 'error');
        }
    });
}

function editProfile(id, profile) {
    Swal.fire({
        title: 'Edit User Profile',
        html: `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profile Name *</label>
                    <input id="profile-name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter profile name" value="${profile.name || ''}" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Shared Users</label>
                    <input id="shared-users" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Number of concurrent users" type="number" value="${profile['shared-users'] || ''}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rate Limit</label>
                    <input id="rate-limit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 1M/1M" value="${profile['rate-limit'] || ''}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Session Timeout</label>
                    <input id="session-timeout" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 1h, 30m" value="${profile['session-timeout'] || ''}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Idle Timeout</label>
                    <input id="idle-timeout" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 10m" value="${profile['idle-timeout'] || ''}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keepalive Timeout</label>
                    <input id="keepalive-timeout" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 2m" value="${profile['keepalive-timeout'] || ''}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Autorefresh</label>
                    <input id="status-autorefresh" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 1m" value="${profile['status-autorefresh'] || ''}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transparent Proxy</label>
                    <select id="transparent-proxy" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Option</option>
                        <option value="yes" ${profile['transparent-proxy'] === 'yes' ? 'selected' : ''}>Yes</option>
                        <option value="no" ${profile['transparent-proxy'] === 'no' ? 'selected' : ''}>No</option>
                    </select>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Update Profile',
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#6b7280',
        width: '800px',
        customClass: {
            popup: 'rounded-2xl',
            title: 'text-xl font-bold text-gray-900',
            confirmButton: 'rounded-xl px-6 py-3 font-semibold',
            cancelButton: 'rounded-xl px-6 py-3 font-semibold'
        },
        preConfirm: () => {
            const name = document.getElementById('profile-name').value;
            if (!name) {
                Swal.showValidationMessage('Profile name is required');
                return false;
            }
            return {
                profile_id: id,
                name: name,
                shared_users: document.getElementById('shared-users').value,
                rate_limit: document.getElementById('rate-limit').value,
                session_timeout: document.getElementById('session-timeout').value,
                idle_timeout: document.getElementById('idle-timeout').value,
                keepalive_timeout: document.getElementById('keepalive-timeout').value,
                status_autorefresh: document.getElementById('status-autorefresh').value,
                transparent_proxy: document.getElementById('transparent-proxy').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            updateProfile(result.value);
        }
    });
}

function updateProfile(data) {
    fetch('{{ route('routers.profiles.update', $router) }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            Swal.fire('Success!', result.message, 'success');
            location.reload();
        } else {
            Swal.fire('Error!', result.error, 'error');
        }
    });
}

function deleteProfile(id, name) {
    Swal.fire({
        title: 'Delete Profile?',
        text: `Remove profile "${name}" from router?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route('routers.profiles.delete', $router) }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ profile_id: id })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    Swal.fire('Deleted!', result.message, 'success');
                    location.reload();
                } else {
                    Swal.fire('Error!', result.error, 'error');
                }
            });
        }
    });
}
</script>
@endsection