<!-- Permit Type & Basic Info -->
<div class="bg-white border border-gray-300 p-6">
    <h2 class="text-lg font-semibold text-black mb-4">Permit Information</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="work_permit_type_id" class="block text-sm font-medium text-black mb-1">Permit Type *</label>
            <select id="work_permit_type_id" name="work_permit_type_id" required
                    class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">Select Permit Type</option>
                @foreach($permitTypes as $type)
                    <option value="{{ $type->id }}" {{ old('work_permit_type_id', $workPermit ? $workPermit->work_permit_type_id : '') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
            @error('work_permit_type_id')
                <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="department_id" class="block text-sm font-medium text-black mb-1">Department</label>
            <select id="department_id" name="department_id"
                    class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">Select Department</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ old('department_id', $workPermit ? $workPermit->department_id : '') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2">
            <label for="work_title" class="block text-sm font-medium text-black mb-1">Work Title *</label>
            <input type="text" id="work_title" name="work_title" required value="{{ old('work_title', $workPermit ? $workPermit->work_title : '') }}"
                   class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            @error('work_title')
                <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="work_description" class="block text-sm font-medium text-black mb-1">Work Description *</label>
            <textarea id="work_description" name="work_description" rows="4" required
                      class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('work_description', $workPermit ? $workPermit->work_description : '') }}</textarea>
            @error('work_description')
                <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="work_location" class="block text-sm font-medium text-black mb-1">Work Location *</label>
            <input type="text" id="work_location" name="work_location" required value="{{ old('work_location', $workPermit ? $workPermit->work_location : '') }}"
                   class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            @error('work_location')
                <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<!-- Work Schedule -->
<div class="bg-white border border-gray-300 p-6">
    <h2 class="text-lg font-semibold text-black mb-4">Work Schedule</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="work_start_date" class="block text-sm font-medium text-black mb-1">Start Date & Time *</label>
            <input type="datetime-local" id="work_start_date" name="work_start_date" required 
                   value="{{ old('work_start_date', $workPermit && $workPermit->work_start_date ? $workPermit->work_start_date->format('Y-m-d\TH:i') : '') }}"
                   class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            @error('work_start_date')
                <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="work_end_date" class="block text-sm font-medium text-black mb-1">End Date & Time *</label>
            <input type="datetime-local" id="work_end_date" name="work_end_date" required
                   value="{{ old('work_end_date', $workPermit && $workPermit->work_end_date ? $workPermit->work_end_date->format('Y-m-d\TH:i') : '') }}"
                   class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            @error('work_end_date')
                <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="validity_hours" class="block text-sm font-medium text-black mb-1">Validity Hours *</label>
            <input type="number" id="validity_hours" name="validity_hours" required 
                   value="{{ old('validity_hours', $workPermit ? $workPermit->validity_hours : 24) }}" min="1" max="168"
                   class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            @error('validity_hours')
                <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<!-- Risk Assessment & JSA -->
<div class="bg-white border border-gray-300 p-6">
    <h2 class="text-lg font-semibold text-black mb-4">Risk Assessment & JSA</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="risk_assessment_id" class="block text-sm font-medium text-black mb-1">Linked Risk Assessment</label>
            <select id="risk_assessment_id" name="risk_assessment_id"
                    class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">None</option>
                @foreach($riskAssessments as $ra)
                    <option value="{{ $ra->id }}" {{ old('risk_assessment_id', $workPermit ? $workPermit->risk_assessment_id : '') == $ra->id ? 'selected' : '' }}>
                        {{ $ra->reference_number }} - {{ $ra->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="jsa_id" class="block text-sm font-medium text-black mb-1">Linked JSA</label>
            <select id="jsa_id" name="jsa_id"
                    class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">None</option>
                @foreach($jsas as $jsa)
                    <option value="{{ $jsa->id }}" {{ old('jsa_id', $workPermit ? $workPermit->jsa_id : '') == $jsa->id ? 'selected' : '' }}>
                        {{ $jsa->reference_number }} - {{ $jsa->title }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<!-- Safety Requirements -->
<div class="bg-white border border-gray-300 p-6">
    <h2 class="text-lg font-semibold text-black mb-4">Safety Requirements</h2>
    
    <div class="space-y-4">
        <div>
            <label class="flex items-center">
                <input type="checkbox" name="gas_test_required" value="1" {{ old('gas_test_required', $workPermit ? $workPermit->gas_test_required : false) ? 'checked' : '' }}
                       class="h-4 w-4 text-[#0066CC] focus:ring-[#0066CC] border-gray-300">
                <span class="ml-2 text-sm text-black">Gas Test Required</span>
            </label>
        </div>

        <div id="gas_test_fields" class="{{ old('gas_test_required', $workPermit ? $workPermit->gas_test_required : false) ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="gas_test_date" class="block text-sm font-medium text-black mb-1">Gas Test Date</label>
                <input type="datetime-local" id="gas_test_date" name="gas_test_date" 
                       value="{{ old('gas_test_date', $workPermit && $workPermit->gas_test_date ? $workPermit->gas_test_date->format('Y-m-d\TH:i') : '') }}"
                       class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            </div>
            <div>
                <label for="gas_tester_id" class="block text-sm font-medium text-black mb-1">Gas Tester</label>
                <select id="gas_tester_id" name="gas_tester_id"
                        class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    <option value="">Select Tester</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('gas_tester_id', $workPermit ? $workPermit->gas_tester_id : '') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="gas_test_results" class="block text-sm font-medium text-black mb-1">Gas Test Results</label>
                <textarea id="gas_test_results" name="gas_test_results" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('gas_test_results', $workPermit ? $workPermit->gas_test_results : '') }}</textarea>
            </div>
        </div>

        <div>
            <label class="flex items-center">
                <input type="checkbox" name="fire_watch_required" value="1" {{ old('fire_watch_required', $workPermit ? $workPermit->fire_watch_required : false) ? 'checked' : '' }}
                       class="h-4 w-4 text-[#0066CC] focus:ring-[#0066CC] border-gray-300">
                <span class="ml-2 text-sm text-black">Fire Watch Required</span>
            </label>
        </div>

        <div id="fire_watch_fields" class="{{ old('fire_watch_required', $workPermit ? $workPermit->fire_watch_required : false) ? '' : 'hidden' }}">
            <label for="fire_watch_person_id" class="block text-sm font-medium text-black mb-1">Fire Watch Person</label>
            <select id="fire_watch_person_id" name="fire_watch_person_id"
                    class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">Select Person</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('fire_watch_person_id', $workPermit ? $workPermit->fire_watch_person_id : '') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="emergency_procedures" class="block text-sm font-medium text-black mb-1">Emergency Procedures</label>
            <textarea id="emergency_procedures" name="emergency_procedures" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('emergency_procedures', $workPermit ? $workPermit->emergency_procedures : '') }}</textarea>
        </div>
    </div>
</div>

<!-- Additional Information -->
<div class="bg-white border border-gray-300 p-6">
    <h2 class="text-lg font-semibold text-black mb-4">Additional Information</h2>
    
    <div class="space-y-4">
        <div>
            <label class="flex items-center">
                <input type="checkbox" name="gcla_compliance_required" value="1" {{ old('gcla_compliance_required', $workPermit ? $workPermit->gcla_compliance_required : false) ? 'checked' : '' }}
                       class="h-4 w-4 text-[#0066CC] focus:ring-[#0066CC] border-gray-300">
                <span class="ml-2 text-sm text-black">GCLA Compliance Required</span>
            </label>
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-black mb-1">Notes</label>
            <textarea id="notes" name="notes" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('notes', $workPermit ? $workPermit->notes : '') }}</textarea>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle gas test fields
    const gasTestCheckbox = document.querySelector('input[name="gas_test_required"]');
    const gasTestFields = document.getElementById('gas_test_fields');
    
    if (gasTestCheckbox) {
        gasTestCheckbox.addEventListener('change', function() {
            gasTestFields.classList.toggle('hidden', !this.checked);
        });
    }

    // Toggle fire watch fields
    const fireWatchCheckbox = document.querySelector('input[name="fire_watch_required"]');
    const fireWatchFields = document.getElementById('fire_watch_fields');
    
    if (fireWatchCheckbox) {
        fireWatchCheckbox.addEventListener('change', function() {
            fireWatchFields.classList.toggle('hidden', !this.checked);
        });
    }
});
</script>
@endpush

