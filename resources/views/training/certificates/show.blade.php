@extends('layouts.app')

@section('title', 'Training Certificate')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Training Certificate</h1>
                    <p class="text-sm text-gray-500 mt-1">Certificate #{{ $certificate->certificate_number }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('training.certificates.generate-pdf', $certificate) }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors" target="_blank">
                        <i class="fas fa-file-pdf mr-2"></i>Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Certificate Preview -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6 border-4 border-yellow-400">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $certificate->company->name ?? 'HSE Management System' }}</h2>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">CERTIFICATE OF COMPLETION</h1>
                <p class="text-lg text-gray-600">This is to certify that</p>
            </div>

            <div class="text-center my-8">
                <div class="text-3xl font-bold text-indigo-600 mb-4 border-b-4 border-yellow-400 inline-block px-8 pb-2">
                    {{ $certificate->user->name ?? 'N/A' }}
                </div>
                <p class="text-lg text-gray-700 mt-6">has successfully completed the training program</p>
                <p class="text-2xl font-bold text-indigo-600 mt-4">
                    {{ $certificate->certificate_title ?? 'Training Program' }}
                </p>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-gray-500">Issue Date</p>
                        <p class="font-medium text-gray-900">{{ $certificate->issue_date->format('F j, Y') }}</p>
                    </div>
                    @if($certificate->has_expiry && $certificate->expiry_date)
                        <div>
                            <p class="text-gray-500">Valid Until</p>
                            <p class="font-medium text-gray-900">{{ $certificate->expiry_date->format('F j, Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-8 flex justify-between items-end">
                <div class="text-center">
                    <div class="border-t-2 border-gray-400 w-48 mx-auto mb-2"></div>
                    <p class="font-medium text-gray-900">{{ $certificate->issuer->name ?? 'Authorized Signatory' }}</p>
                    <p class="text-sm text-gray-500">{{ $certificate->issuing_authority ?? 'Training Manager' }}</p>
                </div>
                <div class="text-center">
                    <div class="border-t-2 border-gray-400 w-48 mx-auto mb-2"></div>
                    <p class="font-medium text-gray-900">{{ $certificate->company->name ?? 'Company' }}</p>
                    <p class="text-sm text-gray-500">{{ $certificate->issue_date->format('F j, Y') }}</p>
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-gray-500">
                Certificate No: {{ $certificate->certificate_number }}
                @if($certificate->verification_code)
                    | Verification Code: {{ $certificate->verification_code }}
                @endif
            </div>
        </div>

        <!-- Certificate Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Certificate Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Certificate Number</p>
                    <p class="font-medium text-gray-900">{{ $certificate->certificate_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="px-2 py-1 text-xs rounded-full {{ $certificate->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($certificate->status) }}
                    </span>
                </div>
                @if($certificate->trainingSession)
                    <div>
                        <p class="text-sm text-gray-500">Training Session</p>
                        <p class="font-medium text-gray-900">{{ $certificate->trainingSession->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Session Date</p>
                        <p class="font-medium text-gray-900">{{ $certificate->trainingSession->scheduled_start->format('F j, Y') }}</p>
                    </div>
                @endif
                <div>
                    <p class="text-sm text-gray-500">Issued By</p>
                    <p class="font-medium text-gray-900">{{ $certificate->issuing_organization ?? 'N/A' }}</p>
                </div>
                @if($certificate->verification_code)
                    <div>
                        <p class="text-sm text-gray-500">Verification Code</p>
                        <p class="font-medium text-gray-900 font-mono">{{ $certificate->verification_code }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
