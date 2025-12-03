@extends('layouts.app')

@section('title', 'Edit Investigation')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('incidents.investigations.show', [$incident, $investigation]) }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Investigation
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Edit Investigation</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('incidents.investigations.update', [$incident, $investigation]) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Investigation Facts -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Investigation Facts</h2>
            <div class="space-y-6">
                <div>
                    <label for="what_happened" class="block text-sm font-medium text-gray-700 mb-1">What Happened?</label>
                    <textarea id="what_happened" name="what_happened" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Describe what happened">{{ old('what_happened', $investigation->what_happened) }}</textarea>
                </div>
                <div>
                    <label for="when_occurred" class="block text-sm font-medium text-gray-700 mb-1">When Did It Occur?</label>
                    <textarea id="when_occurred" name="when_occurred" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Exact time and date details">{{ old('when_occurred', $investigation->when_occurred) }}</textarea>
                </div>
                <div>
                    <label for="where_occurred" class="block text-sm font-medium text-gray-700 mb-1">Where Did It Occur?</label>
                    <textarea id="where_occurred" name="where_occurred" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Location details">{{ old('where_occurred', $investigation->where_occurred) }}</textarea>
                </div>
                <div>
                    <label for="who_involved" class="block text-sm font-medium text-gray-700 mb-1">Who Was Involved?</label>
                    <textarea id="who_involved" name="who_involved" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="List all people involved">{{ old('who_involved', $investigation->who_involved) }}</textarea>
                </div>
                <div>
                    <label for="how_occurred" class="block text-sm font-medium text-gray-700 mb-1">How Did It Occur?</label>
                    <textarea id="how_occurred" name="how_occurred" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Sequence of events leading to the incident">{{ old('how_occurred', $investigation->how_occurred) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Immediate Causes -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Immediate Causes & Contributing Factors</h2>
            <div class="space-y-6">
                <div>
                    <label for="immediate_causes" class="block text-sm font-medium text-gray-700 mb-1">Immediate Causes</label>
                    <textarea id="immediate_causes" name="immediate_causes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Direct causes of the incident">{{ old('immediate_causes', $investigation->immediate_causes) }}</textarea>
                </div>
                <div>
                    <label for="contributing_factors" class="block text-sm font-medium text-gray-700 mb-1">Contributing Factors</label>
                    <textarea id="contributing_factors" name="contributing_factors" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Factors that contributed to the incident">{{ old('contributing_factors', $investigation->contributing_factors) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Conditions at Time -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Conditions at Time of Incident</h2>
            <div class="space-y-6">
                <div>
                    <label for="environmental_conditions" class="block text-sm font-medium text-gray-700 mb-1">Environmental Conditions</label>
                    <textarea id="environmental_conditions" name="environmental_conditions" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Weather, lighting, temperature, etc.">{{ old('environmental_conditions', $investigation->environmental_conditions) }}</textarea>
                </div>
                <div>
                    <label for="equipment_conditions" class="block text-sm font-medium text-gray-700 mb-1">Equipment Conditions</label>
                    <textarea id="equipment_conditions" name="equipment_conditions" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Condition of equipment involved">{{ old('equipment_conditions', $investigation->equipment_conditions) }}</textarea>
                </div>
                <div>
                    <label for="procedures_followed" class="block text-sm font-medium text-gray-700 mb-1">Procedures Followed</label>
                    <textarea id="procedures_followed" name="procedures_followed" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Were proper procedures followed?">{{ old('procedures_followed', $investigation->procedures_followed) }}</textarea>
                </div>
                <div>
                    <label for="training_received" class="block text-sm font-medium text-gray-700 mb-1">Training Received</label>
                    <textarea id="training_received" name="training_received" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Relevant training received by involved personnel">{{ old('training_received', $investigation->training_received) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Findings & Recommendations -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Findings & Recommendations</h2>
            <div class="space-y-6">
                <div>
                    <label for="key_findings" class="block text-sm font-medium text-gray-700 mb-1">Key Findings</label>
                    <textarea id="key_findings" name="key_findings" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Key findings from the investigation">{{ old('key_findings', $investigation->key_findings) }}</textarea>
                </div>
                <div>
                    <label for="recommendations" class="block text-sm font-medium text-gray-700 mb-1">Recommendations</label>
                    <textarea id="recommendations" name="recommendations" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Recommendations to prevent recurrence">{{ old('recommendations', $investigation->recommendations) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('incidents.investigations.show', [$incident, $investigation]) }}" 
                   class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Update Investigation
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

