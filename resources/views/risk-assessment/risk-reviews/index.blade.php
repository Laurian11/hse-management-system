@extends('layouts.app')

@section('title', 'Risk Reviews')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Risk Reviews</h1>
                    <p class="text-sm text-gray-500 mt-1">Scheduled and triggered risk reassessments</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('risk-assessment.risk-reviews.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                        <i class="fas fa-plus mr-2"></i>Schedule Review
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Total Reviews</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Scheduled</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['scheduled'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Triggered</p>
                <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['triggered'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Overdue</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['overdue'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Completed</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['completed'] }}</p>
            </div>
        </div>

        <!-- Reviews Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Risk Assessment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Review Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $review->reference_number }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('risk-assessment.risk-assessments.show', $review->riskAssessment) }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                                    {{ $review->riskAssessment->title }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $review->review_type)) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($review->due_date)
                                    {{ $review->due_date->format('M d, Y') }}
                                    @if($review->isOverdue())
                                        <span class="text-red-600">(Overdue)</span>
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($review->status) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('risk-assessment.risk-reviews.show', $review) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                @if($review->status !== 'completed')
                                    <a href="{{ route('risk-assessment.risk-reviews.edit', $review) }}" class="text-orange-600 hover:text-orange-900">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No risk reviews found. <a href="{{ route('risk-assessment.risk-reviews.create') }}" class="text-blue-600">Create one?</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $reviews->links() }}</div>
    </div>
</div>
@endsection

