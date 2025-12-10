@extends('layouts.app')

@section('title', 'Create Inspection')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('inspections.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Inspection</h1>
            </div>
            <div>
                <a href="{{ route('qr.scanner') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                    <i class="fas fa-qrcode mr-2"></i>Scan QR Code
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('inspections.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-black mb-1">Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('title')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="inspection_date" class="block text-sm font-medium text-black mb-1">Inspection Date *</label>
                    <input type="date" id="inspection_date" name="inspection_date" required value="{{ old('inspection_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('inspection_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="inspection_schedule_id" class="block text-sm font-medium text-black mb-1">Schedule</label>
                    <select id="inspection_schedule_id" name="inspection_schedule_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">None</option>
                        @foreach($schedules as $schedule)
                            <option value="{{ $schedule->id }}" 
                                    data-schedule="{{ json_encode(['department_id' => $schedule->department_id, 'checklist_id' => $schedule->inspection_checklist_id]) }}"
                                    {{ old('inspection_schedule_id', $selectedScheduleId) == $schedule->id ? 'selected' : '' }}>
                                {{ $schedule->title }} ({{ $schedule->reference_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="inspection_checklist_id" class="block text-sm font-medium text-black mb-1">Checklist</label>
                    <select id="inspection_checklist_id" name="inspection_checklist_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">None</option>
                        @foreach($checklists as $checklist)
                            <option value="{{ $checklist->id }}" 
                                    data-items="{{ json_encode($checklist->items ?? []) }}"
                                    {{ old('inspection_checklist_id', $selectedChecklistId) == $checklist->id ? 'selected' : '' }}>
                                {{ $checklist->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-black mb-1">Location *</label>
                    <div class="flex gap-2">
                        <input type="text" id="location" name="location" required value="{{ old('location', $prefilledLocation) }}"
                               class="flex-1 px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <a href="{{ route('qr.scanner') }}" 
                           class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm whitespace-nowrap"
                           title="Scan QR Code for Location">
                            <i class="fas fa-qrcode"></i>
                        </a>
                    </div>
                    @error('location')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-black mb-1">Department</label>
                    <select id="department_id" name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="overall_status" class="block text-sm font-medium text-black mb-1">Overall Status *</label>
                    <select id="overall_status" name="overall_status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="pending" {{ old('overall_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="compliant" {{ old('overall_status') == 'compliant' ? 'selected' : '' }}>Compliant</option>
                        <option value="non_compliant" {{ old('overall_status') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant</option>
                        <option value="partial" {{ old('overall_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    </select>
                    @error('overall_status')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Checklist Responses -->
        <div class="bg-white border border-gray-300 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-black">Checklist Responses</h2>
                @if($selectedChecklist || $selectedSchedule)
                    <span class="text-sm text-green-600 bg-green-50 px-3 py-1 rounded-full">
                        <i class="fas fa-check-circle mr-1"></i>Pre-filled from QR scan
                    </span>
                @endif
            </div>
            <div id="checklist-responses-container">
                @if(!$selectedChecklist)
                    <p class="text-sm text-gray-500 mb-4">Select a checklist above to load items, or add items manually.</p>
                @endif
                <div class="space-y-3" id="checklist-items">
                    @if($selectedChecklist && $selectedChecklist->items)
                        @php
                            $initialResponses = [];
                            foreach($selectedChecklist->items as $index => $item) {
                                $key = 'item_' . $index;
                                $initialResponses[$key] = [
                                    'item' => $item['item'] ?? $item['name'] ?? 'Item ' . ($index + 1),
                                    'type' => $item['type'] ?? 'yes_no',
                                    'status' => 'pending',
                                    'notes' => ''
                                ];
                            }
                            $initialResponsesJson = json_encode($initialResponses);
                        @endphp
                        @foreach($selectedChecklist->items as $index => $item)
                            <div class="border border-gray-300 p-4 rounded-lg">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-black">{{ $index + 1 }}. {{ $item['item'] ?? $item['name'] ?? 'Item' }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Type: {{ ucfirst(str_replace('_', ' ', $item['type'] ?? 'yes_no')) }}</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Status</label>
                                    <div class="flex gap-3">
                                        <label class="flex items-center">
                                            <input type="radio" name="response_item_{{ $index }}_status" value="compliant" 
                                                   onchange="updateChecklistResponse('item_{{ $index }}', 'status', this.value)"
                                                   class="h-4 w-4 text-green-600 focus:ring-green-500">
                                            <span class="ml-2 text-xs text-green-600">Compliant</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="response_item_{{ $index }}_status" value="non_compliant"
                                                   onchange="updateChecklistResponse('item_{{ $index }}', 'status', this.value)"
                                                   class="h-4 w-4 text-red-600 focus:ring-red-500">
                                            <span class="ml-2 text-xs text-red-600">Non-Compliant</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="response_item_{{ $index }}_status" value="na"
                                                   onchange="updateChecklistResponse('item_{{ $index }}', 'status', this.value)"
                                                   class="h-4 w-4 text-gray-600 focus:ring-gray-500">
                                            <span class="ml-2 text-xs text-gray-600">N/A</span>
                                        </label>
                                    </div>
                                    <div class="mt-2">
                                        <input type="text" name="response_item_{{ $index }}_notes" placeholder="Notes (optional)" 
                                               onchange="updateChecklistResponse('item_{{ $index }}', 'notes', this.value)"
                                               class="w-full px-2 py-1 text-xs border border-gray-300 rounded">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <input type="hidden" name="checklist_responses" id="checklist_responses" value="{{ old('checklist_responses', isset($initialResponsesJson) ? $initialResponsesJson : '[]') }}">
        </div>

        <!-- Findings & Recommendations -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Findings & Recommendations</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="observations" class="block text-sm font-medium text-black mb-1">Observations</label>
                    <textarea id="observations" name="observations" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('observations') }}</textarea>
                </div>

                <div>
                    <label for="recommendations" class="block text-sm font-medium text-black mb-1">Recommendations</label>
                    <textarea id="recommendations" name="recommendations" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('recommendations') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Follow-up -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Follow-up</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="requires_follow_up" value="1" {{ old('requires_follow_up') ? 'checked' : '' }}
                               class="h-4 w-4 text-[#0066CC] focus:ring-[#0066CC] border-gray-300">
                        <span class="ml-2 text-sm text-black">Requires Follow-up</span>
                    </label>
                </div>

                <div id="follow-up-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4" style="display: none;">
                    <div>
                        <label for="follow_up_date" class="block text-sm font-medium text-black mb-1">Follow-up Date</label>
                        <input type="date" id="follow_up_date" name="follow_up_date" value="{{ old('follow_up_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    </div>

                    <div>
                        <label for="follow_up_assigned_to" class="block text-sm font-medium text-black mb-1">Assign To</label>
                        <select id="follow_up_assigned_to" name="follow_up_assigned_to"
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('follow_up_assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('inspections.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Inspection
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const followUpCheckbox = document.querySelector('input[name="requires_follow_up"]');
    const followUpFields = document.getElementById('follow-up-fields');
    const scheduleSelect = document.getElementById('inspection_schedule_id');
    const checklistSelect = document.getElementById('inspection_checklist_id');
    const locationInput = document.getElementById('location');
    const departmentSelect = document.getElementById('department_id');
    const checklistItemsContainer = document.getElementById('checklist-items');
    const checklistResponsesInput = document.getElementById('checklist_responses');
    
    // Handle follow-up checkbox
    if (followUpCheckbox) {
        followUpCheckbox.addEventListener('change', function() {
            followUpFields.style.display = this.checked ? 'grid' : 'none';
        });
        
        if (followUpCheckbox.checked) {
            followUpFields.style.display = 'grid';
        }
    }
    
    // Auto-populate from schedule when selected
    if (scheduleSelect) {
        scheduleSelect.addEventListener('change', function() {
            const scheduleId = this.value;
            if (scheduleId) {
                // Get schedule data from option attributes
                const selectedOption = this.options[this.selectedIndex];
                const scheduleData = selectedOption.getAttribute('data-schedule');
                if (scheduleData) {
                    const data = JSON.parse(scheduleData);
                    if (data.department_id && !departmentSelect.value) {
                        departmentSelect.value = data.department_id;
                    }
                    if (data.checklist_id && !checklistSelect.value) {
                        checklistSelect.value = data.checklist_id;
                        loadChecklistItems(data.checklist_id);
                    }
                }
            }
        });
    }
    
    // Load checklist items when checklist is selected
    if (checklistSelect) {
        checklistSelect.addEventListener('change', function() {
            const checklistId = this.value;
            if (checklistId) {
                loadChecklistItems(checklistId);
            } else {
                checklistItemsContainer.innerHTML = '<p class="text-sm text-gray-500">Select a checklist to load items.</p>';
                checklistResponsesInput.value = '[]';
            }
        });
        
        // Load checklist items if pre-selected from QR scan (only if not already loaded in HTML)
        @if($selectedChecklistId && $selectedChecklist && !$selectedChecklist->items)
            loadChecklistItems({{ $selectedChecklistId }});
        @elseif($selectedChecklistId && $selectedChecklist && $selectedChecklist->items)
            // Items already loaded in HTML, responses are already set in the hidden input
            // No need to do anything
        @endif
    }
    
    // Auto-populate location from schedule if available
    @if($selectedSchedule)
        @if($selectedSchedule->department_id)
            if (departmentSelect && !departmentSelect.value) {
                departmentSelect.value = {{ $selectedSchedule->department_id }};
            }
        @endif
        @if($selectedSchedule->checklist && !$selectedChecklistId)
            if (checklistSelect && !checklistSelect.value) {
                checklistSelect.value = {{ $selectedSchedule->checklist->id }};
                loadChecklistItems({{ $selectedSchedule->checklist->id }});
            }
        @endif
    @endif
    
    function loadChecklistItems(checklistId) {
        const selectedOption = checklistSelect.querySelector(`option[value="${checklistId}"]`);
        if (!selectedOption) return;
        
        const items = JSON.parse(selectedOption.getAttribute('data-items') || '[]');
        
        if (items.length === 0) {
            checklistItemsContainer.innerHTML = '<p class="text-sm text-gray-500">No items in this checklist.</p>';
            checklistResponsesInput.value = '[]';
            return;
        }
        
        let html = '';
        const responses = {};
        
        items.forEach((item, index) => {
            const itemKey = `item_${index}`;
            responses[itemKey] = {
                item: item.item || item.name || 'Item ' + (index + 1),
                type: item.type || 'yes_no',
                status: 'pending',
                notes: ''
            };
            
            html += `
                <div class="border border-gray-300 p-4 rounded-lg">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-black">${index + 1}. ${item.item || item.name || 'Item'}</p>
                            <p class="text-xs text-gray-500 mt-1">Type: ${(item.type || 'yes_no').replace('_', ' ')}</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Status</label>
                        <div class="flex gap-3">
                            <label class="flex items-center">
                                <input type="radio" name="response_${itemKey}_status" value="compliant" 
                                       onchange="updateChecklistResponse('${itemKey}', 'status', this.value)"
                                       class="h-4 w-4 text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-xs text-green-600">Compliant</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="response_${itemKey}_status" value="non_compliant"
                                       onchange="updateChecklistResponse('${itemKey}', 'status', this.value)"
                                       class="h-4 w-4 text-red-600 focus:ring-red-500">
                                <span class="ml-2 text-xs text-red-600">Non-Compliant</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="response_${itemKey}_status" value="na"
                                       onchange="updateChecklistResponse('${itemKey}', 'status', this.value)"
                                       class="h-4 w-4 text-gray-600 focus:ring-gray-500">
                                <span class="ml-2 text-xs text-gray-600">N/A</span>
                            </label>
                        </div>
                        <div class="mt-2">
                            <input type="text" placeholder="Notes (optional)" 
                                   onchange="updateChecklistResponse('${itemKey}', 'notes', this.value)"
                                   class="w-full px-2 py-1 text-xs border border-gray-300 rounded">
                        </div>
                    </div>
                </div>
            `;
        });
        
        checklistItemsContainer.innerHTML = html;
        checklistResponsesInput.value = JSON.stringify(responses);
    }
    
    window.updateChecklistResponse = function(itemKey, field, value) {
        const responses = JSON.parse(checklistResponsesInput.value || '{}');
        if (responses[itemKey]) {
            responses[itemKey][field] = value;
            checklistResponsesInput.value = JSON.stringify(responses);
        }
    };
    
    // Show success message if coming from QR scan
    @if(session('success'))
        const successMsg = @json(session('success'));
        if (successMsg && successMsg.includes('QR code')) {
            setTimeout(() => {
                alert(successMsg);
            }, 500);
        }
    @endif
});
</script>
@endpush
@endsection

