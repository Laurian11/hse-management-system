@extends('layouts.app')

@section('title', 'Work Permit Types')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Work Permit Types</h1>
                <p class="text-sm text-gray-500 mt-1">Manage permit type definitions</p>
            </div>
            <div>
                <a href="{{ route('work-permits.types.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Type
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Validity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Requirements</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Approval Levels</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($types as $type)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-black">{{ $type->name }}</div>
                            @if($type->description)
                                <div class="text-xs text-gray-500">{{ Str::limit($type->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-black">{{ $type->code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-black">{{ $type->default_validity_hours }}h (max: {{ $type->max_validity_hours }}h)</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @if($type->requires_risk_assessment)
                                    <span class="px-2 py-1 text-xs border border-gray-300 bg-[#F5F5F5]">RA</span>
                                @endif
                                @if($type->requires_jsa)
                                    <span class="px-2 py-1 text-xs border border-gray-300 bg-[#F5F5F5]">JSA</span>
                                @endif
                                @if($type->requires_gas_test)
                                    <span class="px-2 py-1 text-xs border border-gray-300 bg-[#F5F5F5]">Gas</span>
                                @endif
                                @if($type->requires_fire_watch)
                                    <span class="px-2 py-1 text-xs border border-gray-300 bg-[#F5F5F5]">Fire</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-black">{{ $type->approval_levels }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300 {{ $type->is_active ? 'bg-[#F5F5F5] text-black' : 'bg-[#F5F5F5] text-gray-500' }}">
                                {{ $type->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('work-permits.types.show', $type) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('work-permits.types.edit', $type) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No permit types found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

