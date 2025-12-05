@extends('layouts.app')

@section('title', 'Edit 5S Audit')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('housekeeping.5s-audits.show', $audit) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit 5S Audit</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('housekeeping.5s-audits.update', $audit) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">5S Audit Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="audit_date" class="block text-sm font-medium text-black mb-1">Audit Date *</label>
                    <input type="date" id="audit_date" name="audit_date" required value="{{ old('audit_date', $audit->audit_date->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="area" class="block text-sm font-medium text-black mb-1">Area *</label>
                    <input type="text" id="area" name="area" required value="{{ old('area', $audit->area) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="sort_score" class="block text-sm font-medium text-black mb-1">Sort (Seiri) Score</label>
                    <input type="number" id="sort_score" name="sort_score" min="0" max="100" value="{{ old('sort_score', $audit->sort_score) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="set_score" class="block text-sm font-medium text-black mb-1">Set in Order (Seiton) Score</label>
                    <input type="number" id="set_score" name="set_score" min="0" max="100" value="{{ old('set_score', $audit->set_score) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="shine_score" class="block text-sm font-medium text-black mb-1">Shine (Seiso) Score</label>
                    <input type="number" id="shine_score" name="shine_score" min="0" max="100" value="{{ old('shine_score', $audit->shine_score) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="standardize_score" class="block text-sm font-medium text-black mb-1">Standardize (Seiketsu) Score</label>
                    <input type="number" id="standardize_score" name="standardize_score" min="0" max="100" value="{{ old('standardize_score', $audit->standardize_score) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="sustain_score" class="block text-sm font-medium text-black mb-1">Sustain (Shitsuke) Score</label>
                    <input type="number" id="sustain_score" name="sustain_score" min="0" max="100" value="{{ old('sustain_score', $audit->sustain_score) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-black mb-1">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="scheduled" {{ old('status', $audit->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="in_progress" {{ old('status', $audit->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status', $audit->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-black mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('notes', $audit->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('housekeeping.5s-audits.show', $audit) }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Update Audit
            </button>
        </div>
    </form>
</div>
@endsection

