{{-- Recent Items Component --}}
@php
    $recentItems = session('recent_items', []);
    if (empty($recentItems)) {
        $recentItems = [];
    }
    // Limit to last 10 items
    $recentItems = array_slice(array_reverse($recentItems), 0, 10);
@endphp

@if(!empty($recentItems))
<div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-2">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center space-x-4 overflow-x-auto">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">Recent:</span>
            <div class="flex items-center space-x-2">
                @foreach($recentItems as $item)
                    <a href="{{ $item['url'] }}" 
                       class="inline-flex items-center space-x-1 px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors whitespace-nowrap"
                       title="{{ $item['title'] }}">
                        @if(isset($item['icon']))
                            <i class="fas {{ $item['icon'] }} text-xs"></i>
                        @endif
                        <span>{{ Str::limit($item['title'], 30) }}</span>
                    </a>
                @endforeach
            </div>
            <button onclick="clearRecentItems()" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" title="Clear recent items">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>

<script>
    function clearRecentItems() {
        if (confirm('Clear all recent items?')) {
            fetch('{{ route("recent-items.clear") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(() => {
                location.reload();
            });
        }
    }
</script>
@endif

