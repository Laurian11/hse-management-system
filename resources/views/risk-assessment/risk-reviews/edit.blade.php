@extends('layouts.app')

@section('title', 'Edit Risk Review')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('risk-assessment.risk-reviews.show', $riskReview) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Risk Review</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('risk-assessment.risk-reviews.update', $riskReview) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Review Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Review Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Risk Assessment</label>
                    <p class="text-black">{{ $riskReview->riskAssessment->reference_number }} - {{ $riskReview->riskAssessment->title }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Review Type</label>
                    <p class="text-black">{{ ucfirst(str_replace('_', ' ', $riskReview->review_type)) }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-black mb-1">Due Date *</label>
                        <input type="date" id="due_date" name="due_date" required 
                               value="{{ old('due_date', $riskReview->due_date->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        @error('due_date')
                            <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-black mb-1">Assigned To</label>
                        <select id="assigned_to" name="assigned_to"
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to', $riskReview->assigned_to) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-black mb-1">Status *</label>
                        <select id="status" name="status" required
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="scheduled" {{ old('status', $riskReview->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="in_progress" {{ old('status', $riskReview->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="overdue" {{ old('status', $riskReview->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            <option value="cancelled" {{ old('status', $riskReview->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('risk-assessment.risk-reviews.show', $riskReview) }}" 
               class="px-6 py-2 border border-gray-300 text-black hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white hover:bg-[#0052A3]">
                Update Review
            </button>
        </div>
    </form>
</div>
@endsection

