@extends('layouts.app')

@section('title', 'Create Work Permit')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('work-permits.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Work Permit</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('work-permits.store') }}" method="POST" class="space-y-6">
        @csrf

        @include('work-permits.partials.form', ['workPermit' => null, 'permitTypes' => $permitTypes, 'departments' => $departments, 'users' => $users, 'riskAssessments' => $riskAssessments, 'jsas' => $jsas])

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('work-permits.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Permit
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle gas test fields
    const gasTestCheckbox = document.querySelector('input[name="gas_test_required"]');
    const gasTestFields = document.getElementById('gas_test_fields');
    
    if (gasTestCheckbox) {
        gasTestCheckbox.addEventListener('change', function() {
            gasTestFields.classList.toggle('hidden', !this.checked);
        });
        // Trigger on load if checked
        if (gasTestCheckbox.checked) {
            gasTestFields.classList.remove('hidden');
        }
    }

    // Toggle fire watch fields
    const fireWatchCheckbox = document.querySelector('input[name="fire_watch_required"]');
    const fireWatchFields = document.getElementById('fire_watch_fields');
    
    if (fireWatchCheckbox) {
        fireWatchCheckbox.addEventListener('change', function() {
            fireWatchFields.classList.toggle('hidden', !this.checked);
        });
        // Trigger on load if checked
        if (fireWatchCheckbox.checked) {
            fireWatchFields.classList.remove('hidden');
        }
    }
});
</script>
@endpush
@endsection

