@extends('layouts.app')

@section('title', 'Edit Document Template')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('documents.templates.show', $template) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Document Template</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('documents.templates.update', $template) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Template Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-black mb-1">Template Name *</label>
                    <input type="text" id="name" name="name" required value="{{ old('name', $template->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="template_type" class="block text-sm font-medium text-black mb-1">Template Type *</label>
                    <select id="template_type" name="template_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="policy" {{ old('template_type', $template->template_type) == 'policy' ? 'selected' : '' }}>Policy</option>
                        <option value="procedure" {{ old('template_type', $template->template_type) == 'procedure' ? 'selected' : '' }}>Procedure</option>
                        <option value="form" {{ old('template_type', $template->template_type) == 'form' ? 'selected' : '' }}>Form</option>
                        <option value="checklist" {{ old('template_type', $template->template_type) == 'checklist' ? 'selected' : '' }}>Checklist</option>
                        <option value="report" {{ old('template_type', $template->template_type) == 'report' ? 'selected' : '' }}>Report</option>
                        <option value="other" {{ old('template_type', $template->template_type) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}
                               class="mr-2">
                        <span class="text-sm text-black">Active</span>
                    </label>
                </div>

                <div>
                    <label for="file" class="block text-sm font-medium text-black mb-1">Update Template File</label>
                    <input type="file" id="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @if($template->file_path)
                        <p class="mt-1 text-xs text-gray-500">Current file exists</p>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description', $template->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('documents.templates.show', $template) }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Update Template
            </button>
        </div>
    </form>
</div>
@endsection

