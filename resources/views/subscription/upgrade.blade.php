@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-3xl font-bold mb-6">Upgrade Your Plan</h2>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <p class="text-blue-700">
                <strong>Current Plan:</strong> {{ ucfirst($user->plan) }} 
                ({{ $user->voucher_limit }} vouchers limit)
            </p>
        </div>

        @if($pendingRequest)
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                <p class="text-yellow-700">
                    <strong>Pending Request:</strong> You requested to upgrade to {{ ucfirst($pendingRequest->requested_plan) }} plan.
                    We will contact you at {{ $pendingRequest->phone }} soon.
                </p>
            </div>
        @else
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <!-- Pro Plan -->
                <div class="bg-white border-2 border-indigo-500 rounded-lg p-6">
                    <h3 class="text-2xl font-bold mb-2">Pro Plan</h3>
                    <p class="text-4xl font-bold text-indigo-600 mb-4">$29<span class="text-lg text-gray-600">/month</span></p>
                    <ul class="space-y-2 mb-6">
                        <li>✓ 500 vouchers/month</li>
                        <li>✓ 5 routers</li>
                        <li>✓ Advanced analytics</li>
                        <li>✓ Priority support</li>
                    </ul>
                    <button onclick="openModal('pro')" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">
                        Request Upgrade
                    </button>
                </div>

                <!-- Enterprise Plan -->
                <div class="bg-white border-2 border-purple-500 rounded-lg p-6">
                    <h3 class="text-2xl font-bold mb-2">Enterprise Plan</h3>
                    <p class="text-4xl font-bold text-purple-600 mb-4">$99<span class="text-lg text-gray-600">/month</span></p>
                    <ul class="space-y-2 mb-6">
                        <li>✓ Unlimited vouchers</li>
                        <li>✓ Unlimited routers</li>
                        <li>✓ White-label solution</li>
                        <li>✓ 24/7 phone support</li>
                    </ul>
                    <button onclick="openModal('enterprise')" class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700">
                        Request Upgrade
                    </button>
                </div>
            </div>
        @endif

        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="font-bold mb-2">How Manual Activation Works:</h3>
            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                <li>Submit upgrade request with your phone number</li>
                <li>Our team will contact you within 24 hours</li>
                <li>Complete payment via bank transfer or mobile money</li>
                <li>We activate your plan immediately after payment confirmation</li>
                <li>Start enjoying your upgraded features!</li>
            </ol>
            <p class="mt-4 text-sm text-gray-600">
                <strong>Payment Methods:</strong> Bank Transfer, Mobile Money (0933003304, 0118787874)
            </p>
        </div>
    </div>
</div>

<!-- Request Modal -->
<div id="upgradeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold mb-4">Request Plan Upgrade</h3>
        <form method="POST" action="{{ route('subscription.request') }}">
            @csrf
            <input type="hidden" name="plan" id="selectedPlan">
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Phone Number *</label>
                <input type="text" name="phone" required value="{{ old('phone') }}"
                    class="w-full px-3 py-2 border rounded focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="0933003304">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Message (Optional)</label>
                <textarea name="message" rows="3"
                    class="w-full px-3 py-2 border rounded focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Any special requirements or questions...">{{ old('message') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeModal()" class="flex-1 bg-gray-200 text-gray-800 py-2 rounded hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(plan) {
    document.getElementById('selectedPlan').value = plan;
    document.getElementById('upgradeModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('upgradeModal').classList.add('hidden');
}
</script>
@endsection
