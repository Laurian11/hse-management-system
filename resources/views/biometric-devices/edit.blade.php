@extends('layouts.app')

@section('title', 'Edit Device')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('biometric-devices.show', $biometricDevice) }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Device
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Device</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="{{ route('biometric-devices.update', $biometricDevice) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Same form structure as create.blade.php but with values pre-filled -->
            @include('biometric-devices.form', ['biometricDevice' => $biometricDevice, 'companies' => $companies])

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('biometric-devices.show', $biometricDevice) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Update Device
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

