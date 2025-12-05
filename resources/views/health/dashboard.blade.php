@extends('layouts.app')

@section('title', 'Health & Wellness Dashboard')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Health & Wellness Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Occupational health monitoring and medical records</p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-6 mb-8">
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Surveillance Records</p>
                    <p class="text-xl md:text-3xl font-bold text-black mt-1 md:mt-2">{{ $stats['total_surveillance_records'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-user-md text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Due Surveillance</p>
                    <p class="text-xl md:text-3xl font-bold text-[#FF9900] mt-1 md:mt-2">{{ $stats['due_surveillance'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-calendar-check text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">First Aid Entries</p>
                    <p class="text-xl md:text-3xl font-bold text-black mt-1 md:mt-2">{{ $stats['total_first_aid_entries'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-first-aid text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Ergonomic Assessments</p>
                    <p class="text-xl md:text-3xl font-bold text-black mt-1 md:mt-2">{{ $stats['total_ergonomic_assessments'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-chair text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Work-Related Sick Leave</p>
                    <p class="text-xl md:text-3xl font-bold text-[#CC0000] mt-1 md:mt-2">{{ $stats['work_related_sick_leave'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-notes-medical text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent First Aid -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Recent First Aid Entries</h3>
            <div class="space-y-3">
                @forelse($recentFirstAid as $entry)
                    <div class="border border-gray-300 p-4 hover:bg-[#F5F5F5] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $entry->injured_person_name ?? ($entry->injuredPerson->name ?? 'Unknown') }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $entry->reference_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $entry->severity == 'serious' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' }}">
                                {{ ucfirst($entry->severity) }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $entry->incident_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $entry->location ?? 'N/A' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No first aid entries recorded</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Surveillance -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Recent Health Surveillance</h3>
            <div class="space-y-3">
                @forelse($recentSurveillance as $record)
                    <div class="border border-gray-300 p-4 hover:bg-[#F5F5F5] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $record->user->name ?? 'Unknown' }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $record->reference_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $record->result == 'fit' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' }}">
                                {{ ucfirst(str_replace('_', ' ', $record->result)) }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $record->examination_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-tag mr-1"></i>{{ ucfirst(str_replace('_', ' ', $record->surveillance_type)) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No surveillance records</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('health.surveillance.index') }}" class="bg-white border border-gray-300 p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-user-md text-black text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-black">Health Surveillance</h3>
                    <p class="text-sm text-gray-500">Medical examinations & tests</p>
                </div>
            </div>
        </a>
        <a href="{{ route('health.first-aid.index') }}" class="bg-white border border-gray-300 p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-first-aid text-black text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-black">First Aid Logbook</h3>
                    <p class="text-sm text-gray-500">First aid records</p>
                </div>
            </div>
        </a>
        <a href="{{ route('health.ergonomic.index') }}" class="bg-white border border-gray-300 p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-chair text-black text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-black">Ergonomic Assessments</h3>
                    <p class="text-sm text-gray-500">Workplace ergonomics</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection

