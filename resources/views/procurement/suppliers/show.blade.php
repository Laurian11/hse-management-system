@extends('layouts.app')

@section('title', 'Supplier: ' . $supplier->name)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('procurement.suppliers.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $supplier->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $supplier->supplier_type ?? 'N/A' }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('procurement.suppliers.edit', $supplier) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
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
                <h2 class="text-lg font-semibold text-black mb-4">Supplier Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Supplier Name</dt>
                        <dd class="mt-1 text-sm text-black">{{ $supplier->name }}</dd>
                    </div>
                    @if($supplier->contact_person)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Contact Person</dt>
                        <dd class="mt-1 text-sm text-black">{{ $supplier->contact_person }}</dd>
                    </div>
                    @endif
                    @if($supplier->email)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-black">{{ $supplier->email }}</dd>
                    </div>
                    @endif
                    @if($supplier->phone)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="mt-1 text-sm text-black">{{ $supplier->phone }}</dd>
                    </div>
                    @endif
                    @if($supplier->address)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                        <dd class="mt-1 text-sm text-black">{{ $supplier->address }}</dd>
                    </div>
                    @endif
                    @if($supplier->supplier_type)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Supplier Type</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $supplier->supplier_type)) }}</dd>
                    </div>
                    @endif
                    @if($supplier->tax_id)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tax ID / Registration</dt>
                        <dd class="mt-1 text-sm text-black">{{ $supplier->tax_id }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $supplier->status == 'active' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-gray-500' }}">
                                {{ ucfirst($supplier->status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Quick Info</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-black">{{ $supplier->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-black">{{ $supplier->updated_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>

            @if($supplier->notes)
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Notes</h3>
                <p class="text-sm text-black">{{ $supplier->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

