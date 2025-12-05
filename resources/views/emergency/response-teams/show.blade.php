@extends('layouts.app')

@section('title', 'Emergency Response Team: ' . $team->name)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('emergency.response-teams.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $team->name }}</h1>
                    <p class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $team->team_type)) }} Team</p>
                </div>
            </div>
            <div>
                <a href="{{ route('emergency.response-teams.edit', $team) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-gray-300 p-6">
        <h2 class="text-lg font-semibold text-black mb-4">Team Details</h2>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Team Name</dt>
                <dd class="mt-1 text-sm text-black">{{ $team->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Team Type</dt>
                <dd class="mt-1 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $team->team_type)) }}</dd>
            </div>
            @if($team->teamLeader)
            <div>
                <dt class="text-sm font-medium text-gray-500">Team Leader</dt>
                <dd class="mt-1 text-sm text-black">{{ $team->teamLeader->name }}</dd>
            </div>
            @endif
            @if($team->deputyLeader)
            <div>
                <dt class="text-sm font-medium text-gray-500">Deputy Leader</dt>
                <dd class="mt-1 text-sm text-black">{{ $team->deputyLeader->name }}</dd>
            </div>
            @endif
            <div>
                <dt class="text-sm font-medium text-gray-500">Team Members</dt>
                <dd class="mt-1 text-sm text-black">{{ count($team->team_members ?? []) }} members</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Availability</dt>
                <dd class="mt-1 text-sm text-black">{{ $team->is_24_7 ? '24/7' : 'Scheduled' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                    <span class="px-2 py-1 text-xs font-semibold border border-gray-300 {{ $team->is_active ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-gray-500 border-gray-300' }}">
                        {{ $team->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </dd>
            </div>
            @if($team->next_training_date)
            <div>
                <dt class="text-sm font-medium text-gray-500">Next Training Date</dt>
                <dd class="mt-1 text-sm text-black">{{ $team->next_training_date->format('M d, Y') }}</dd>
            </div>
            @endif
            @if($team->description)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Description</dt>
                <dd class="mt-1 text-sm text-black">{{ $team->description }}</dd>
            </div>
            @endif
            @if($team->responsibilities)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Responsibilities</dt>
                <dd class="mt-1 text-sm text-black">{{ $team->responsibilities }}</dd>
            </div>
            @endif
            @if($team->notes)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                <dd class="mt-1 text-sm text-black">{{ $team->notes }}</dd>
            </div>
            @endif
        </dl>
    </div>
</div>
@endsection

