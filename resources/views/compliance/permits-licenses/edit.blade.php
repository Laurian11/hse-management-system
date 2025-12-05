@extends('layouts.app')

@section('title', 'Edit Permit/License')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('compliance.permits-licenses.show', $permitsLicense) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Permit/License</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('compliance.permits-licenses.update', $permitsLicense) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Permit/License Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-black mb-1">Name *</label>
                    <input type="text" id="name" name="name" required value="{{ old('name', $permitsLicense->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-black mb-1">Type *</label>
                    <select id="type" name="type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="environmental_permit" {{ old('type', $permitsLicense->type) == 'environmental_permit' ? 'selected' : '' }}>Environmental Permit</option>
                        <option value="operating_license" {{ old('type', $permitsLicense->type) == 'operating_license' ? 'selected' : '' }}>Operating License</option>
                        <option value="fire_safety_certificate" {{ old('type', $permitsLicense->type) == 'fire_safety_certificate' ? 'selected' : '' }}>Fire Safety Certificate</option>
                        <option value="building_permit" {{ old('type', $permitsLicense->type) == 'building_permit' ? 'selected' : '' }}>Building Permit</option>
                        <option value="health_permit" {{ old('type', $permitsLicense->type) == 'health_permit' ? 'selected' : '' }}>Health Permit</option>
                        <option value="other" {{ old('type', $permitsLicense->type) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="issue_date" class="block text-sm font-medium text-black mb-1">Issue Date *</label>
                    <input type="date" id="issue_date" name="issue_date" required value="{{ old('issue_date', $permitsLicense->issue_date->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="expiry_date" class="block text-sm font-medium text-black mb-1">Expiry Date</label>
                    <input type="date" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $permitsLicense->expiry_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-black mb-1">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="active" {{ old('status', $permitsLicense->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expired" {{ old('status', $permitsLicense->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="pending_renewal" {{ old('status', $permitsLicense->status) == 'pending_renewal' ? 'selected' : '' }}>Pending Renewal</option>
                        <option value="revoked" {{ old('status', $permitsLicense->status) == 'revoked' ? 'selected' : '' }}>Revoked</option>
                    </select>
                </div>

                <div>
                    <label for="file" class="block text-sm font-medium text-black mb-1">Update File</label>
                    <input type="file" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @if($permitsLicense->attachment_path)
                        <p class="mt-1 text-xs text-gray-500">Current file exists</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('compliance.permits-licenses.show', $permitsLicense) }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Update Permit/License
            </button>
        </div>
    </form>
</div>
@endsection

