@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold mb-6">Subscription Requests</h2>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($requests as $request)
                <tr>
                    <td class="px-6 py-4">
                        <div class="font-medium">{{ $request->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $request->user->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded bg-gray-100">{{ ucfirst($request->user->plan) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded bg-indigo-100 text-indigo-800">{{ ucfirst($request->requested_plan) }}</span>
                    </td>
                    <td class="px-6 py-4">{{ $request->phone }}</td>
                    <td class="px-6 py-4">
                        @if($request->status === 'pending')
                            <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($request->status === 'approved')
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Approved</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Rejected</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $request->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        @if($request->status === 'pending')
                            <form method="POST" action="{{ route('admin.subscription.approve', $request) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800 mr-2">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.subscription.reject', $request) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800">Reject</button>
                            </form>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No subscription requests</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $requests->links() }}
    </div>
</div>
@endsection
