<div class="hierarchy-item {{ $level > 0 ? 'hierarchy-level' : '' }}">
    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors mb-2">
        <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                <i class="fas fa-sitemap text-blue-600"></i>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900">{{ $department->name }}</h3>
                @if($department->code)
                    <p class="text-xs text-gray-500">{{ $department->code }}</p>
                @endif
                @if($department->headOfDepartment)
                    <p class="text-xs text-gray-500">HOD: {{ $department->headOfDepartment->name }}</p>
                @endif
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                {{ $department->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $department->is_active ? 'Active' : 'Inactive' }}
            </span>
            <a href="{{ route('admin.departments.show', $department) }}" class="text-blue-600 hover:text-blue-900">
                <i class="fas fa-eye"></i>
            </a>
        </div>
    </div>
    
    @if(isset($department->children) && count($department->children) > 0)
        <div class="mt-2">
            @foreach($department->children as $child)
                @include('admin.departments.partials.hierarchy-item', ['department' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>

