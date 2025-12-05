@extends('layouts.app')

@section('title', 'Generate PPE Compliance Report')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('ppe.reports.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Generate Compliance Report</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('ppe.reports.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Report Parameters</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">Report Type *</label>
                    <select id="report_type" name="report_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="usage">Usage Report</option>
                        <option value="expiry">Expiry Report</option>
                        <option value="inspection">Inspection Report</option>
                        <option value="compliance">Compliance Report</option>
                    </select>
                </div>

                <div>
                    <label for="scope" class="block text-sm font-medium text-gray-700 mb-1">Scope *</label>
                    <select id="scope" name="scope" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="company" {{ old('scope', 'company') == 'company' ? 'selected' : '' }}>Company</option>
                        <option value="department" {{ old('scope') == 'department' ? 'selected' : '' }}>Department</option>
                        <option value="user" {{ old('scope') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="item" {{ old('scope') == 'item' ? 'selected' : '' }}>Item</option>
                    </select>
                </div>

                <div id="department_field" style="display: none;">
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select id="department_id" name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="user_field" style="display: none;">
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">User</label>
                    <select id="user_id" name="user_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="item_field" style="display: none;">
                    <label for="ppe_item_id" class="block text-sm font-medium text-gray-700 mb-1">PPE Item</label>
                    <select id="ppe_item_id" name="ppe_item_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Select Item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('ppe_item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="report_period_start" class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                    <input type="date" id="report_period_start" name="report_period_start" required value="{{ old('report_period_start', date('Y-m-d', strtotime('-30 days'))) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>

                <div>
                    <label for="report_period_end" class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
                    <input type="date" id="report_period_end" name="report_period_end" required value="{{ old('report_period_end', date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('ppe.reports.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">Generate Report</button>
        </div>
    </form>
</div>

<script>
document.getElementById('scope').addEventListener('change', function() {
    const departmentField = document.getElementById('department_field');
    const userField = document.getElementById('user_field');
    const itemField = document.getElementById('item_field');
    
    departmentField.style.display = 'none';
    userField.style.display = 'none';
    itemField.style.display = 'none';
    
    if (this.value === 'department') {
        departmentField.style.display = 'block';
    } else if (this.value === 'user') {
        userField.style.display = 'block';
    } else if (this.value === 'item') {
        itemField.style.display = 'block';
    }
});
</script>
@endsection

