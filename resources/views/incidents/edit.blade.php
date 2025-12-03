@extends('layouts.app')

@section('title', 'Edit Incident')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('incidents.show', $incident) }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Incident
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Edit Incident: {{ $incident->reference_number }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('incidents.update', $incident) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="incidentForm">
        @csrf
        @method('PUT')

        <!-- Event Type Selection -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Event Type *</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors event-type-option {{ old('event_type', $incident->event_type) == 'injury_illness' ? 'border-blue-500 bg-blue-50' : '' }}">
                    <input type="radio" name="event_type" value="injury_illness" required class="sr-only" onchange="toggleEventFields()" {{ old('event_type', $incident->event_type) == 'injury_illness' ? 'checked' : '' }}>
                    <div class="flex-1">
                        <div class="flex items-center">
                            <i class="fas fa-user-injured text-2xl text-red-500 mr-3"></i>
                            <div>
                                <div class="font-semibold text-gray-900">Injury/Illness</div>
                                <div class="text-sm text-gray-500">Work-related injury or illness</div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors event-type-option {{ old('event_type', $incident->event_type) == 'property_damage' ? 'border-blue-500 bg-blue-50' : '' }}">
                    <input type="radio" name="event_type" value="property_damage" required class="sr-only" onchange="toggleEventFields()" {{ old('event_type', $incident->event_type) == 'property_damage' ? 'checked' : '' }}>
                    <div class="flex-1">
                        <div class="flex items-center">
                            <i class="fas fa-tools text-2xl text-orange-500 mr-3"></i>
                            <div>
                                <div class="font-semibold text-gray-900">Property Damage</div>
                                <div class="text-sm text-gray-500">Damage to equipment or property</div>
                            </div>
                        </div>
                    </div>
                </label>
                <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors event-type-option {{ old('event_type', $incident->event_type) == 'near_miss' ? 'border-blue-500 bg-blue-50' : '' }}">
                    <input type="radio" name="event_type" value="near_miss" required class="sr-only" onchange="toggleEventFields()" {{ old('event_type', $incident->event_type) == 'near_miss' ? 'checked' : '' }}>
                    <div class="flex-1">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-2xl text-yellow-500 mr-3"></i>
                            <div>
                                <div class="font-semibold text-gray-900">Near Miss</div>
                                <div class="text-sm text-gray-500">Unplanned event with no injury</div>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
            @error('event_type')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Incident Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title', $incident->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Brief description of the incident">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="severity" class="block text-sm font-medium text-gray-700 mb-1">Severity *</label>
                    <select id="severity" name="severity" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Severity</option>
                        <option value="low" {{ old('severity', $incident->severity) == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('severity', $incident->severity) == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('severity', $incident->severity) == 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('severity', $incident->severity) == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                    @error('severity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="reported" {{ old('status', $incident->status) == 'reported' ? 'selected' : '' }}>Reported</option>
                        <option value="open" {{ old('status', $incident->status) == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="investigating" {{ old('status', $incident->status) == 'investigating' ? 'selected' : '' }}>Investigating</option>
                        <option value="resolved" {{ old('status', $incident->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ old('status', $incident->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_occurred" class="block text-sm font-medium text-gray-700 mb-1">Date & Time Occurred *</label>
                    <input type="datetime-local" id="date_occurred" name="date_occurred" required 
                           value="{{ old('date_occurred', $incident->incident_date ? $incident->incident_date->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('date_occurred')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Detailed description of what happened">{{ old('description', $incident->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location *</label>
                    <input type="text" id="location" name="location" required value="{{ old('location', $incident->location) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Where did this incident occur?">
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location_specific" class="block text-sm font-medium text-gray-700 mb-1">Specific Location</label>
                    <input type="text" id="location_specific" name="location_specific" value="{{ old('location_specific', $incident->location_specific) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="More specific location details">
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select id="department_id" name="department_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $incident->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                    <select id="assigned_to" name="assigned_to" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Person</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to', $incident->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if($incident->status == 'closed' || $incident->status == 'resolved')
                <div class="md:col-span-2">
                    <label for="resolution_notes" class="block text-sm font-medium text-gray-700 mb-1">Resolution Notes</label>
                    <textarea id="resolution_notes" name="resolution_notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Notes about how this incident was resolved">{{ old('resolution_notes', $incident->resolution_notes) }}</textarea>
                    @error('resolution_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endif
            </div>
        </div>

        <!-- Injury/Illness Specific Fields -->
        <div id="injury_illness_fields" class="bg-white rounded-lg shadow p-6 {{ old('event_type', $incident->event_type) == 'injury_illness' ? '' : 'hidden' }}">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Injury/Illness Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="injury_type" class="block text-sm font-medium text-gray-700 mb-1">Injury Type</label>
                    <select id="injury_type" name="injury_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Type</option>
                        <option value="fatal" {{ old('injury_type', $incident->injury_type) == 'fatal' ? 'selected' : '' }}>Fatal</option>
                        <option value="lost_time" {{ old('injury_type', $incident->injury_type) == 'lost_time' ? 'selected' : '' }}>Lost Time Injury</option>
                        <option value="medical_treatment" {{ old('injury_type', $incident->injury_type) == 'medical_treatment' ? 'selected' : '' }}>Medical Treatment</option>
                        <option value="first_aid" {{ old('injury_type', $incident->injury_type) == 'first_aid' ? 'selected' : '' }}>First Aid</option>
                        <option value="illness" {{ old('injury_type', $incident->injury_type) == 'illness' ? 'selected' : '' }}>Illness</option>
                        <option value="other" {{ old('injury_type', $incident->injury_type) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label for="body_part_affected" class="block text-sm font-medium text-gray-700 mb-1">Body Part Affected</label>
                    <input type="text" id="body_part_affected" name="body_part_affected" value="{{ old('body_part_affected', $incident->body_part_affected) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., Left hand, Back, Head">
                </div>
                <div>
                    <label for="nature_of_injury" class="block text-sm font-medium text-gray-700 mb-1">Nature of Injury</label>
                    <input type="text" id="nature_of_injury" name="nature_of_injury" value="{{ old('nature_of_injury', $incident->nature_of_injury) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., Laceration, Fracture, Strain">
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="lost_time_injury" value="1" {{ old('lost_time_injury', $incident->lost_time_injury) ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm text-gray-700">Lost Time Injury</span>
                    </label>
                </div>
                <div>
                    <label for="days_lost" class="block text-sm font-medium text-gray-700 mb-1">Days Lost</label>
                    <input type="number" id="days_lost" name="days_lost" min="0" value="{{ old('days_lost', $incident->days_lost) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label for="medical_treatment_details" class="block text-sm font-medium text-gray-700 mb-1">Medical Treatment Details</label>
                    <textarea id="medical_treatment_details" name="medical_treatment_details" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Details of medical treatment received">{{ old('medical_treatment_details', $incident->medical_treatment_details) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Property Damage Specific Fields -->
        <div id="property_damage_fields" class="bg-white rounded-lg shadow p-6 {{ old('event_type', $incident->event_type) == 'property_damage' ? '' : 'hidden' }}">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Property Damage Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="asset_damaged" class="block text-sm font-medium text-gray-700 mb-1">Asset/Equipment Damaged</label>
                    <input type="text" id="asset_damaged" name="asset_damaged" value="{{ old('asset_damaged', $incident->asset_damaged) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Description of damaged asset or equipment">
                </div>
                <div>
                    <label for="estimated_cost" class="block text-sm font-medium text-gray-700 mb-1">Estimated Cost</label>
                    <input type="number" id="estimated_cost" name="estimated_cost" step="0.01" min="0" value="{{ old('estimated_cost', $incident->estimated_cost) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00">
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="insurance_claim_filed" value="1" {{ old('insurance_claim_filed', $incident->insurance_claim_filed) ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm text-gray-700">Insurance Claim Filed</span>
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label for="damage_description" class="block text-sm font-medium text-gray-700 mb-1">Damage Description</label>
                    <textarea id="damage_description" name="damage_description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Detailed description of the damage">{{ old('damage_description', $incident->damage_description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Near Miss Specific Fields -->
        <div id="near_miss_fields" class="bg-white rounded-lg shadow p-6 {{ old('event_type', $incident->event_type) == 'near_miss' ? '' : 'hidden' }}">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Near Miss Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="potential_severity" class="block text-sm font-medium text-gray-700 mb-1">Potential Severity</label>
                    <select id="potential_severity" name="potential_severity"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Severity</option>
                        <option value="low" {{ old('potential_severity', $incident->potential_severity) == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('potential_severity', $incident->potential_severity) == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('potential_severity', $incident->potential_severity) == 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('potential_severity', $incident->potential_severity) == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="potential_consequences" class="block text-sm font-medium text-gray-700 mb-1">Potential Consequences</label>
                    <textarea id="potential_consequences" name="potential_consequences" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="What could have happened?">{{ old('potential_consequences', $incident->potential_consequences) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label for="preventive_measures_taken" class="block text-sm font-medium text-gray-700 mb-1">Preventive Measures Taken</label>
                    <textarea id="preventive_measures_taken" name="preventive_measures_taken" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="What measures were taken to prevent this from happening again?">{{ old('preventive_measures_taken', $incident->preventive_measures_taken) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Images/Attachments -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Images/Attachments</h2>
            
            @if($incident->images && count($incident->images) > 0)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Images</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($incident->images as $image)
                        <div class="relative">
                            <img src="{{ asset('storage/' . $image) }}" alt="Incident Image" class="w-full h-24 object-cover rounded-lg">
                            <button type="button" onclick="removeImage('{{ $image }}')" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div>
                <label for="images" class="block text-sm font-medium text-gray-700 mb-1">Upload Additional Images</label>
                <input type="file" id="images" name="images[]" multiple accept="image/*"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-sm text-gray-500">You can upload multiple images (max 5MB each)</p>
                @error('images.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                    <i class="fas fa-save mr-2"></i>Update Incident
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function toggleEventFields() {
    const eventType = document.querySelector('input[name="event_type"]:checked')?.value;
    
    // Hide all event-specific fields
    document.getElementById('injury_illness_fields').classList.add('hidden');
    document.getElementById('property_damage_fields').classList.add('hidden');
    document.getElementById('near_miss_fields').classList.add('hidden');
    
    // Show relevant fields based on selection
    if (eventType === 'injury_illness') {
        document.getElementById('injury_illness_fields').classList.remove('hidden');
    } else if (eventType === 'property_damage') {
        document.getElementById('property_damage_fields').classList.remove('hidden');
    } else if (eventType === 'near_miss') {
        document.getElementById('near_miss_fields').classList.remove('hidden');
    }
    
    // Update border color for selected option
    document.querySelectorAll('.event-type-option').forEach(option => {
        option.classList.remove('border-blue-500', 'bg-blue-50');
    });
    const selectedOption = document.querySelector('input[name="event_type"]:checked')?.closest('.event-type-option');
    if (selectedOption) {
        selectedOption.classList.add('border-blue-500', 'bg-blue-50');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const eventType = '{{ old("event_type", $incident->event_type) }}';
    if (eventType) {
        document.querySelector(`input[name="event_type"][value="${eventType}"]`).checked = true;
        toggleEventFields();
    }
});
</script>
@endsection

