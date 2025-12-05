@extends('layouts.app')

@section('title', 'Edit HSE Document')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('documents.hse-documents.show', $document) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit HSE Document</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('documents.hse-documents.update', $document) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Document Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-black mb-1">Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title', $document->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('title')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="document_type" class="block text-sm font-medium text-black mb-1">Document Type *</label>
                    <select id="document_type" name="document_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="policy" {{ old('document_type', $document->document_type) == 'policy' ? 'selected' : '' }}>Policy</option>
                        <option value="procedure" {{ old('document_type', $document->document_type) == 'procedure' ? 'selected' : '' }}>Procedure</option>
                        <option value="form" {{ old('document_type', $document->document_type) == 'form' ? 'selected' : '' }}>Form</option>
                        <option value="template" {{ old('document_type', $document->document_type) == 'template' ? 'selected' : '' }}>Template</option>
                        <option value="manual" {{ old('document_type', $document->document_type) == 'manual' ? 'selected' : '' }}>Manual</option>
                        <option value="guideline" {{ old('document_type', $document->document_type) == 'guideline' ? 'selected' : '' }}>Guideline</option>
                        <option value="standard" {{ old('document_type', $document->document_type) == 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="other" {{ old('document_type', $document->document_type) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-black mb-1">Category *</label>
                    <select id="category" name="category" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="safety" {{ old('category', $document->category) == 'safety' ? 'selected' : '' }}>Safety</option>
                        <option value="health" {{ old('category', $document->category) == 'health' ? 'selected' : '' }}>Health</option>
                        <option value="environmental" {{ old('category', $document->category) == 'environmental' ? 'selected' : '' }}>Environmental</option>
                        <option value="quality" {{ old('category', $document->category) == 'quality' ? 'selected' : '' }}>Quality</option>
                        <option value="compliance" {{ old('category', $document->category) == 'compliance' ? 'selected' : '' }}>Compliance</option>
                        <option value="training" {{ old('category', $document->category) == 'training' ? 'selected' : '' }}>Training</option>
                        <option value="emergency" {{ old('category', $document->category) == 'emergency' ? 'selected' : '' }}>Emergency</option>
                        <option value="other" {{ old('category', $document->category) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-black mb-1">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="draft" {{ old('status', $document->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="under_review" {{ old('status', $document->status) == 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="approved" {{ old('status', $document->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="active" {{ old('status', $document->status) == 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                </div>

                <div>
                    <label for="access_level" class="block text-sm font-medium text-black mb-1">Access Level *</label>
                    <select id="access_level" name="access_level" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="public" {{ old('access_level', $document->access_level) == 'public' ? 'selected' : '' }}>Public</option>
                        <option value="restricted" {{ old('access_level', $document->access_level) == 'restricted' ? 'selected' : '' }}>Restricted</option>
                        <option value="confidential" {{ old('access_level', $document->access_level) == 'confidential' ? 'selected' : '' }}>Confidential</option>
                        <option value="classified" {{ old('access_level', $document->access_level) == 'classified' ? 'selected' : '' }}>Classified</option>
                    </select>
                </div>

                <div>
                    <label for="file" class="block text-sm font-medium text-black mb-1">Update Document File</label>
                    <input type="file" id="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @if($document->file_name)
                        <p class="mt-1 text-xs text-gray-500">Current: {{ $document->file_name }}</p>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description', $document->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('documents.hse-documents.show', $document) }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Update Document
            </button>
        </div>
    </form>
</div>
@endsection

