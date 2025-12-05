@extends('layouts.app')

@section('title', 'Create 5S Audit')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('housekeeping.5s-audits.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create 5S Audit</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('housekeeping.5s-audits.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">5S Audit Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="audit_date" class="block text-sm font-medium text-black mb-1">Audit Date *</label>
                    <input type="date" id="audit_date" name="audit_date" required value="{{ old('audit_date', date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('audit_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="area" class="block text-sm font-medium text-black mb-1">Area *</label>
                    <input type="text" id="area" name="area" required value="{{ old('area') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('area')
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
                    <label for="audited_by" class="block text-sm font-medium text-black mb-1">Audited By *</label>
                    <select id="audited_by" name="audited_by" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Auditor</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('audited_by', auth()->id()) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('audited_by')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sort_score" class="block text-sm font-medium text-black mb-1">Sort (Seiri) Score (0-100)</label>
                    <input type="number" id="sort_score" name="sort_score" min="0" max="100" value="{{ old('sort_score') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="set_score" class="block text-sm font-medium text-black mb-1">Set in Order (Seiton) Score (0-100)</label>
                    <input type="number" id="set_score" name="set_score" min="0" max="100" value="{{ old('set_score') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="shine_score" class="block text-sm font-medium text-black mb-1">Shine (Seiso) Score (0-100)</label>
                    <input type="number" id="shine_score" name="shine_score" min="0" max="100" value="{{ old('shine_score') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="standardize_score" class="block text-sm font-medium text-black mb-1">Standardize (Seiketsu) Score (0-100)</label>
                    <input type="number" id="standardize_score" name="standardize_score" min="0" max="100" value="{{ old('standardize_score') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="sustain_score" class="block text-sm font-medium text-black mb-1">Sustain (Shitsuke) Score (0-100)</label>
                    <input type="number" id="sustain_score" name="sustain_score" min="0" max="100" value="{{ old('sustain_score') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="overall_rating" class="block text-sm font-medium text-black mb-1">Overall Rating</label>
                    <select id="overall_rating" name="overall_rating"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Rating</option>
                        <option value="excellent" {{ old('overall_rating') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                        <option value="good" {{ old('overall_rating') == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="needs_improvement" {{ old('overall_rating') == 'needs_improvement' ? 'selected' : '' }}>Needs Improvement</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-black mb-1">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-black mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('housekeeping.5s-audits.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Audit
            </button>
        </div>
    </form>
</div>
@endsection

