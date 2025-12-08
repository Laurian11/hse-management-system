@extends('layouts.app')

@section('title', 'Safety Communication Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('safety-communications.index') }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Communications
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $communication->title }}</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ $communication->reference_number }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('safety-communications.export-pdf', $communication) }}" class="px-4 py-2 text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </a>
                    @if($communication->canBeEdited())
                        <a href="{{ route('safety-communications.edit', $communication) }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                    @endif
                    @if($communication->status === 'draft' || $communication->status === 'scheduled')
                        <form action="{{ route('safety-communications.send', $communication) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-paper-plane mr-2"></i>Send Now
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('safety-communications.duplicate', $communication) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-copy mr-2"></i>Duplicate
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Communication Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Communication Details</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Title</label>
                            <p class="text-base text-gray-900 mt-1">{{ $communication->title }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Message</label>
                            <div class="mt-1 text-base text-gray-900 whitespace-pre-wrap">{{ $communication->message }}</div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Type</label>
                                <p class="text-base text-gray-900 mt-1 capitalize">{{ str_replace('_', ' ', $communication->communication_type) }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Priority</label>
                                <p class="text-base mt-1">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $communication->getPriorityColor() }}">
                                        {{ ucfirst($communication->priority_level) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Target Audience</label>
                            <p class="text-base text-gray-900 mt-1">{{ $communication->getTargetAudienceLabel() }}</p>
                        </div>
                        
                        @if($communication->target_departments)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Target Departments</label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                @foreach($communication->target_departments as $deptId)
                                    @php
                                        $dept = \App\Models\Department::find($deptId);
                                    @endphp
                                    @if($dept)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $dept->name }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Delivery Method</label>
                            <p class="text-base text-gray-900 mt-1 capitalize">{{ str_replace('_', ' ', $communication->delivery_method) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Attachments -->
                @if($communication->attachments && count($communication->attachments) > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Attachments</h2>
                    <div class="space-y-2">
                        @foreach($communication->attachments as $attachment)
                            <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="flex items-center space-x-2 text-blue-600 hover:text-blue-800">
                                <i class="fas fa-paperclip"></i>
                                <span>{{ basename($attachment) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Current Status</label>
                            <p class="mt-1">
                                {!! $communication->getStatusBadge() !!}
                            </p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Created</label>
                            <p class="text-base text-gray-900 mt-1">{{ $communication->created_at->format('F j, Y g:i A') }}</p>
                        </div>
                        
                        @if($communication->sent_at)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Sent At</label>
                            <p class="text-base text-gray-900 mt-1">{{ $communication->sent_at->format('F j, Y g:i A') }}</p>
                        </div>
                        @endif
                        
                        @if($communication->scheduled_send_time)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Scheduled For</label>
                            <p class="text-base text-gray-900 mt-1">{{ $communication->scheduled_send_time->format('F j, Y g:i A') }}</p>
                        </div>
                        @endif
                        
                        @if($communication->expires_at)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Expires At</label>
                            <p class="text-base text-gray-900 mt-1">{{ $communication->expires_at->format('F j, Y g:i A') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Recipients Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recipients</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Total Recipients</label>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $recipientCount }}</p>
                        </div>
                        
                        @if($communication->requires_acknowledgment)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Acknowledged</label>
                            <p class="text-base text-gray-900 mt-1">
                                {{ $communication->acknowledged_count ?? 0 }} / {{ $communication->total_recipients ?? 0 }}
                            </p>
                            @if($communication->acknowledgment_rate)
                            <div class="mt-2">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $communication->acknowledgment_rate }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ number_format($communication->acknowledgment_rate, 1) }}%</p>
                            </div>
                            @endif
                            
                            @if($communication->acknowledgment_deadline)
                            <p class="text-xs text-gray-500 mt-2">
                                Deadline: {{ $communication->acknowledgment_deadline->format('M j, Y') }}
                            </p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Creator Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Created By</h3>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-semibold">{{ strtoupper(substr($communication->creator->name ?? 'U', 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $communication->creator->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $communication->created_at->format('M j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

