@extends('layouts.app')

@section('title', 'Create Root Cause Analysis')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('incidents.show', $incident) }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Incident
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Root Cause Analysis</h1>
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
            </div>
        </div>
    </div>

    <form action="{{ route('incidents.rca.store', $incident) }}" method="POST" class="space-y-6" id="rcaForm">
        @csrf

        <!-- Analysis Type -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Analysis Type *</h2>
            <select id="analysis_type" name="analysis_type" required onchange="toggleAnalysisType()"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select Analysis Type</option>
                <option value="5_whys" {{ old('analysis_type') == '5_whys' ? 'selected' : '' }}>5 Whys</option>
                <option value="fishbone" {{ old('analysis_type') == 'fishbone' ? 'selected' : '' }}>Fishbone (Ishikawa)</option>
                <option value="taproot" {{ old('analysis_type') == 'taproot' ? 'selected' : '' }}>Taproot Analysis</option>
                <option value="fault_tree" {{ old('analysis_type') == 'fault_tree' ? 'selected' : '' }}>Fault Tree Analysis</option>
                <option value="custom" {{ old('analysis_type') == 'custom' ? 'selected' : '' }}>Custom</option>
            </select>
            @error('analysis_type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- 5 Whys Analysis -->
        <div id="five_whys_section" class="bg-white rounded-lg shadow p-6 hidden">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">5 Whys Analysis</h2>
            <p class="text-sm text-gray-600 mb-6">Ask "Why?" five times to drill down to the root cause.</p>
            
            <div class="space-y-4">
                <div>
                    <label for="why_1" class="block text-sm font-medium text-gray-700 mb-1">Why 1 (First Level)</label>
                    <textarea id="why_1" name="why_1" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Why did this happen?">{{ old('why_1') }}</textarea>
                </div>
                <div>
                    <label for="why_2" class="block text-sm font-medium text-gray-700 mb-1">Why 2 (Second Level)</label>
                    <textarea id="why_2" name="why_2" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Why did that happen?">{{ old('why_2') }}</textarea>
                </div>
                <div>
                    <label for="why_3" class="block text-sm font-medium text-gray-700 mb-1">Why 3 (Third Level)</label>
                    <textarea id="why_3" name="why_3" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Why did that happen?">{{ old('why_3') }}</textarea>
                </div>
                <div>
                    <label for="why_4" class="block text-sm font-medium text-gray-700 mb-1">Why 4 (Fourth Level)</label>
                    <textarea id="why_4" name="why_4" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Why did that happen?">{{ old('why_4') }}</textarea>
                </div>
                <div>
                    <label for="why_5" class="block text-sm font-medium text-gray-700 mb-1">Why 5 (Fifth Level)</label>
                    <textarea id="why_5" name="why_5" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Why did that happen?">{{ old('why_5') }}</textarea>
                </div>
                <div>
                    <label for="root_cause" class="block text-sm font-medium text-gray-700 mb-1">Root Cause Identified</label>
                    <textarea id="root_cause" name="root_cause" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="The underlying root cause based on the 5 Whys analysis">{{ old('root_cause') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Fishbone Analysis -->
        <div id="fishbone_section" class="bg-white rounded-lg shadow p-6 hidden">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Fishbone (Ishikawa) Analysis</h2>
            <p class="text-sm text-gray-600 mb-6">Analyze causes across different categories.</p>
            
            <div class="space-y-4">
                <div>
                    <label for="human_factors" class="block text-sm font-medium text-gray-700 mb-1">Human Factors</label>
                    <textarea id="human_factors" name="human_factors" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Human-related causes (training, fatigue, error, etc.)">{{ old('human_factors') }}</textarea>
                </div>
                <div>
                    <label for="organizational_factors" class="block text-sm font-medium text-gray-700 mb-1">Organizational Factors</label>
                    <textarea id="organizational_factors" name="organizational_factors" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Organizational causes (policies, culture, management, etc.)">{{ old('organizational_factors') }}</textarea>
                </div>
                <div>
                    <label for="technical_factors" class="block text-sm font-medium text-gray-700 mb-1">Technical Factors</label>
                    <textarea id="technical_factors" name="technical_factors" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Technical causes (design, maintenance, technology, etc.)">{{ old('technical_factors') }}</textarea>
                </div>
                <div>
                    <label for="environmental_factors" class="block text-sm font-medium text-gray-700 mb-1">Environmental Factors</label>
                    <textarea id="environmental_factors" name="environmental_factors" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Environmental causes (weather, lighting, noise, etc.)">{{ old('environmental_factors') }}</textarea>
                </div>
                <div>
                    <label for="procedural_factors" class="block text-sm font-medium text-gray-700 mb-1">Procedural Factors</label>
                    <textarea id="procedural_factors" name="procedural_factors" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Procedural causes (SOPs, work instructions, etc.)">{{ old('procedural_factors') }}</textarea>
                </div>
                <div>
                    <label for="equipment_factors" class="block text-sm font-medium text-gray-700 mb-1">Equipment Factors</label>
                    <textarea id="equipment_factors" name="equipment_factors" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Equipment-related causes (failure, maintenance, etc.)">{{ old('equipment_factors') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Comprehensive Analysis (for all types) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Comprehensive Analysis</h2>
            <div class="space-y-4">
                <div>
                    <label for="direct_cause" class="block text-sm font-medium text-gray-700 mb-1">Direct Cause</label>
                    <textarea id="direct_cause" name="direct_cause" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="The immediate cause of the incident">{{ old('direct_cause') }}</textarea>
                </div>
                <div>
                    <label for="contributing_causes" class="block text-sm font-medium text-gray-700 mb-1">Contributing Causes</label>
                    <textarea id="contributing_causes" name="contributing_causes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Factors that contributed to the incident">{{ old('contributing_causes') }}</textarea>
                </div>
                <div>
                    <label for="root_causes" class="block text-sm font-medium text-gray-700 mb-1">Root Causes</label>
                    <textarea id="root_causes" name="root_causes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Underlying systemic causes">{{ old('root_causes') }}</textarea>
                </div>
                <div>
                    <label for="systemic_failures" class="block text-sm font-medium text-gray-700 mb-1">Systemic Failures</label>
                    <textarea id="systemic_failures" name="systemic_failures" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="System or process failures that allowed this to happen">{{ old('systemic_failures') }}</textarea>
                </div>
                <div>
                    <label for="prevention_possible" class="block text-sm font-medium text-gray-700 mb-1">Could This Have Been Prevented?</label>
                    <textarea id="prevention_possible" name="prevention_possible" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Analysis of preventability">{{ old('prevention_possible') }}</textarea>
                </div>
                <div>
                    <label for="lessons_learned" class="block text-sm font-medium text-gray-700 mb-1">Lessons Learned</label>
                    <textarea id="lessons_learned" name="lessons_learned" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Key lessons learned from this analysis">{{ old('lessons_learned') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('incidents.show', $incident) }}" 
                   class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Root Cause Analysis
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function toggleAnalysisType() {
    const analysisType = document.getElementById('analysis_type').value;
    
    // Hide all sections
    document.getElementById('five_whys_section').classList.add('hidden');
    document.getElementById('fishbone_section').classList.add('hidden');
    
    // Show relevant section
    if (analysisType === '5_whys') {
        document.getElementById('five_whys_section').classList.remove('hidden');
    } else if (analysisType === 'fishbone') {
        document.getElementById('fishbone_section').classList.remove('hidden');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const oldType = '{{ old("analysis_type") }}';
    if (oldType) {
        document.getElementById('analysis_type').value = oldType;
        toggleAnalysisType();
    }
});
</script>
@endsection

