@extends('layouts.app')

@section('title', 'Create Compliance Audit')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('compliance.audits.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Compliance Audit</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('compliance.audits.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Audit Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="audit_title" class="block text-sm font-medium text-black mb-1">Audit Title *</label>
                    <input type="text" id="audit_title" name="audit_title" required value="{{ old('audit_title') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('audit_title')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="audit_type" class="block text-sm font-medium text-black mb-1">Audit Type *</label>
                    <select id="audit_type" name="audit_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Type</option>
                        <option value="internal" {{ old('audit_type') == 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="external" {{ old('audit_type') == 'external' ? 'selected' : '' }}>External</option>
                        <option value="iso_14001" {{ old('audit_type') == 'iso_14001' ? 'selected' : '' }}>ISO 14001</option>
                        <option value="iso_45001" {{ old('audit_type') == 'iso_45001' ? 'selected' : '' }}>ISO 45001</option>
                        <option value="iso_9001" {{ old('audit_type') == 'iso_9001' ? 'selected' : '' }}>ISO 9001</option>
                        <option value="regulatory" {{ old('audit_type') == 'regulatory' ? 'selected' : '' }}>Regulatory</option>
                        <option value="other" {{ old('audit_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('audit_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="audit_date" class="block text-sm font-medium text-black mb-1">Audit Date *</label>
                    <input type="date" id="audit_date" name="audit_date" required value="{{ old('audit_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('audit_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="audit_status" class="block text-sm font-medium text-black mb-1">Status *</label>
                    <select id="audit_status" name="audit_status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="scheduled" {{ old('audit_status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="in_progress" {{ old('audit_status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('audit_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('audit_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('audit_status')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="auditor_id" class="block text-sm font-medium text-black mb-1">Auditor</label>
                    <select id="auditor_id" name="auditor_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Auditor</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('auditor_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="standard" class="block text-sm font-medium text-black mb-1">Standard</label>
                    <input type="text" id="standard" name="standard" value="{{ old('standard') }}"
                           placeholder="e.g., ISO 14001:2015"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="file" class="block text-sm font-medium text-black mb-1">Audit Report</label>
                    <input type="file" id="file" name="file" accept=".pdf"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    <p class="mt-1 text-xs text-gray-500">PDF format only (Max: 10MB)</p>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('compliance.audits.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Audit
            </button>
        </div>
    </form>
</div>
@endsection

