@extends('layouts.app')

@section('title', $template->name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('toolbox-talks.templates.index') }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Templates
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $template->name }}</h1>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('toolbox-talks.templates.edit', $template) }}" 
                       class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <a href="{{ route('toolbox-talks.create', ['template_id' => $template->id]) }}" 
                       class="btn-primary rounded">
                        <i class="fas fa-plus mr-2"></i>Use Template
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-6">
            <!-- Template Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Template Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Name</h3>
                        <p class="text-lg font-medium text-gray-900">{{ $template->name }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Status</h3>
                        @if($template->is_active)
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                        @endif
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Talk Type</h3>
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst(str_replace('_', ' ', $template->talk_type)) }}
                        </span>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Duration</h3>
                        <p class="text-gray-900">{{ $template->duration_minutes }} minutes</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Topic</h3>
                        <p class="text-gray-900">{{ $template->topic ? $template->topic->title : 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Usage Count</h3>
                        <p class="text-gray-900">{{ $template->usage_count }} times</p>
                    </div>
                </div>
                
                @if($template->description)
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Description</h3>
                        <p class="text-gray-900">{{ $template->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Template Content -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Template Content</h2>
                
                @if($template->title)
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Default Title</h3>
                        <p class="text-gray-900">{{ $template->title }}</p>
                    </div>
                @endif
                
                @if($template->description_content)
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Default Description</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $template->description_content }}</p>
                    </div>
                @endif
                
                @if($template->key_points)
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Key Points</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $template->key_points }}</p>
                    </div>
                @endif
                
                @if($template->regulatory_references)
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Regulatory References</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $template->regulatory_references }}</p>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('toolbox-talks.templates.index') }}" 
                   class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Back to List
                </a>
                <a href="{{ route('toolbox-talks.templates.edit', $template) }}" 
                   class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('toolbox-talks.create', ['template_id' => $template->id]) }}" 
                   class="btn-primary rounded">
                    <i class="fas fa-plus mr-2"></i>Use Template
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

