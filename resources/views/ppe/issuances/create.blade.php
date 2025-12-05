@extends('layouts.app')

@section('title', 'Issue PPE')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('ppe.issuances.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Issue PPE</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('ppe.issuances.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Issuance Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="ppe_item_id" class="block text-sm font-medium text-gray-700 mb-1">PPE Item *</label>
                    <select id="ppe_item_id" name="ppe_item_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Select Item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ (old('ppe_item_id', $selectedItem?->id) == $item->id) ? 'selected' : '' }} 
                                    data-available="{{ $item->available_quantity }}">
                                {{ $item->name }} (Available: {{ $item->available_quantity }})
                            </option>
                        @endforeach
                    </select>
                    @error('ppe_item_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Issue To *</label>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2 mb-2">
                            <input type="radio" id="issue_type_single" name="issue_type" value="single" checked 
                                   onchange="toggleIssueType()" class="text-teal-600 focus:ring-teal-500">
                            <label for="issue_type_single" class="text-sm text-gray-700">Single User</label>
                            <input type="radio" id="issue_type_bulk" name="issue_type" value="bulk" 
                                   onchange="toggleIssueType()" class="ml-4 text-teal-600 focus:ring-teal-500">
                            <label for="issue_type_bulk" class="text-sm text-gray-700">Multiple Users (Bulk)</label>
                        </div>
                        
                        <!-- Single User Selection -->
                        <div id="single_user_selection">
                            <select id="issued_to" name="issued_to" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('issued_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Bulk User Selection -->
                        <div id="bulk_user_selection" style="display: none;">
                            <select id="users" name="users[]" multiple size="8"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->department->name ?? 'No Department' }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Hold Ctrl (Windows) or Cmd (Mac) to select multiple users</p>
                        </div>
                    </div>
                    @error('issued_to')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('users')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                    <input type="number" id="quantity" name="quantity" required value="{{ old('quantity', 1) }}" min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-1">Issue Date *</label>
                    <input type="date" id="issue_date" name="issue_date" required value="{{ old('issue_date', date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    @error('issue_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select id="department_id" name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="initial_condition" class="block text-sm font-medium text-gray-700 mb-1">Initial Condition *</label>
                    <select id="initial_condition" name="initial_condition" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="new" {{ old('initial_condition', 'new') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="good" {{ old('initial_condition') == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="fair" {{ old('initial_condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                        <option value="poor" {{ old('initial_condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Issuance</label>
                    <textarea id="reason" name="reason" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">{{ old('reason') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('ppe.issuances.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" id="submit_btn" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Issue PPE</button>
        </div>
    </form>
</div>

<script>
function toggleIssueType() {
    const issueType = document.querySelector('input[name="issue_type"]:checked').value;
    const singleSelection = document.getElementById('single_user_selection');
    const bulkSelection = document.getElementById('bulk_user_selection');
    const issuedToField = document.getElementById('issued_to');
    const usersField = document.getElementById('users');
    
    if (issueType === 'bulk') {
        singleSelection.style.display = 'none';
        bulkSelection.style.display = 'block';
        issuedToField.removeAttribute('required');
        usersField.setAttribute('required', 'required');
        document.getElementById('submit_btn').textContent = 'Issue PPE (Bulk)';
    } else {
        singleSelection.style.display = 'block';
        bulkSelection.style.display = 'none';
        issuedToField.setAttribute('required', 'required');
        usersField.removeAttribute('required');
        document.getElementById('submit_btn').textContent = 'Issue PPE';
    }
}

// Update form action based on issue type
document.querySelector('form').addEventListener('submit', function(e) {
    const issueType = document.querySelector('input[name="issue_type"]:checked').value;
    if (issueType === 'bulk') {
        this.action = '{{ route("ppe.issuances.bulk-issue") }}';
    } else {
        this.action = '{{ route("ppe.issuances.store") }}';
    }
});
</script>
@endsection

