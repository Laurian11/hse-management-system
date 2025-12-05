{{-- Breadcrumbs Component --}}
@php
    $breadcrumbs = $breadcrumbs ?? [];
    if (empty($breadcrumbs)) {
        // Auto-generate breadcrumbs from route
        $routeName = Route::currentRouteName();
        $breadcrumbs = [];
        
        // Home
        $breadcrumbs[] = [
            'label' => 'Dashboard',
            'url' => route('dashboard'),
            'icon' => 'fa-home'
        ];
        
        // Special cases - routes that don't follow the module.index pattern
        $specialRoutes = ['dashboard'];
        if (in_array($routeName, $specialRoutes)) {
            // For special routes like 'dashboard', don't show breadcrumbs (it's the home page)
            $breadcrumbs = [];
        } else {
            // Parse route name to generate breadcrumbs
            $parts = explode('.', $routeName);
            $url = '';
            
            foreach ($parts as $index => $part) {
                if ($index === 0) {
                    // First part is usually the module
                    // Try to get the index route, but handle cases where it doesn't exist
                    try {
                        $url = route($part . '.index');
                    } catch (\Exception $e) {
                        // If .index doesn't exist, try the route name itself
                        try {
                            $url = route($part);
                        } catch (\Exception $e2) {
                            $url = null;
                        }
                    }
                    $label = ucfirst(str_replace('-', ' ', $part));
                } else {
                    // Subsequent parts
                    if ($part === 'index') {
                        continue; // Skip index
                    } elseif ($part === 'create') {
                        $label = 'Create';
                    } elseif ($part === 'edit') {
                        $label = 'Edit';
                    } elseif ($part === 'show') {
                        $label = 'View';
                    } else {
                        $label = ucfirst(str_replace('-', ' ', $part));
                    }
                    
                    // Build URL
                    $routeParts = array_slice($parts, 0, $index + 1);
                    $routeNameToTry = implode('.', $routeParts);
                    
                    try {
                        $url = route($routeNameToTry);
                    } catch (\Exception $e) {
                        $url = null;
                    }
                }
                
                // Don't add if it's the last item and it's a show/edit/create
                if ($index === count($parts) - 1 && in_array($part, ['show', 'edit', 'create'])) {
                    $breadcrumbs[] = [
                        'label' => $label,
                        'url' => null,
                        'active' => true
                    ];
                } else {
                    $breadcrumbs[] = [
                        'label' => $label,
                        'url' => $url,
                        'active' => false
                    ];
                }
            }
        }
    }
@endphp

@if(!empty($breadcrumbs) && count($breadcrumbs) > 1)
<nav class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 py-3" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2 text-sm">
        @foreach($breadcrumbs as $index => $breadcrumb)
            <li class="flex items-center">
                @if($index > 0)
                    <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                @endif
                
                @if(isset($breadcrumb['url']) && $breadcrumb['url'] && !($breadcrumb['active'] ?? false))
                    <a href="{{ $breadcrumb['url'] }}" class="text-gray-500 hover:text-gray-700 transition-colors flex items-center">
                        @if(isset($breadcrumb['icon']))
                            <i class="fas {{ $breadcrumb['icon'] }} mr-1"></i>
                        @endif
                        {{ $breadcrumb['label'] }}
                    </a>
                @else
                    <span class="text-gray-900 font-medium flex items-center {{ $breadcrumb['active'] ?? false ? 'text-gray-900' : 'text-gray-500' }}">
                        @if(isset($breadcrumb['icon']) && !($breadcrumb['active'] ?? false))
                            <i class="fas {{ $breadcrumb['icon'] }} mr-1"></i>
                        @endif
                        {{ $breadcrumb['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif

