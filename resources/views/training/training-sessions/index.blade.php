@extends('layouts.app')

@section('title', 'Training Sessions')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-gray-900">Training Sessions</h1>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('training.training-sessions.export', ['format' => 'excel']) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </a>
                    <a href="{{ route('training.training-sessions.export', ['format' => 'csv']) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-file-csv mr-2"></i>Export CSV
                    </a>
                    <a href="{{ route('training.training-sessions.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Schedule Session
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('training.training-sessions.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Status</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter</label>
                    <select name="upcoming" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Sessions</option>
                        <option value="1" {{ request('upcoming') == '1' ? 'selected' : '' }}>Upcoming Only</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Sessions List -->
        <div class="bg-white rounded-lg shadow">
            @if($sessions->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($sessions as $session)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            <a href="{{ route('training.training-sessions.show', $session) }}" class="hover:text-indigo-600">
                                                {{ $session->title }}
                                            </a>
                                        </h3>
                                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                                            {{ $session->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $session->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $session->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $session->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                                        </span>
                                    </div>
                                    @if($session->trainingPlan)
                                        <p class="text-sm text-gray-600 mb-2">
                                            <i class="fas fa-link mr-1"></i>
                                            <a href="{{ route('training.training-plans.show', $session->trainingPlan) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $session->trainingPlan->title }}
                                            </a>
                                        </p>
                                    @endif
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span>
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $session->scheduled_start->format('M j, Y') }}
                                        </span>
                                        <span>
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $session->scheduled_start->format('g:i A') }} - {{ $session->scheduled_end->format('g:i A') }}
                                        </span>
                                        @if($session->instructor)
                                            <span>
                                                <i class="fas fa-user mr-1"></i>
                                                {{ $session->instructor->name }}
                                            </span>
                                        @endif
                                        @if($session->location_name)
                                            <span>
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $session->location_name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('training.training-sessions.show', $session) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        View <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $sessions->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-calendar text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Training Sessions Found</h3>
                    <p class="text-gray-500 mb-6">Schedule training sessions to deliver training.</p>
                    <a href="{{ route('training.training-sessions.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Schedule Session
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
