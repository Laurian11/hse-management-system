@extends('layouts.app')

@section('title', 'Create PPE Inspection')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('ppe.inspections.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Create PPE Inspection</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('ppe.inspections.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Inspection Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="ppe_issuance_id" class="block text-sm font-medium text-gray-700 mb-1">PPE Issuance *</label>
                    <select id="ppe_issuance_id" name="ppe_issuance_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Select Issuance</option>
                        @foreach($issuances as $issuance)
                            <option value="{{ $issuance->id }}" {{ (old('ppe_issuance_id', $selectedIssuance?->id) == $issuance->id) ? 'selected' : '' }}>
                                {{ $issuance->ppeItem->name }} - {{ $issuance->issuedTo->name }} ({{ $issuance->reference_number }})
                            </option>
                        @endforeach
                    </select>
                    @error('ppe_issuance_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="inspection_date" class="block text-sm font-medium text-gray-700 mb-1">Inspection Date *</label>
                    <input type="date" id="inspection_date" name="inspection_date" required value="{{ old('inspection_date', date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    @error('inspection_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="inspection_type" class="block text-sm font-medium text-gray-700 mb-1">Inspection Type *</label>
                    <select id="inspection_type" name="inspection_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="scheduled" {{ old('inspection_type', 'scheduled') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="pre_use" {{ old('inspection_type') == 'pre_use' ? 'selected' : '' }}>Pre-Use</option>
                        <option value="post_use" {{ old('inspection_type') == 'post_use' ? 'selected' : '' }}>Post-Use</option>
                        <option value="damage_report" {{ old('inspection_type') == 'damage_report' ? 'selected' : '' }}>Damage Report</option>
                        <option value="random" {{ old('inspection_type') == 'random' ? 'selected' : '' }}>Random</option>
                    </select>
                </div>

                <div>
                    <label for="condition" class="block text-sm font-medium text-gray-700 mb-1">Condition *</label>
                    <select id="condition" name="condition" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="excellent" {{ old('condition', 'good') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                        <option value="good" {{ old('condition', 'good') == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                        <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                        <option value="unsafe" {{ old('condition') == 'unsafe' ? 'selected' : '' }}>Unsafe</option>
                        <option value="damaged" {{ old('condition') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                    </select>
                </div>

                <div>
                    <label for="action_taken" class="block text-sm font-medium text-gray-700 mb-1">Action Taken *</label>
                    <select id="action_taken" name="action_taken" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="approved" {{ old('action_taken', 'approved') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="repair" {{ old('action_taken') == 'repair' ? 'selected' : '' }}>Repair</option>
                        <option value="replace" {{ old('action_taken') == 'replace' ? 'selected' : '' }}>Replace</option>
                        <option value="dispose" {{ old('action_taken') == 'dispose' ? 'selected' : '' }}>Dispose</option>
                        <option value="quarantine" {{ old('action_taken') == 'quarantine' ? 'selected' : '' }}>Quarantine</option>
                    </select>
                </div>

                <div>
                    <label for="next_inspection_date" class="block text-sm font-medium text-gray-700 mb-1">Next Inspection Date</label>
                    <input type="date" id="next_inspection_date" name="next_inspection_date" value="{{ old('next_inspection_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>

                <div class="md:col-span-2">
                    <label for="findings" class="block text-sm font-medium text-gray-700 mb-1">Findings</label>
                    <textarea id="findings" name="findings" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">{{ old('findings') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="defects" class="block text-sm font-medium text-gray-700 mb-1">Defects</label>
                    <textarea id="defects" name="defects" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">{{ old('defects') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="defect_photos" class="block text-sm font-medium text-gray-700 mb-1">Defect Photos</label>
                    <input type="file" id="defect_photos" name="defect_photos[]" multiple accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <p class="mt-1 text-xs text-gray-500">Upload photos of defects (max 5MB each)</p>
                </div>

                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_compliant" name="is_compliant" value="1" {{ old('is_compliant', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                        <label for="is_compliant" class="ml-2 block text-sm text-gray-900">Is Compliant</label>
                    </div>
                </div>

                <div class="md:col-span-2" id="non_compliance_fields" style="display: none;">
                    <label for="non_compliance_reason" class="block text-sm font-medium text-gray-700 mb-1">Non-Compliance Reason</label>
                    <textarea id="non_compliance_reason" name="non_compliance_reason" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">{{ old('non_compliance_reason') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('ppe.inspections.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">Create Inspection</button>
        </div>
    </form>
</div>

<script>
document.getElementById('is_compliant').addEventListener('change', function() {
    const nonComplianceFields = document.getElementById('non_compliance_fields');
    if (!this.checked) {
        nonComplianceFields.style.display = 'block';
    } else {
        nonComplianceFields.style.display = 'none';
    }
});
</script>
@endsection

