@extends('layouts.app')

@section('title', 'CAPA Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('incidents.show', $incident) }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Incident
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">CAPA: {{ $capa->reference_number }}</h1>
                </div>
                <div class="flex space-x-3">
                    {!! $capa->getStatusBadge() !!}
                    {!! $capa->getPriorityBadge() !!}
                    @if($capa->status !== 'closed')
                        <a href="{{ route('incidents.capas.edit', [$incident, $capa]) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- CAPA Details -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">CAPA Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Action Type</h3>
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                        {{ $capa->isCorrective() ? 'bg-red-100 text-red-800' : 
                           ($capa->isPreventive() ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                        {{ ucfirst($capa->action_type) }} Action
                    </span>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Title</h3>
                    <p class="text-lg font-medium text-gray-900">{{ $capa->title }}</p>
                </div>
                <div class="md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Description</h3>
                    <p class="text-gray-900 whitespace-pre-wrap">{{ $capa->description }}</p>
                </div>
                @if($capa->root_cause_addressed)
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Root Cause Addressed</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $capa->root_cause_addressed }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Assignment & Timeline -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Assignment & Timeline</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Assigned To</h3>
                    <p class="text-gray-900">{{ $capa->assignedTo->name ?? 'Unassigned' }}</p>
                </div>
                @if($capa->department)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Department</h3>
                        <p class="text-gray-900">{{ $capa->department->name }}</p>
                    </div>
                @endif
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Due Date</h3>
                    <p class="text-gray-900">
                        {{ $capa->due_date->format('M j, Y') }}
                        @if($capa->isOverdue())
                            <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Overdue</span>
                        @elseif($capa->getDaysRemaining() !== null)
                            <span class="ml-2 text-sm text-gray-500">({{ $capa->getDaysRemaining() }} days remaining)</span>
                        @endif
                    </p>
                </div>
                @if($capa->started_at)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Started On</h3>
                        <p class="text-gray-900">{{ $capa->started_at->format('M j, Y') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Resources & Cost -->
        @if($capa->required_resources || $capa->estimated_cost)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Resources & Budget</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($capa->required_resources)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Required Resources</h3>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $capa->required_resources }}</p>
                        </div>
                    @endif
                    @if($capa->estimated_cost)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Estimated Cost</h3>
                            <p class="text-gray-900 text-lg font-semibold">${{ number_format($capa->estimated_cost, 2) }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Implementation -->
        @if($capa->implementation_plan || $capa->progress_notes)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Implementation</h2>
                <div class="space-y-6">
                    @if($capa->implementation_plan)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Implementation Plan</h3>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $capa->implementation_plan }}</p>
                        </div>
                    @endif
                    @if($capa->progress_notes)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Progress Notes</h3>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $capa->progress_notes }}</p>
                        </div>
                    @endif
                    @if($capa->challenges_encountered)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Challenges Encountered</h3>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $capa->challenges_encountered }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Verification -->
        @if($capa->isVerified() || $capa->status === 'under_review')
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Verification</h2>
                <div class="space-y-4">
                    @if($capa->verified_by)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Verified By</h3>
                            <p class="text-gray-900">{{ $capa->verifiedBy->name }}</p>
                            <p class="text-sm text-gray-500">{{ $capa->verified_at->format('M j, Y g:i A') }}</p>
                        </div>
                    @endif
                    @if($capa->verification_notes)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Verification Notes</h3>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $capa->verification_notes }}</p>
                        </div>
                    @endif
                    @if($capa->effectiveness)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Effectiveness</h3>
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                                {{ $capa->effectiveness === 'effective' ? 'bg-green-100 text-green-800' : 
                                   ($capa->effectiveness === 'partially_effective' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $capa->effectiveness)) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-end space-x-3">
                @if($capa->status === 'pending')
                    <form action="{{ route('incidents.capas.start', [$incident, $capa]) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-play mr-2"></i>Start CAPA
                        </button>
                    </form>
                @endif
                @if($capa->status === 'in_progress')
                    <form action="{{ route('incidents.capas.complete', [$incident, $capa]) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>Complete CAPA
                        </button>
                    </form>
                @endif
                @if($capa->status === 'under_review')
                    <button onclick="showVerifyModal()" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-check-circle mr-2"></i>Verify CAPA
                    </button>
                @endif
                @if($capa->isVerified() && !$capa->isClosed())
                    <form action="{{ route('incidents.capas.close', [$incident, $capa]) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-lock mr-2"></i>Close CAPA
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Verify Modal -->
<div id="verifyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Verify CAPA</h3>
        </div>
        <form action="{{ route('incidents.capas.verify', [$incident, $capa]) }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Effectiveness *</label>
                    <select name="effectiveness" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Effectiveness</option>
                        <option value="effective">Effective</option>
                        <option value="partially_effective">Partially Effective</option>
                        <option value="ineffective">Ineffective</option>
                        <option value="not_yet_measured">Not Yet Measured</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Verification Notes</label>
                    <textarea name="verification_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Effectiveness Evidence</label>
                    <textarea name="effectiveness_evidence" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Evidence supporting the effectiveness rating"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="hideVerifyModal()" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-check-circle mr-2"></i>Verify
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showVerifyModal() {
    document.getElementById('verifyModal').classList.remove('hidden');
}

function hideVerifyModal() {
    document.getElementById('verifyModal').classList.add('hidden');
}
</script>
@endsection

