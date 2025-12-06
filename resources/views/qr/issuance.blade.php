@extends('layouts.app')

@section('title', 'PPE Issuance - QR Scan')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-6">
                <i class="fas fa-qrcode text-6xl text-purple-600 mb-4"></i>
                <h1 class="text-3xl font-bold text-gray-900">PPE Issuance Details</h1>
                <p class="text-gray-600 mt-2">Scanned via QR Code</p>
            </div>

            <div class="border-t border-b border-gray-200 py-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Reference Number</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $item->reference_number }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Status</h3>
                        <span class="px-3 py-1 text-sm rounded-full 
                            {{ $item->status === 'active' ? 'bg-green-100 text-green-800' : 
                               ($item->status === 'returned' ? 'bg-gray-100 text-gray-800' : 
                               ($item->status === 'expired' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">PPE Item</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $item->ppeItem->name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Issued To</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $item->issuedTo->name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Quantity</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $item->quantity }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Issue Date</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $item->issue_date->format('M d, Y') }}</p>
                    </div>
                    @if($item->expiry_date)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Expiry Date</h3>
                        <p class="text-lg font-semibold {{ $item->expiry_date->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $item->expiry_date->format('M d, Y') }}
                        </p>
                    </div>
                    @endif
                    @if($item->department)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Department</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $item->department->name }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex justify-center space-x-4">
                <button onclick="updateSystem('check')" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                    <i class="fas fa-check mr-2"></i>Mark as Checked
                </button>
                <a href="{{ route('ppe.issuances.show', $item) }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-eye mr-2"></i>View Full Details
                </a>
                <a href="{{ route('ppe.inspections.create', ['ppe_issuance_id' => $item->id]) }}" class="bg-yellow-600 text-white px-6 py-3 rounded-lg hover:bg-yellow-700">
                    <i class="fas fa-search mr-2"></i>Inspect Item
                </a>
            </div>

            <script>
            function updateSystem(action) {
                const url = '{{ route("qr.scan", ["type" => "issuance", "id" => $item->id]) }}' + '&action=' + action;
                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message || 'System updated successfully!');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating system. Please try again.');
                });
            }
            </script>
        </div>
    </div>
</div>
@endsection

