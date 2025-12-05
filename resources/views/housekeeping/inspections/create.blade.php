@extends('layouts.app')

@section('title', 'Create Housekeeping Inspection')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('housekeeping.inspections.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Housekeeping Inspection</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('housekeeping.inspections.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Inspection Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="inspection_date" class="block text-sm font-medium text-black mb-1">Inspection Date *</label>
                    <input type="date" id="inspection_date" name="inspection_date" required value="{{ old('inspection_date', date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('inspection_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-black mb-1">Location *</label>
                    <input type="text" id="location" name="location" required value="{{ old('location') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('location')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-black mb-1">Department</label>
                    <select id="department_id" name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="inspected_by" class="block text-sm font-medium text-black mb-1">Inspected By *</label>
                    <select id="inspected_by" name="inspected_by" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Inspector</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('inspected_by', auth()->id()) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('inspected_by')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="overall_rating" class="block text-sm font-medium text-black mb-1">Overall Rating</label>
                    <select id="overall_rating" name="overall_rating"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Rating</option>
                        <option value="excellent" {{ old('overall_rating') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                        <option value="good" {{ old('overall_rating') == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="satisfactory" {{ old('overall_rating') == 'satisfactory' ? 'selected' : '' }}>Satisfactory</option>
                        <option value="needs_improvement" {{ old('overall_rating') == 'needs_improvement' ? 'selected' : '' }}>Needs Improvement</option>
                        <option value="poor" {{ old('overall_rating') == 'poor' ? 'selected' : '' }}>Poor</option>
                    </select>
                </div>

                <div>
                    <label for="score" class="block text-sm font-medium text-black mb-1">Score (0-100)</label>
                    <input type="number" id="score" name="score" min="0" max="100" value="{{ old('score') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-black mb-1">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="follow_up_required" {{ old('status') == 'follow_up_required' ? 'selected' : '' }}>Follow-up Required</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="follow_up_date" class="block text-sm font-medium text-black mb-1">Follow-up Date</label>
                    <input type="date" id="follow_up_date" name="follow_up_date" value="{{ old('follow_up_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="follow_up_assigned_to" class="block text-sm font-medium text-black mb-1">Follow-up Assigned To</label>
                    <select id="follow_up_assigned_to" name="follow_up_assigned_to"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Person</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('follow_up_assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="findings" class="block text-sm font-medium text-black mb-1">Findings</label>
                    <textarea id="findings" name="findings" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('findings') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="recommendations" class="block text-sm font-medium text-black mb-1">Recommendations</label>
                    <textarea id="recommendations" name="recommendations" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('recommendations') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="corrective_actions" class="block text-sm font-medium text-black mb-1">Corrective Actions</label>
                    <textarea id="corrective_actions" name="corrective_actions" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('corrective_actions') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-black mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('housekeeping.inspections.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Inspection
            </button>
        </div>
    </form>
</div>
@endsection

