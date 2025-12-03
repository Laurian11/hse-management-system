@extends('layouts.app')

@section('title', 'Department Hierarchy')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <h1 class="text-2xl font-bold text-gray-900">Department Hierarchy</h1>
            <a href="{{ route('admin.departments.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-list mr-2"></i>All Departments
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Hierarchy Tree -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Department Structure</h2>
        
        @if(count($hierarchy) > 0)
            <div class="space-y-4">
                @foreach($hierarchy as $dept)
                    @include('admin.departments.partials.hierarchy-item', ['department' => $dept, 'level' => 0])
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-sitemap text-gray-400 text-6xl mb-4"></i>
                <p class="text-gray-500">No departments found. Create departments to see the hierarchy.</p>
                <a href="{{ route('admin.departments.create') }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Create Department
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.hierarchy-level {
    margin-left: 2rem;
    border-left: 2px solid #e5e7eb;
    padding-left: 1rem;
}
</style>
@endsection

