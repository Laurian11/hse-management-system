@extends('layouts.app')

@section('title', 'Topic Library')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-book text-blue-600 text-2xl"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Topic Library</h1>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('toolbox-topics.index') }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-list mr-2"></i>All Topics
                    </a>
                    <a href="{{ route('toolbox-topics.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>New Topic
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('toolbox-topics.library') }}" class="space-y-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search topics, keywords, or content..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div>
                        <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <select name="difficulty" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Levels</option>
                            <option value="basic" {{ request('difficulty') == 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="intermediate" {{ request('difficulty') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ request('difficulty') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Seasonal Recommendations -->
        <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-lg p-6 mb-6 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        <i class="fas fa-sun text-yellow-500 mr-2"></i>
                        Seasonal Recommendations
                    </h3>
                    <p class="text-gray-600">Topics specifically relevant for current season</p>
                </div>
                <div class="text-2xl">
                    @php
                        $month = date('n');
                    @endphp
                    @if(in_array($month, [12, 1, 2]))
                        <i class="fas fa-snowflake text-blue-500"></i>
                    @elseif(in_array($month, [3, 4, 5]))
                        <i class="fas fa-sun text-yellow-500"></i>
                    @elseif(in_array($month, [6, 7, 8]))
                        <i class="fas fa-cloud-rain text-blue-600"></i>
                    @else
                        <i class="fas fa-temperature-high text-red-500"></i>
                    @endif
                </div>
            </div>
        </div>

        <!-- Topics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($topics as $topic)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow overflow-hidden">
                    <!-- Topic Header -->
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    @switch($topic->category)
                                        @case('safety')
                                            <i class="fas fa-shield-alt text-blue-600"></i>
                                            @break
                                        @case('health')
                                            <i class="fas fa-heartbeat text-red-600"></i>
                                            @break
                                        @case('environment')
                                            <i class="fas fa-leaf text-green-600"></i>
                                            @break
                                        @default
                                            <i class="fas fa-comments text-gray-600"></i>
                                    @endswitch
                                    <span class="text-xs font-medium text-gray-500 uppercase">{{ $topic->category }}</span>
                                    @if($topic->is_mandatory)
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Mandatory</span>
                                    @endif
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $topic->title }}</h3>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $topic->description }}</p>
                            </div>
                        </div>

                        <!-- Topic Details -->
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Duration:</span>
                                <span class="text-gray-900">{{ $topic->estimated_duration_minutes }} min</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Difficulty:</span>
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    @switch($topic->difficulty_level)
                                        @case('basic')
                                            bg-green-100 text-green-800
                                            @break
                                        @case('intermediate')
                                            bg-yellow-100 text-yellow-800
                                            @break
                                        @case('advanced')
                                            bg-red-100 text-red-800
                                            @break
                                    @endswitch">
                                    {{ ucfirst($topic->difficulty_level) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Used:</span>
                                <span class="text-gray-900">{{ $topic->usage_count }} times</span>
                            </div>
                            @if($topic->average_feedback_score)
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Rating:</span>
                                    <div class="flex items-center">
                                        <span class="text-yellow-500">★</span>
                                        <span class="text-gray-900 ml-1">{{ number_format($topic->average_feedback_score, 1) }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Learning Objectives -->
                        @if($topic->learning_objectives)
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Learning Objectives:</h4>
                                <ul class="text-xs text-gray-600 space-y-1">
                                    @foreach(array_slice($topic->learning_objectives, 0, 3) as $objective)
                                        <li class="flex items-start">
                                            <i class="fas fa-check text-green-500 mr-1 mt-0.5"></i>
                                            <span>{{ $objective }}</span>
                                        </li>
                                    @endforeach
                                    @if(count($topic->learning_objectives) > 3)
                                        <li class="text-gray-400 italic">+{{ count($topic->learning_objectives) - 3 }} more...</li>
                                    @endif
                                </ul>
                            </div>
                        @endif

                        <!-- Seasonal Badge -->
                        @if($topic->seasonal_relevance !== 'all_year')
                            <div class="mt-3">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $topic->seasonal_relevance)) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="bg-gray-50 px-6 py-3 border-t">
                        <div class="flex space-x-2">
                            <a href="{{ route('toolbox-topics.show', $topic) }}" class="flex-1 text-center px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <button onclick="useTopic({{ $topic->id }})" class="flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                                <i class="fas fa-play mr-1"></i>Use
                            </button>
                            <button onclick="duplicateTopic({{ $topic->id }})" class="px-3 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 transition-colors">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-book-open text-gray-400 text-5xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No topics found</p>
                    <p class="text-gray-400 mb-4">Try adjusting your filters or create a new topic</p>
                    <a href="{{ route('toolbox-topics.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Create Topic
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($topics->hasPages())
            <div class="mt-8">
                {{ $topics->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function useTopic(topicId) {
        // Redirect to create toolbox talk with pre-selected topic
        window.location.href = `/toolbox-talks/create?topic_id=${topicId}`;
    }
    
    function duplicateTopic(topicId) {
        if (confirm('Are you sure you want to duplicate this topic?')) {
            // Submit form to duplicate topic
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/toolbox-topics/${topicId}/duplicate`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    // Auto-search functionality
    let searchTimeout;
    document.querySelector('input[name="search"]').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            e.target.form.submit();
        }, 500);
    });
    
    // Category quick filter
    document.querySelectorAll('[data-category]').forEach(button => {
        button.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            const categorySelect = document.querySelector('select[name="category"]');
            categorySelect.value = category;
            categorySelect.form.submit();
        });
    });
</script>
@endpush
