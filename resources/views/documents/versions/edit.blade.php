@extends('layouts.app')

@section('title', 'Edit Document Version')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('documents.versions.show', $version) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Document Version</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('documents.versions.update', $version) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Version Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="version_number" class="block text-sm font-medium text-black mb-1">Version Number *</label>
                    <input type="text" id="version_number" name="version_number" required value="{{ old('version_number', $version->version_number) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-black mb-1">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="draft" {{ old('status', $version->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="under_review" {{ old('status', $version->status) == 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="approved" {{ old('status', $version->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('status', $version->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_current" value="1" {{ old('is_current', $version->is_current) ? 'checked' : '' }}
                               class="mr-2">
                        <span class="text-sm text-black">Mark as Current Version</span>
                    </label>
                </div>

                <div>
                    <label for="file" class="block text-sm font-medium text-black mb-1">Update Version File</label>
                    <input type="file" id="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @if($version->file_name)
                        <p class="mt-1 text-xs text-gray-500">Current: {{ $version->file_name }}</p>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <label for="change_summary" class="block text-sm font-medium text-black mb-1">Change Summary</label>
                    <textarea id="change_summary" name="change_summary" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('change_summary', $version->change_summary) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('documents.versions.show', $version) }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Update Version
            </button>
        </div>
    </form>
</div>
@endsection

