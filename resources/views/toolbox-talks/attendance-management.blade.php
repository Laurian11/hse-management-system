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
        <form action="{{ route('toolbox-talks.mark-attendance', $toolboxTalk) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-primary-black mb-1">Employee</label>
                    <select name="employee_id" required class="w-full border border-border-gray rounded px-3 py-2">
                        <option value="">Select Employee</option>
                        @foreach($employees ?? [] as $employee)
                            <option value="{{ $employee->id }}">
                                {{ $employee->name }} ({{ $employee->employee_id_number }})
                            </option>
                        @endforeach
                    </select>
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
            <button type="submit" class="mt-4 btn-primary">
                <i class="fas fa-check mr-2"></i>Mark Attendance
            </button>
        </form>
    </div>

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

