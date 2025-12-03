@extends('layouts.app')

@section('title', 'Edit CAPA')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('incidents.capas.show', [$incident, $capa]) }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to CAPA
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Edit CAPA</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
            <div>
                <h3 class="font-semibold text-blue-900">Incident: {{ $incident->reference_number }}</h3>
                <p class="text-sm text-blue-700 mt-1">{{ $incident->title }}</p>
                <p class="text-sm text-blue-700 mt-1">CAPA: {{ $capa->reference_number }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('incidents.capas.update', [$incident, $capa]) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- CAPA Basic Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Action Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Action Type</label>
                    <input type="text" value="{{ ucfirst($capa->action_type) }} Action" readonly
                           class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                    <select id="priority" name="priority" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="low" {{ old('priority', $capa->priority) == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $capa->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $capa->priority) == 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('priority', $capa->priority) == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">CAPA Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title', $capa->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Brief title for this action">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Detailed description of the action to be taken">{{ old('description', $capa->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Assignment and Timeline -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Assignment & Timeline</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                    <input type="text" value="{{ $capa->assignedTo->name ?? 'Unassigned' }}" readonly
                           class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date *</label>
                    <input type="date" id="due_date" name="due_date" required 
                           value="{{ old('due_date', $capa->due_date ? $capa->due_date->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Implementation Plan -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Implementation</h2>
            <div class="space-y-6">
                <div>
                    <label for="implementation_plan" class="block text-sm font-medium text-gray-700 mb-1">Implementation Plan</label>
                    <textarea id="implementation_plan" name="implementation_plan" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Step-by-step plan for implementing this action">{{ old('implementation_plan', $capa->implementation_plan) }}</textarea>
                </div>
                <div>
                    <label for="progress_notes" class="block text-sm font-medium text-gray-700 mb-1">Progress Notes</label>
                    <textarea id="progress_notes" name="progress_notes" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Update on progress made">{{ old('progress_notes', $capa->progress_notes) }}</textarea>
                </div>
                <div>
                    <label for="challenges_encountered" class="block text-sm font-medium text-gray-700 mb-1">Challenges Encountered</label>
                    <textarea id="challenges_encountered" name="challenges_encountered" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Any challenges or obstacles faced">{{ old('challenges_encountered', $capa->challenges_encountered) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('incidents.capas.show', [$incident, $capa]) }}" 
                   class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Update CAPA
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

