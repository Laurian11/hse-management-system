@props(['section' => null])

@php
    // Determine section based on current route if not provided
    if (!$section) {
        if (request()->is('toolbox-talks*') || request()->is('toolbox-topics*')) {
            $section = 'toolbox';
        } elseif (request()->is('incidents*')) {
            $section = 'incidents';
        } elseif (request()->is('safety-communications*')) {
            $section = 'communications';
        } elseif (request()->is('admin/*')) {
            $section = 'admin';
        } elseif (request()->is('dashboard')) {
            $section = 'dashboard';
        } else {
            $section = null;
        }
    }

    // Define navigation items for each section
    $navItems = [
        'toolbox' => [
            ['route' => 'toolbox-talks.index', 'label' => 'Talk Management', 'icon' => 'fa-list', 'pattern' => 'toolbox-talks', 'exclude' => ['toolbox-talks/*']],
            ['route' => 'toolbox-topics.index', 'label' => 'Topic Library', 'icon' => 'fa-book', 'pattern' => 'toolbox-topics*'],
            ['route' => 'toolbox-talks.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-chart-pie', 'pattern' => 'toolbox-talks/dashboard'],
            ['route' => 'toolbox-talks.schedule', 'label' => 'Schedule', 'icon' => 'fa-calendar-plus', 'pattern' => 'toolbox-talks/schedule'],
            ['route' => 'toolbox-talks.calendar', 'label' => 'Calendar', 'icon' => 'fa-calendar-alt', 'pattern' => 'toolbox-talks/calendar'],
            ['route' => 'toolbox-talks.attendance', 'label' => 'Attendance', 'icon' => 'fa-users', 'pattern' => 'toolbox-talks/attendance'],
            ['route' => 'toolbox-talks.feedback', 'label' => 'Feedback', 'icon' => 'fa-comment-dots', 'pattern' => 'toolbox-talks/feedback'],
            ['route' => 'toolbox-talks.reporting', 'label' => 'Reports', 'icon' => 'fa-chart-bar', 'pattern' => 'toolbox-talks/reporting'],
        ],
        'incidents' => [
            ['route' => 'incidents.index', 'label' => 'All Incidents', 'icon' => 'fa-list', 'pattern' => 'incidents', 'exclude' => ['incidents/*']],
            ['route' => 'incidents.create', 'label' => 'Report Incident', 'icon' => 'fa-plus-circle', 'pattern' => 'incidents/create'],
            ['route' => 'incidents.dashboard', 'label' => 'Analytics', 'icon' => 'fa-chart-line', 'pattern' => 'incidents/dashboard'],
        ],
        'communications' => [
            ['route' => 'safety-communications.index', 'label' => 'All Communications', 'icon' => 'fa-list', 'pattern' => 'safety-communications', 'exclude' => ['safety-communications/*']],
            ['route' => 'safety-communications.create', 'label' => 'New Communication', 'icon' => 'fa-plus', 'pattern' => 'safety-communications/create'],
            ['route' => 'safety-communications.dashboard', 'label' => 'Dashboard', 'icon' => 'fa-chart-pie', 'pattern' => 'safety-communications/dashboard'],
        ],
        'admin' => [
            ['route' => 'admin.users.index', 'label' => 'Users', 'icon' => 'fa-users', 'pattern' => 'admin/users*'],
            ['route' => 'admin.companies.index', 'label' => 'Companies', 'icon' => 'fa-building', 'pattern' => 'admin/companies*'],
            ['route' => 'admin.departments.index', 'label' => 'Departments', 'icon' => 'fa-sitemap', 'pattern' => 'admin/departments*'],
            ['route' => 'admin.roles.index', 'label' => 'Roles & Permissions', 'icon' => 'fa-user-shield', 'pattern' => 'admin/roles*'],
            ['route' => 'admin.activity-logs.index', 'label' => 'Activity Logs', 'icon' => 'fa-history', 'pattern' => 'admin/activity-logs*'],
        ],
        'dashboard' => [
            ['route' => 'dashboard', 'label' => 'Overview', 'icon' => 'fa-home', 'pattern' => 'dashboard'],
            ['route' => 'toolbox-talks.dashboard', 'label' => 'Toolbox Talks', 'icon' => 'fa-comments', 'pattern' => 'toolbox-talks/dashboard'],
            ['route' => 'incidents.dashboard', 'label' => 'Incidents', 'icon' => 'fa-exclamation-triangle', 'pattern' => 'incidents/dashboard'],
        ],
    ];
@endphp

@if($section && isset($navItems[$section]))
<div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
    <div class="container mx-auto px-4">
        <nav class="flex space-x-1 overflow-x-auto">
            @foreach($navItems[$section] as $item)
                @php
                    $isActive = false;
                    if (isset($item['pattern'])) {
                        $pattern = $item['pattern'];
                        
                        // Check exclude patterns first
                        if (isset($item['exclude'])) {
                            $excluded = false;
                            foreach ($item['exclude'] as $exclude) {
                                if (request()->is($exclude)) {
                                    $excluded = true;
                                    break;
                                }
                            }
                            if (!$excluded) {
                                $isActive = request()->is($pattern);
                            }
                        } else {
                            $isActive = request()->is($pattern);
                        }
                    } else {
                        $isActive = request()->routeIs($item['route']);
                    }
                @endphp
                <a href="{{ route($item['route']) }}" 
                   class="flex items-center space-x-2 px-4 py-3 text-sm font-medium transition-colors whitespace-nowrap
                   {{ $isActive 
                       ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50' 
                       : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 border-b-2 border-transparent' }}">
                    <i class="fas {{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>
</div>
@endif

