@extends('layouts.app')

@section('title', 'Toolbox Talk Templates')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('toolbox-talks.index') }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Talks
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Toolbox Talk Templates</h1>
                </div>
                <a href="{{ route('toolbox-talks.templates.create') }}" class="btn-primary rounded">
                    <i class="fas fa-plus mr-2"></i>Create Template
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($templates->isEmpty())
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-file-alt text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Templates Found</h3>
                <p class="text-gray-600 mb-6">Create your first template to speed up talk creation.</p>
                <a href="{{ route('toolbox-talks.templates.create') }}" class="btn-primary rounded inline-block">
                    <i class="fas fa-plus mr-2"></i>Create Template
                </a>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Topic</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($templates as $template)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $template->name }}</p>
                                        @if($template->description)
                                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($template->description, 50) }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst(str_replace('_', ' ', $template->talk_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $template->duration_minutes }} min</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $template->topic ? $template->topic->title : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $template->usage_count }} times</td>
                                <td class="px-6 py-4">
                                    @if($template->is_active)
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex space-x-3">
                                        <a href="{{ route('toolbox-talks.templates.show', $template) }}" 
                                           class="text-blue-600 hover:text-blue-700" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('toolbox-talks.templates.edit', $template) }}" 
                                           class="text-yellow-600 hover:text-yellow-700" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('toolbox-talks.templates.destroy', $template) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this template?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

