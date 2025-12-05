@extends('layouts.app')

@section('title', 'Edit Work Permit')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('work-permits.show', $workPermit) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Work Permit</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('work-permits.update', $workPermit) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Same form structure as create, but with existing values -->
        @include('work-permits.partials.form', ['workPermit' => $workPermit, 'permitTypes' => $permitTypes, 'departments' => $departments, 'users' => $users, 'riskAssessments' => $riskAssessments, 'jsas' => $jsas])

        <div class="flex justify-end space-x-3">
            <a href="{{ route('work-permits.show', $workPermit) }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Update Permit
            </button>
        </div>
    </form>
</div>
@endsection

