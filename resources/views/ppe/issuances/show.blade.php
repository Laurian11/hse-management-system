@extends('layouts.app')

@section('title', 'PPE Issuance Details')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('ppe.issuances.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-gray-900">PPE Issuance: {{ $issuance->reference_number }}</h1>
            </div>
            @if($issuance->status === 'active')
                <button onclick="document.getElementById('returnForm').classList.toggle('hidden')" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700">
                    <i class="fas fa-undo mr-2"></i>Return Item
                </button>
            @endif
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Issuance Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Issuance Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Reference Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $issuance->reference_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">PPE Item</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $issuance->ppeItem->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Issued To</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $issuance->issuedTo->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $issuance->quantity }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Issue Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $issuance->issue_date->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs rounded-full {{ $issuance->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($issuance->status) }}
                            </span>
                        </dd>
                    </div>
                    @if($issuance->expiry_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Expiry Date</dt>
                        <dd class="mt-1 text-sm {{ $issuance->expiry_date->isPast() ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                            {{ $issuance->expiry_date->format('M d, Y') }}
                            @if($issuance->expiry_date->isPast())
                                <span class="ml-2 text-xs">(Expired)</span>
                            @endif
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Return Form (Hidden by default) -->
            @if($issuance->status === 'active')
            <div id="returnForm" class="hidden bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Return Item</h2>
                <form action="{{ route('ppe.issuances.return', $issuance) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="actual_return_date" class="block text-sm font-medium text-gray-700 mb-1">Return Date *</label>
                            <input type="date" id="actual_return_date" name="actual_return_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        </div>
                        <div>
                            <label for="return_condition" class="block text-sm font-medium text-gray-700 mb-1">Condition *</label>
                            <select id="return_condition" name="return_condition" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                                <option value="damaged">Damaged</option>
                                <option value="lost">Lost</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="return_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea id="return_notes" name="return_notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"></textarea>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Submit Return</button>
                    </div>
                </form>
            </div>
            @endif

            <!-- Inspections -->
            @if($issuance->inspections->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Inspections</h2>
                <div class="space-y-3">
                    @foreach($issuance->inspections as $inspection)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $inspection->inspection_date->format('M d, Y') }}</p>
                                <p class="text-sm text-gray-600">Condition: {{ ucfirst($inspection->condition) }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $inspection->is_compliant ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $inspection->is_compliant ? 'Compliant' : 'Non-Compliant' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('ppe.inspections.create', ['ppe_issuance_id' => $issuance->id]) }}" class="block w-full text-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        <i class="fas fa-search mr-2"></i>Inspect
                    </a>
                    <a href="{{ route('ppe.items.show', $issuance->ppeItem) }}" class="block w-full text-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        View Item
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

