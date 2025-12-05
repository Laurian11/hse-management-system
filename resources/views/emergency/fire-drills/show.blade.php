@extends('layouts.app')

@section('title', 'Fire Drill: ' . $fireDrill->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('emergency.fire-drills.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $fireDrill->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $fireDrill->location }} - {{ $fireDrill->drill_date->format('M d, Y') }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('emergency.fire-drills.edit', $fireDrill) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Drill Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                        <dd class="mt-1 text-sm text-black">{{ $fireDrill->drill_date->format('M d, Y') }} {{ $fireDrill->drill_time }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                        <dd class="mt-1 text-sm text-black">{{ $fireDrill->location }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Drill Type</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst($fireDrill->drill_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Conducted By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $fireDrill->conductedBy->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Participants</dt>
                        <dd class="mt-1 text-sm text-black">{{ $fireDrill->total_participants }} / {{ $fireDrill->expected_participants }}</dd>
                    </div>
                    @if($fireDrill->evacuation_time)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Evacuation Time</dt>
                        <dd class="mt-1 text-sm text-black">{{ $fireDrill->evacuation_time }}</dd>
                    </div>
                    @endif
                    @if($fireDrill->overall_result)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Overall Result</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $fireDrill->overall_result == 'excellent' || $fireDrill->overall_result == 'good' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $fireDrill->overall_result == 'needs_improvement' || $fireDrill->overall_result == 'poor' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $fireDrill->overall_result)) }}
                            </span>
                        </dd>
                    </div>
                    @endif
                    @if($fireDrill->objectives)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Objectives</dt>
                        <dd class="mt-1 text-sm text-black">{{ $fireDrill->objectives }}</dd>
                    </div>
                    @endif
                    @if($fireDrill->scenario)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Scenario</dt>
                        <dd class="mt-1 text-sm text-black">{{ $fireDrill->scenario }}</dd>
                    </div>
                    @endif
                    @if($fireDrill->observations)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Observations</dt>
                        <dd class="mt-1 text-sm text-black">{{ $fireDrill->observations }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Strengths & Weaknesses -->
            @if($fireDrill->strengths || $fireDrill->weaknesses)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Analysis</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($fireDrill->strengths)
                    <div>
                        <h3 class="text-md font-medium text-black mb-2">Strengths</h3>
                        <ul class="list-disc list-inside text-sm text-black space-y-1">
                            @foreach($fireDrill->strengths as $strength)
                                <li>{{ $strength }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @if($fireDrill->weaknesses)
                    <div>
                        <h3 class="text-md font-medium text-black mb-2">Weaknesses</h3>
                        <ul class="list-disc list-inside text-sm text-black space-y-1">
                            @foreach($fireDrill->weaknesses as $weakness)
                                <li>{{ $weakness }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Recommendations -->
            @if($fireDrill->recommendations)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Recommendations</h2>
                <ul class="list-disc list-inside text-sm text-black space-y-1">
                    @foreach($fireDrill->recommendations as $recommendation)
                        <li>{{ $recommendation }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Follow-up -->
            @if($fireDrill->requires_follow_up)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Follow-up</h2>
                <dl class="space-y-3">
                    @if($fireDrill->follow_up_actions)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Actions</dt>
                        <dd class="mt-1 text-sm text-black">{{ $fireDrill->follow_up_actions }}</dd>
                    </div>
                    @endif
                    @if($fireDrill->follow_up_due_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $fireDrill->follow_up_due_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300 {{ $fireDrill->follow_up_completed ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' }}">
                                {{ $fireDrill->follow_up_completed ? 'Completed' : 'Pending' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

