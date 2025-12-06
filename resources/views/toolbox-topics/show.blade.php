@extends('layouts.app')

@section('title', $topic->title)

@php
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'fa-home'],
    ['label' => 'Toolbox Topics', 'url' => route('toolbox-topics.index'), 'icon' => 'fa-book'],
    ['label' => $topic->title, 'url' => null, 'active' => true]
];
@endphp

@section('content')
<script>
    // Track recent item view
    document.addEventListener('DOMContentLoaded', function() {
        fetch('{{ route("recent-items.track") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                title: '{{ $topic->title }}',
                url: '{{ route("toolbox-topics.show", $topic) }}',
                module: 'Toolbox Topics',
                icon: 'fa-book'
            })
        });
    });
</script>
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('toolbox-topics.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Topics
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $topic->title }}</h1>
                    <p class="text-sm text-gray-500">
                        <span class="px-2 py-1 text-xs rounded-full {{ $topic->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $topic->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($topic->is_mandatory)
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 ml-2">Mandatory</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex space-x-3">
                <x-print-button />
                <a href="{{ route('toolbox-topics.edit', $topic) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <form action="{{ route('toolbox-topics.duplicate', $topic) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-copy mr-2"></i>Duplicate
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $topic->category) }}</dd>
                    </div>
                    @if($topic->subcategory)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Subcategory</dt>
                        <dd class="mt-1 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $topic->subcategory) }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Difficulty Level</dt>
                        <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $topic->difficulty_level }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Estimated Duration</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $topic->estimated_duration_minutes }} minutes</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Seasonal Relevance</dt>
                        <dd class="mt-1 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $topic->seasonal_relevance) }}</dd>
                    </div>
                    @if($topic->representer)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Representer</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $topic->representer->name }}</dd>
                    </div>
                    @endif
                    @if($topic->creator)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $topic->creator->name }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $topic->created_at->format('M d, Y') }}</dd>
                    </div>
                    @if($topic->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $topic->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Learning Objectives -->
            @if($topic->learning_objectives && count($topic->learning_objectives) > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Learning Objectives</h2>
                <ul class="space-y-2">
                    @foreach($topic->learning_objectives as $objective)
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 mt-1 mr-2"></i>
                            <span class="text-sm text-gray-900">{{ $objective }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Key Talking Points -->
            @if($topic->key_talking_points)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Key Talking Points</h2>
                <div class="text-sm text-gray-900 whitespace-pre-wrap">{{ $topic->key_talking_points }}</div>
            </div>
            @endif

            <!-- Real World Examples -->
            @if($topic->real_world_examples)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Real World Examples</h2>
                <div class="text-sm text-gray-900 whitespace-pre-wrap">{{ $topic->real_world_examples }}</div>
            </div>
            @endif

            <!-- Regulatory References -->
            @if($topic->regulatory_references)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Regulatory References</h2>
                <div class="text-sm text-gray-900 whitespace-pre-wrap">{{ $topic->regulatory_references }}</div>
            </div>
            @endif

            <!-- Required Materials -->
            @if($topic->required_materials && count($topic->required_materials) > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Required Materials</h2>
                <ul class="space-y-2">
                    @foreach($topic->required_materials as $material)
                        <li class="flex items-start">
                            <i class="fas fa-check text-blue-600 mt-1 mr-2"></i>
                            <span class="text-sm text-gray-900">{{ $material }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Recent Toolbox Talks -->
            @if($topic->toolboxTalks && $topic->toolboxTalks->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Toolbox Talks</h2>
                <div class="space-y-4">
                    @foreach($topic->toolboxTalks as $talk)
                        <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">{{ $talk->title }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-calendar mr-1"></i>{{ $talk->scheduled_date->format('M d, Y') }}
                                        @if($talk->department)
                                            <span class="ml-3"><i class="fas fa-building mr-1"></i>{{ $talk->department->name }}</span>
                                        @endif
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $talk->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($talk->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                       ($talk->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst($talk->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Usage Statistics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Usage Statistics</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Uses</dt>
                        <dd class="mt-1 text-2xl font-bold text-gray-900">{{ $usageStats['total_uses'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Recent Uses</dt>
                        <dd class="mt-1 text-2xl font-bold text-gray-900">{{ $usageStats['recent_uses'] }}</dd>
                    </div>
                    @if($usageStats['avg_feedback'])
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Average Feedback</dt>
                        <dd class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($usageStats['avg_feedback'], 1) }}/5</dd>
                    </div>
                    @endif
                    @if($usageStats['effectiveness'])
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Effectiveness Rating</dt>
                        <dd class="mt-1 text-2xl font-bold text-gray-900">{{ $usageStats['effectiveness'] }}/5</dd>
                    </div>
                    @endif
                    @if($usageStats['last_used'])
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Used</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $usageStats['last_used']->format('M d, Y') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Department Usage -->
            @if($departmentUsage && $departmentUsage->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Department Usage</h2>
                <div class="space-y-3">
                    @foreach($departmentUsage as $usage)
                        @if(isset($usage['department']) && $usage['department'])
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-900">{{ $usage['department']->name }}</span>
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ $usage['count'] }} uses</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                <div class="space-y-2">
                    <a href="{{ route('toolbox-talks.create', ['topic_id' => $topic->id]) }}" class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Schedule Talk
                    </a>
                    <a href="{{ route('toolbox-topics.edit', $topic) }}" class="block w-full px-4 py-2 bg-gray-600 text-white text-center rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Topic
                    </a>
                    <form action="{{ route('toolbox-topics.duplicate', $topic) }}" method="POST" class="inline w-full">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-gray-200 text-gray-700 text-center rounded-lg hover:bg-gray-300 transition-colors">
                            <i class="fas fa-copy mr-2"></i>Duplicate
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

