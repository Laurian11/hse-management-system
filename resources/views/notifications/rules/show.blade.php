@extends('layouts.app')

@section('title', 'Notification Rule: ' . $rule->name)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('notifications.rules.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $rule->name }}</h1>
                    <p class="text-sm text-gray-500">Notification Rule</p>
                </div>
            </div>
            <div>
                <a href="{{ route('notifications.rules.edit', $rule) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Rule Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-black">{{ $rule->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Trigger Type</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $rule->trigger_type)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Notification Channel</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst($rule->notification_channel) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $rule->is_active ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-gray-500 border-gray-500' }}">
                                {{ $rule->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>
                    </div>
                    @if($rule->days_before)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Days Before Event</dt>
                        <dd class="mt-1 text-sm text-black">{{ $rule->days_before }} days</dd>
                    </div>
                    @endif
                    @if($rule->frequency)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Frequency</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst($rule->frequency) }}</dd>
                    </div>
                    @endif
                    @if($rule->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-black">{{ $rule->description }}</dd>
                    </div>
                    @endif
                    @if($rule->message_template)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Message Template</dt>
                        <dd class="mt-1 text-sm text-black">{{ $rule->message_template }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Details</h3>
                <dl class="space-y-3">
                    @if($rule->creator)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $rule->creator->name }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $rule->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

