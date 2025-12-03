@extends('layouts.app')

@section('title', 'Risk Register')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Risk Register</h1>
                    <p class="text-sm text-gray-500 mt-1">Central repository of all risk assessments</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('risk-assessment.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-chart-pie mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('risk-assessment.risk-assessments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>New Assessment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Total Assessments</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">High Risk</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['high_risk'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Due for Review</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['due_for_review'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Approved</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['approved'] }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <select name="risk_level" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Risk Levels</option>
                    <option value="low" {{ request('risk_level') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('risk_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('risk_level') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ request('risk_level') == 'critical' ? 'selected' : '' }}>Critical</option>
                    <option value="extreme" {{ request('risk_level') == 'extreme' ? 'selected' : '' }}>Extreme</option>
                </select>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="implementation" {{ request('status') == 'implementation' ? 'selected' : '' }}>Implementation</option>
                </select>
                <select name="department_id" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Filter</button>
            </form>
        </div>

        <!-- Risk Assessments Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Risk Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Next Review</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($riskAssessments as $assessment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $assessment->reference_number }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $assessment->title }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($assessment->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full {{ $assessment->getRiskLevelColor() }}">
                                    {{ strtoupper($assessment->risk_level) }} ({{ $assessment->risk_score }})
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($assessment->status) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($assessment->next_review_date)
                                    {{ $assessment->next_review_date->format('M d, Y') }}
                                    @if($assessment->isOverdueForReview())
                                        <span class="ml-2 text-red-600">(Overdue)</span>
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('risk-assessment.risk-assessments.show', $assessment) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="{{ route('risk-assessment.risk-assessments.edit', $assessment) }}" class="text-orange-600 hover:text-orange-900">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No risk assessments found. <a href="{{ route('risk-assessment.risk-assessments.create') }}" class="text-blue-600">Create one?</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $riskAssessments->links() }}</div>
    </div>
</div>
@endsection

