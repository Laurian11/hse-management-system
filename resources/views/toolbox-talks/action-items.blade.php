@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary-black">Action Items - {{ $toolboxTalk->title }}</h1>
        <a href="{{ route('toolbox-talks.show', $toolboxTalk) }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Talk
        </a>
    </div>

    <!-- Action Items Form -->
    <div class="bg-white border border-border-gray p-6 rounded mb-6">
        <h2 class="text-lg font-semibold text-primary-black mb-4">
            <i class="fas fa-tasks mr-2"></i>Manage Action Items
        </h2>
        <form action="{{ route('toolbox-talks.save-action-items', $toolboxTalk) }}" method="POST" id="actionItemsForm">
            @csrf
            <div id="actionItemsContainer">
                @if(!empty($actionItems))
                    @foreach($actionItems as $index => $item)
                        <div class="action-item border border-border-gray p-4 rounded mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-primary-black mb-1">Action Title</label>
                                    <input type="text" name="action_items[{{ $index }}][title]" 
                                           value="{{ $item['title'] ?? '' }}" required
                                           class="w-full border border-border-gray rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-primary-black mb-1">Assigned To</label>
                                    <select name="action_items[{{ $index }}][assigned_to]" 
                                            class="w-full border border-border-gray rounded px-3 py-2">
                                        <option value="">Unassigned</option>
                                        @foreach($attendances as $attendance)
                                            <option value="{{ $attendance->employee_id }}" 
                                                    {{ ($item['assigned_to'] ?? '') == $attendance->employee_id ? 'selected' : '' }}>
                                                {{ $attendance->employee_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-primary-black mb-1">Priority</label>
                                    <select name="action_items[{{ $index }}][priority]" 
                                            class="w-full border border-border-gray rounded px-3 py-2">
                                        <option value="low" {{ ($item['priority'] ?? '') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ ($item['priority'] ?? '') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ ($item['priority'] ?? '') == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-primary-black mb-1">Due Date</label>
                                    <input type="date" name="action_items[{{ $index }}][due_date]" 
                                           value="{{ $item['due_date'] ?? '' }}"
                                           class="w-full border border-border-gray rounded px-3 py-2">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-primary-black mb-1">Description</label>
                                    <textarea name="action_items[{{ $index }}][description]" rows="2"
                                              class="w-full border border-border-gray rounded px-3 py-2">{{ $item['description'] ?? '' }}</textarea>
                                </div>
                                <div class="md:col-span-4 flex justify-end">
                                    <button type="button" onclick="removeActionItem(this)" class="text-red-600 hover:text-red-700">
                                        <i class="fas fa-trash mr-1"></i>Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="flex gap-3 mt-4">
                <button type="button" onclick="addActionItem()" class="btn-secondary">
                    <i class="fas fa-plus mr-2"></i>Add Action Item
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Save Action Items
                </button>
            </div>
        </form>
    </div>

    <!-- Assigned Actions Summary -->
    <div class="bg-white border border-border-gray p-6 rounded">
        <h2 class="text-lg font-semibold text-primary-black mb-4">Assigned Actions Summary</h2>
        <div class="space-y-3">
            @foreach($attendances as $attendance)
                @if(!empty($attendance->assigned_actions))
                    <div class="border border-border-gray p-4 rounded">
                        <div class="font-medium text-primary-black mb-2">
                            {{ $attendance->employee_name }}
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-sm text-medium-gray">
                            @foreach($attendance->assigned_actions as $action)
                                <li>{{ $action['title'] ?? 'Untitled Action' }}</li>
                            @endforeach
                        </ul>
                        @if($attendance->action_acknowledged)
                            <div class="mt-2 text-sm text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>Acknowledged
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

<script>
let actionItemIndex = {{ count($actionItems ?? []) }};

function addActionItem() {
    const container = document.getElementById('actionItemsContainer');
    const html = `
        <div class="action-item border border-border-gray p-4 rounded mb-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-primary-black mb-1">Action Title</label>
                    <input type="text" name="action_items[${actionItemIndex}][title]" required
                           class="w-full border border-border-gray rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary-black mb-1">Assigned To</label>
                    <select name="action_items[${actionItemIndex}][assigned_to]" 
                            class="w-full border border-border-gray rounded px-3 py-2">
                        <option value="">Unassigned</option>
                        @foreach($attendances as $attendance)
                            <option value="{{ $attendance->employee_id }}">{{ $attendance->employee_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary-black mb-1">Priority</label>
                    <select name="action_items[${actionItemIndex}][priority]" 
                            class="w-full border border-border-gray rounded px-3 py-2">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-primary-black mb-1">Due Date</label>
                    <input type="date" name="action_items[${actionItemIndex}][due_date]"
                           class="w-full border border-border-gray rounded px-3 py-2">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-primary-black mb-1">Description</label>
                    <textarea name="action_items[${actionItemIndex}][description]" rows="2"
                              class="w-full border border-border-gray rounded px-3 py-2"></textarea>
                </div>
                <div class="md:col-span-4 flex justify-end">
                    <button type="button" onclick="removeActionItem(this)" class="text-red-600 hover:text-red-700">
                        <i class="fas fa-trash mr-1"></i>Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    actionItemIndex++;
}

function removeActionItem(button) {
    button.closest('.action-item').remove();
}
</script>
@endsection

