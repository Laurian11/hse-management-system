@extends('layouts.app')

@section('title', 'Audit: ' . $audit->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('inspections.audits.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $audit->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $audit->title }}</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('inspections.audits.edit', $audit) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('inspections.audits.findings.index', $audit) }}" class="bg-white text-black px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">
                    <i class="fas fa-list mr-2"></i>Findings
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Audit Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Title</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Audit Type</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst($audit->audit_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Scope</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst($audit->scope) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $audit->status == 'completed' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $audit->status == 'in_progress' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $audit->status == 'planned' ? 'bg-[#F5F5F5] text-gray-500 border-gray-300' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $audit->status)) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Planned Dates</dt>
                        <dd class="mt-1 text-sm text-black">
                            {{ $audit->planned_start_date->format('M d, Y') }} - {{ $audit->planned_end_date->format('M d, Y') }}
                        </dd>
                    </div>
                    @if($audit->actual_start_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Actual Dates</dt>
                        <dd class="mt-1 text-sm text-black">
                            {{ $audit->actual_start_date->format('M d, Y') }} - {{ $audit->actual_end_date ? $audit->actual_end_date->format('M d, Y') : 'Ongoing' }}
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Lead Auditor</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->leadAuditor->name }}</dd>
                    </div>
                    @if($audit->department)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->department->name }}</dd>
                    </div>
                    @endif
                    @if($audit->result)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Result</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $audit->result == 'compliant' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $audit->result == 'non_compliant' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $audit->result)) }}
                            </span>
                        </dd>
                    </div>
                    @endif
                    @if($audit->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Findings Summary -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Findings Summary</h2>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-black">{{ $audit->total_findings }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-[#CC0000]">{{ $audit->critical_findings }}</p>
                        <p class="text-xs text-gray-500 mt-1">Critical</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-[#FF9900]">{{ $audit->major_findings }}</p>
                        <p class="text-xs text-gray-500 mt-1">Major</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-[#0066CC]">{{ $audit->minor_findings }}</p>
                        <p class="text-xs text-gray-500 mt-1">Minor</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-500">{{ $audit->observations }}</p>
                        <p class="text-xs text-gray-500 mt-1">Observations</p>
                    </div>
                </div>
            </div>

            <!-- Executive Summary & Conclusion -->
            @if($audit->executive_summary || $audit->conclusion)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Summary & Conclusion</h2>
                <dl class="space-y-4">
                    @if($audit->executive_summary)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Executive Summary</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->executive_summary }}</dd>
                    </div>
                    @endif
                    @if($audit->conclusion)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Conclusion</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->conclusion }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('inspections.audits.findings.create', $audit) }}" class="block w-full text-center px-4 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                        <i class="fas fa-plus mr-2"></i>Add Finding
                    </a>
                    <a href="{{ route('inspections.audits.findings.index', $audit) }}" class="block w-full text-center px-4 py-2 bg-white text-black border border-gray-300 hover:bg-[#F5F5F5]">
                        <i class="fas fa-list mr-2"></i>View All Findings
                    </a>
                </div>
            </div>

            <!-- Recent Findings -->
            @if($audit->findings->count() > 0)
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Recent Findings</h3>
                <div class="space-y-3">
                    @foreach($audit->findings->take(5) as $finding)
                        <a href="{{ route('inspections.audits.findings.show', [$audit, $finding]) }}" class="block border border-gray-300 p-3 hover:bg-[#F5F5F5] hover:border-[#0066CC] transition-all">
                            <p class="text-sm font-medium text-black">{{ $finding->title }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ ucfirst($finding->severity) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

