@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary-black">Attendance Management</h1>
        <div class="flex gap-3">
            @if($toolboxTalk->biometric_required)
                <form action="{{ route('toolbox-talks.sync-biometric', $toolboxTalk) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-fingerprint mr-2"></i>Sync Biometric
                    </button>
                </form>
            @endif
            <a href="{{ route('toolbox-talks.export-attendance-pdf', $toolboxTalk) }}" class="btn-secondary" target="_blank">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </a>
            <a href="{{ route('toolbox-talks.export-attendance-excel', $toolboxTalk) }}" class="btn-secondary">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </a>
            <a href="{{ route('toolbox-talks.show', $toolboxTalk) }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Talk
            </a>
        </div>
    </div>

    <!-- Attendance Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Total Expected</div>
            <div class="text-2xl font-bold text-primary-black">{{ $toolboxTalk->total_attendees ?? 0 }}</div>
        </div>
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Present</div>
            <div class="text-2xl font-bold text-green-600">{{ $toolboxTalk->attendances->where('attendance_status', 'present')->count() }}</div>
        </div>
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Absent</div>
            <div class="text-2xl font-bold text-red-600">{{ $toolboxTalk->attendances->where('attendance_status', 'absent')->count() }}</div>
        </div>
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Attendance Rate</div>
            <div class="text-2xl font-bold text-primary-black">{{ number_format($toolboxTalk->attendance_rate ?? 0, 1) }}%</div>
        </div>
    </div>

    <!-- Manual Attendance Marker -->
    <div class="bg-white border border-border-gray p-6 rounded mb-6">
        <h2 class="text-lg font-semibold text-primary-black mb-4">
            <i class="fas fa-user-check mr-2"></i>Manual Attendance Marker
        </h2>
        
        <!-- Tabs for Single vs Multiple -->
        <div class="mb-4 border-b border-border-gray">
            <button type="button" onclick="switchAttendanceMode('single')" id="tab-single" 
                    class="px-4 py-2 font-medium text-primary-black border-b-2 border-[#0066CC]">
                <i class="fas fa-user mr-2"></i>Single Employee
            </button>
            <button type="button" onclick="switchAttendanceMode('multiple')" id="tab-multiple" 
                    class="px-4 py-2 font-medium text-gray-500 border-b-2 border-transparent">
                <i class="fas fa-users mr-2"></i>Multiple Employees
            </button>
        </div>

        <form action="{{ route('toolbox-talks.mark-attendance', $toolboxTalk) }}" method="POST" id="attendance-form">
            @csrf
            
            <!-- Single Employee Mode -->
            <div id="single-mode" class="attendance-mode">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-primary-black mb-1">Employee</label>
                        <div class="relative">
                            <input type="text" 
                                   id="employee_search" 
                                   name="employee_search"
                                   class="w-full border border-border-gray rounded px-3 py-2 pr-10"
                                   placeholder="Search employee by name, ID, or email..."
                                   autocomplete="off">
                            <input type="hidden" name="employee_id" id="employee_id" value="">
                            <div id="employee_autocomplete" class="absolute z-50 w-full mt-1 bg-white border border-border-gray rounded shadow-lg hidden max-h-60 overflow-y-auto"></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-black mb-1">Status</label>
                        <select name="status" required class="w-full border border-border-gray rounded px-3 py-2">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="excused">Excused</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-black mb-1">Absence Reason (if applicable)</label>
                        <input type="text" name="absence_reason" class="w-full border border-border-gray rounded px-3 py-2" 
                               placeholder="Optional">
                    </div>
                </div>
            </div>

            <!-- Multiple Employees Mode -->
            <div id="multiple-mode" class="attendance-mode hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-primary-black mb-1">
                            Employee Names (comma-separated)
                        </label>
                        <div class="relative">
                            <textarea name="employee_names" id="employee_names" rows="4" 
                                      class="w-full border border-border-gray rounded px-3 py-2"
                                      placeholder="Start typing employee name, ID, or email... Press comma to add multiple employees"
                                      autocomplete="off"></textarea>
                            <div id="multiple_autocomplete" class="absolute z-50 w-full mt-1 bg-white border border-border-gray rounded shadow-lg hidden max-h-60 overflow-y-auto"></div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Start typing to see suggestions. Press Enter or click to select. Separate multiple employees with commas.
                        </p>
                        <div id="selected_employees" class="mt-2 flex flex-wrap gap-2"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-black mb-1">Status</label>
                        <select name="status_multiple" id="status_multiple" required class="w-full border border-border-gray rounded px-3 py-2">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="excused">Excused</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-black mb-1">Absence Reason (if applicable)</label>
                        <input type="text" name="absence_reason_multiple" id="absence_reason_multiple" 
                               class="w-full border border-border-gray rounded px-3 py-2" 
                               placeholder="Optional">
                    </div>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit" class="bg-[#0066CC] text-white px-6 py-2 rounded hover:bg-[#0052A3] font-medium">
                    <i class="fas fa-check mr-2"></i>Mark Attendance
                </button>
            </div>
        </form>
    </div>

    <script>
        function switchAttendanceMode(mode) {
            // Hide all modes
            document.querySelectorAll('.attendance-mode').forEach(el => el.classList.add('hidden'));
            
            // Remove active class from all tabs
            document.querySelectorAll('[id^="tab-"]').forEach(tab => {
                tab.classList.remove('border-[#0066CC]', 'text-primary-black');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected mode
            if (mode === 'single') {
                document.getElementById('single-mode').classList.remove('hidden');
                document.getElementById('tab-single').classList.add('border-[#0066CC]', 'text-primary-black');
                document.getElementById('tab-single').classList.remove('border-transparent', 'text-gray-500');
                document.getElementById('employee_id').removeAttribute('disabled');
                document.getElementById('employee_names').setAttribute('disabled', 'disabled');
            } else {
                document.getElementById('multiple-mode').classList.remove('hidden');
                document.getElementById('tab-multiple').classList.add('border-[#0066CC]', 'text-primary-black');
                document.getElementById('tab-multiple').classList.remove('border-transparent', 'text-gray-500');
                document.getElementById('employee_id').setAttribute('disabled', 'disabled');
                document.getElementById('employee_names').removeAttribute('disabled');
            }
        }

        // Autocomplete for single employee mode
        let employeeSearchTimeout;
        let selectedEmployee = null;
        const employeeSearchInput = document.getElementById('employee_search');
        const employeeIdInput = document.getElementById('employee_id');
        const employeeAutocomplete = document.getElementById('employee_autocomplete');

        employeeSearchInput.addEventListener('input', function() {
            clearTimeout(employeeSearchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                employeeAutocomplete.classList.add('hidden');
                employeeIdInput.value = '';
                selectedEmployee = null;
                return;
            }

            employeeSearchTimeout = setTimeout(() => {
                fetch(`{{ route('api.employees.search') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            employeeAutocomplete.classList.add('hidden');
                            return;
                        }

                        employeeAutocomplete.innerHTML = data.map(emp => {
                            const display = emp.display.replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                            const name = emp.name.replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                            return `<div class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" 
                                 onclick="selectEmployee(${emp.id}, '${display}')">
                                <div class="font-medium">${name}</div>
                                <div class="text-xs text-gray-500">${emp.employee_id_number || emp.email}</div>
                            </div>`;
                        }).join('');
                        employeeAutocomplete.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        employeeAutocomplete.classList.add('hidden');
                    });
            }, 300);
        });

        function selectEmployee(id, display) {
            employeeIdInput.value = id;
            employeeSearchInput.value = display;
            selectedEmployee = { id, display };
            employeeAutocomplete.classList.add('hidden');
        }

        // Close autocomplete when clicking outside
        document.addEventListener('click', function(e) {
            if (!employeeSearchInput.contains(e.target) && !employeeAutocomplete.contains(e.target)) {
                employeeAutocomplete.classList.add('hidden');
            }
        });

        // Autocomplete for multiple employees mode
        let multipleSearchTimeout;
        let currentQuery = '';
        let selectedEmployees = [];
        const employeeNamesInput = document.getElementById('employee_names');
        const multipleAutocomplete = document.getElementById('multiple_autocomplete');
        const selectedEmployeesDiv = document.getElementById('selected_employees');

        employeeNamesInput.addEventListener('input', function() {
            const value = this.value;
            const lastCommaIndex = value.lastIndexOf(',');
            currentQuery = lastCommaIndex >= 0 ? value.substring(lastCommaIndex + 1).trim() : value.trim();
            
            clearTimeout(multipleSearchTimeout);
            
            if (currentQuery.length < 2) {
                multipleAutocomplete.classList.add('hidden');
                return;
            }

            multipleSearchTimeout = setTimeout(() => {
                fetch(`{{ route('api.employees.search') }}?q=${encodeURIComponent(currentQuery)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            multipleAutocomplete.classList.add('hidden');
                            return;
                        }

                        multipleAutocomplete.innerHTML = data.map(emp => {
                            const name = emp.name.replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                            return `<div class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" 
                                 onclick="selectMultipleEmployee('${name}')">
                                <div class="font-medium">${emp.name}</div>
                                <div class="text-xs text-gray-500">${emp.employee_id_number || emp.email}</div>
                            </div>`;
                        }).join('');
                        multipleAutocomplete.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        multipleAutocomplete.classList.add('hidden');
                    });
            }, 300);
        });

        function selectMultipleEmployee(name) {
            if (selectedEmployees.includes(name)) {
                return; // Already selected
            }
            
            selectedEmployees.push(name);
            const value = employeeNamesInput.value;
            const lastCommaIndex = value.lastIndexOf(',');
            const beforeComma = lastCommaIndex >= 0 ? value.substring(0, lastCommaIndex + 1) : '';
            employeeNamesInput.value = beforeComma + name + ', ';
            
            // Add to selected display
            const badge = document.createElement('span');
            badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800';
            badge.innerHTML = `${name} <button type="button" onclick="removeEmployee('${name.replace(/'/g, "\\'")}', this)" class="ml-2 text-blue-600 hover:text-blue-800">×</button>`;
            selectedEmployeesDiv.appendChild(badge);
            
            multipleAutocomplete.classList.add('hidden');
            employeeNamesInput.focus();
        }

        function removeEmployee(name, button) {
            selectedEmployees = selectedEmployees.filter(n => n !== name);
            button.parentElement.remove();
            
            // Remove from textarea
            const value = employeeNamesInput.value;
            const escapedName = name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            employeeNamesInput.value = value.replace(new RegExp(escapedName + ',\\s*', 'g'), '').replace(new RegExp(',\\s*' + escapedName, 'g'), '');
        }

        // Close autocomplete when clicking outside
        document.addEventListener('click', function(e) {
            if (!employeeNamesInput.contains(e.target) && !multipleAutocomplete.contains(e.target)) {
                multipleAutocomplete.classList.add('hidden');
            }
        });

        // Handle form submission
        document.getElementById('attendance-form').addEventListener('submit', function(e) {
            const singleMode = !document.getElementById('single-mode').classList.contains('hidden');
            
            if (singleMode) {
                // Single mode - validate employee_id
                if (!employeeIdInput.value) {
                    e.preventDefault();
                    alert('Please select an employee');
                    return false;
                }
                document.getElementById('employee_names').disabled = true;
            } else {
                // Multiple mode - use employee_names
                if (!employeeNamesInput.value.trim()) {
                    e.preventDefault();
                    alert('Please enter at least one employee name');
                    return false;
                }
                document.getElementById('employee_id').disabled = true;
                // Copy status and absence_reason to main fields
                const form = this;
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = document.getElementById('status_multiple').value;
                form.appendChild(statusInput);
                
                const absenceInput = document.createElement('input');
                absenceInput.type = 'hidden';
                absenceInput.name = 'absence_reason';
                absenceInput.value = document.getElementById('absence_reason_multiple').value;
                form.appendChild(absenceInput);
            }
        });
    </script>

    <!-- Attendance List -->
    <div class="bg-white border border-border-gray rounded overflow-hidden">
        <div class="p-4 border-b border-border-gray">
            <h2 class="text-lg font-semibold text-primary-black">Attendance List</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Employee</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">ID Number</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Department</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Check-in Method</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Check-in Time</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-gray">
                    @forelse($toolboxTalk->attendances as $attendance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $attendance->employee_name }}</td>
                            <td class="px-4 py-3 text-sm text-medium-gray">{{ $attendance->employee_id_number }}</td>
                            <td class="px-4 py-3 text-sm">{{ $attendance->department ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">
                                {!! $attendance->getAttendanceStatusBadge() !!}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {!! $attendance->getCheckInMethodBadge() !!}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $attendance->check_in_time ? $attendance->check_in_time->format('M d, h:i A') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($attendance->isBiometric())
                                    <span class="text-blue-600" title="Biometric Check-in">
                                        <i class="fas fa-fingerprint"></i>
                                    </span>
                                @endif
                                @if($attendance->hasSignature())
                                    <span class="text-green-600" title="Digital Signature">
                                        <i class="fas fa-signature"></i>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-medium-gray">No attendance records yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

