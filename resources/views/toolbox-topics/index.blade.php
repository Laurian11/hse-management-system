@extends('layouts.app')

@section('title', 'Toolbox Topics')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-book text-blue-600 text-2xl"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Toolbox Topics</h1>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('toolbox-topics.library') }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-book-open mr-2"></i>Library View
                    </a>
                    <a href="{{ route('toolbox-topics.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Create Topic
                    </a>
                </div>
            </div>
        </div>
    </div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded flex items-center justify-center">
                    <i class="fas fa-book text-blue-600 text-lg"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</span>
            </div>
            <h3 class="text-sm font-medium text-gray-900">Total Topics</h3>
            <p class="text-xs text-gray-500">Available topics</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</span>
            </div>
            <h3 class="text-sm font-medium text-gray-900">Active Topics</h3>
            <p class="text-xs text-gray-500">Currently available</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded flex items-center justify-center">
                    <i class="fas fa-star text-orange-600 text-lg"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $stats['mandatory'] }}</span>
            </div>
            <h3 class="text-sm font-medium text-gray-900">Mandatory Topics</h3>
            <p class="text-xs text-gray-500">Required training</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded flex items-center justify-center">
                    <i class="fas fa-trophy text-purple-600 text-lg"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $topics->where('average_feedback_score', '>=', 4)->count() }}</span>
            </div>
            <h3 class="text-sm font-medium text-gray-900">Highly Rated</h3>
            <p class="text-xs text-gray-500">4+ star rating</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Search topics...">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Categories</option>
                    <option value="safety" {{ request('category') == 'safety' ? 'selected' : '' }}>Safety</option>
                    <option value="health" {{ request('category') == 'health' ? 'selected' : '' }}>Health</option>
                    <option value="environment" {{ request('category') == 'environment' ? 'selected' : '' }}>Environment</option>
                    <option value="equipment" {{ request('category') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                    <option value="procedures" {{ request('category') == 'procedures' ? 'selected' : '' }}>Procedures</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty</label>
                <select name="difficulty" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Levels</option>
                    <option value="basic" {{ request('difficulty') == 'basic' ? 'selected' : '' }}>Basic</option>
                    <option value="intermediate" {{ request('difficulty') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="advanced" {{ request('difficulty') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </div>
        </div>
    </div>

    <!-- Topics List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Topic</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topics as $topic)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $topic->title }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($topic->description, 80) }}</div>
                                    @if($topic->is_mandatory)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 mt-1">
                                            <i class="fas fa-star mr-1"></i>Mandatory
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $topic->category === 'safety' ? 'bg-red-100 text-red-800' : 
                                   ($topic->category === 'health' ? 'bg-green-100 text-green-800' :
                                   ($topic->category === 'environment' ? 'bg-blue-100 text-blue-800' :
                                   ($topic->category === 'equipment' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800'))) }}">
                                {{ ucfirst($topic->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $topic->difficulty === 'basic' ? 'bg-green-100 text-green-800' : 
                                   ($topic->difficulty === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($topic->difficulty) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $topic->estimated_duration }} min
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($topic->average_feedback_score)
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-900 mr-1">{{ number_format($topic->average_feedback_score, 1) }}</span>
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $topic->average_feedback_score ? '' : 'text-gray-300' }} text-xs"></i>
                                        @endfor
                                    </div>
                                </div>
                            @else
                                <span class="text-sm text-gray-500">Not rated</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $topic->usage_count ?? 0 }} times
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('toolbox-topics.show', $topic) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <a href="{{ route('toolbox-topics.edit', $topic) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No topics found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($topics->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                {{ $topics->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
