@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary-black">Talks Schedule</h1>
        <div class="flex gap-3">
            <a href="{{ route('toolbox-talks.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Schedule New Talk
            </a>
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="btn-secondary">
                <i class="fas fa-upload mr-2"></i>Bulk Import
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-border-gray p-4 mb-6 rounded">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-primary-black mb-1">Status</label>
                <select name="status" class="w-full border border-border-gray rounded px-3 py-2">
                    <option value="">All Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-primary-black mb-1">Department</label>
                <select name="department" class="w-full border border-border-gray rounded px-3 py-2">
                    <option value="">All Departments</option>
                    @foreach($departments ?? [] as $dept)
                        <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-primary-black mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border border-border-gray rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-primary-black mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border border-border-gray rounded px-3 py-2">
            </div>
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="btn-primary">Filter</button>
                <a href="{{ route('toolbox-talks.index') }}" class="btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Schedule Table -->
    <div class="bg-white border border-border-gray rounded overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Reference</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Title</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Date & Time</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Location</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Department</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Status</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-gray">
                @forelse($toolboxTalks as $talk)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">{{ $talk->reference_number }}</td>
                        <td class="px-4 py-3 text-sm font-medium">{{ $talk->title }}</td>
                        <td class="px-4 py-3 text-sm">
                            {{ $talk->scheduled_date->format('M d, Y') }}<br>
                            <span class="text-medium-gray">{{ $talk->start_time ? $talk->start_time->format('h:i A') : 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $talk->location }}</td>
                        <td class="px-4 py-3 text-sm">{{ $talk->department?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $talk->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $talk->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $talk->status == 'scheduled' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $talk->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex gap-2">
                                <a href="{{ route('toolbox-talks.show', $talk) }}" class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('toolbox-talks.edit', $talk) }}" class="text-yellow-600 hover:text-yellow-700">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-medium-gray">No talks scheduled</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $toolboxTalks->links() }}
    </div>
</div>

<!-- Bulk Import Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-primary-black">Bulk Import Talks</h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-medium-gray hover:text-primary-black">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('toolbox-talks.bulk-import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-primary-black mb-2">Upload CSV/Excel File</label>
                <input type="file" name="file" accept=".csv,.xlsx,.xls" required class="w-full border border-border-gray rounded px-3 py-2">
                <p class="text-xs text-medium-gray mt-1">Format: Title, Description, Date, Time, Duration, Location, Type, Department ID, Supervisor ID, Biometric Required</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1">Import</button>
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="btn-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

