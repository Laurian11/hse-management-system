@extends('layouts.app')

@section('title', 'Training Needs Analysis')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-gray-900">Training Needs Analysis</h1>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('training.training-needs.export', ['format' => 'excel']) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </a>
                    <a href="{{ route('training.training-needs.export', ['format' => 'csv']) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-file-csv mr-2"></i>Export CSV
                    </a>
                    <a href="{{ route('training.training-needs.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Identify Training Need
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('training.training-needs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Status</option>
                        <option value="identified" {{ request('status') == 'identified' ? 'selected' : '' }}>Identified</option>
                        <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Validated</option>
                        <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trigger Source</label>
                    <select name="trigger_source" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Sources</option>
                        <option value="risk_assessment" {{ request('trigger_source') == 'risk_assessment' ? 'selected' : '' }}>Risk Assessment</option>
                        <option value="incident_rca" {{ request('trigger_source') == 'incident_rca' ? 'selected' : '' }}>Incident RCA</option>
                        <option value="new_hire" {{ request('trigger_source') == 'new_hire' ? 'selected' : '' }}>New Hire</option>
                        <option value="certificate_expiry" {{ request('trigger_source') == 'certificate_expiry' ? 'selected' : '' }}>Certificate Expiry</option>
                        <option value="manual" {{ request('trigger_source') == 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Training Needs List -->
        <div class="bg-white rounded-lg shadow">
            @if($tnas->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($tnas as $tna)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            <a href="{{ route('training.training-needs.show', $tna) }}" class="hover:text-indigo-600">
                                                {{ $tna->training_title }}
                                            </a>
                                        </h3>
                                        {!! $tna->getStatusBadge() !!}
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($tna->training_description, 150) }}</p>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span>
                                            <i class="fas fa-tag mr-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $tna->trigger_source)) }}
                                        </span>
                                        <span>
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ ucfirst($tna->priority) }} Priority
                                        </span>
                                        @if($tna->is_mandatory)
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                                                Mandatory
                                            </span>
                                        @endif
                                        @if($tna->is_regulatory)
                                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs">
                                                Regulatory
                                            </span>
                                        @endif
                                        <span>
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $tna->created_at->format('M j, Y') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('training.training-needs.show', $tna) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        View <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $tnas->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-graduation-cap text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Training Needs Found</h3>
                    <p class="text-gray-500 mb-6">Training needs will appear here when identified from incidents, risk assessments, or created manually.</p>
                    <a href="{{ route('training.training-needs.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Identify Training Need
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
