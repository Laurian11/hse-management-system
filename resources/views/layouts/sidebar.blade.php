<!-- Sidebar -->
<aside class="w-64 bg-white shadow-lg border-r border-gray-200 fixed left-0 top-0 h-full z-10 lg:translate-x-0 -translate-x-full transition-all duration-300 overflow-y-auto" id="sidebar">
    <div class="flex flex-col h-full">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
            <div class="flex items-center space-x-2 sidebar-brand">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg flex items-center justify-center shadow-md">
                    <i class="fas fa-shield-alt text-white text-lg"></i>
                </div>
                <div class="sidebar-text">
                    <span class="text-lg font-bold text-primary-black">HSE System</span>
                    <div class="text-xs text-medium-gray font-normal">Safety Management</div>
                </div>
            </div>
            <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 p-2 rounded-lg transition-colors sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <!-- Quick Actions -->
        <div class="p-4 border-b border-gray-200 bg-gray-50 sidebar-quick-actions">
            <div class="grid grid-cols-2 gap-2 sidebar-text">
                <a href="{{ route('incidents.create') }}" class="flex items-center justify-center space-x-1 px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg text-xs font-medium transition-colors" data-tooltip="Report Incident">
                    <i class="fas fa-plus text-xs"></i>
                    <span class="sidebar-text">Report</span>
                </a>
                <a href="{{ route('toolbox-talks.create') }}" class="flex items-center justify-center space-x-1 px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-medium transition-colors" data-tooltip="New Talk">
                    <i class="fas fa-plus text-xs"></i>
                    <span class="sidebar-text">New Talk</span>
                </a>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ request()->is('dashboard') ? 'bg-blue-50 text-blue-700 shadow-sm' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Dashboard">
                <i class="fas fa-home w-5 text-center"></i>
                <span class="sidebar-text font-medium">Dashboard</span>
            </a>
            
            <!-- Toolbox Module - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('toolbox')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-hard-hat text-blue-600"></i>
                        <span class="sidebar-text">Toolbox Module</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="toolbox-chevron"></i>
                </button>
                <div id="toolbox-section" class="space-y-1 pl-4 border-l-2 border-blue-100">
                    <a href="{{ route('toolbox-talks.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('toolbox-talks') && !request()->is('toolbox-talks/*') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="All Talks">
                        <i class="fas fa-list w-5 text-center text-blue-600"></i>
                        <span class="sidebar-text">All Talks</span>
                    </a>
                    <a href="{{ route('toolbox-talks.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('toolbox-talks/dashboard') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center text-blue-600"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('toolbox-talks.schedule') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('toolbox-talks/schedule') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Schedule">
                        <i class="fas fa-calendar-plus w-5 text-center text-green-600"></i>
                        <span class="sidebar-text">Schedule</span>
                    </a>
                    <a href="{{ route('toolbox-talks.calendar') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('toolbox-talks/calendar') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Calendar">
                        <i class="fas fa-calendar-alt w-5 text-center text-purple-600"></i>
                        <span class="sidebar-text">Calendar</span>
                    </a>
                    <a href="{{ route('toolbox-talks.attendance') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('toolbox-talks/attendance') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Attendance">
                        <i class="fas fa-users w-5 text-center text-indigo-600"></i>
                        <span class="sidebar-text">Attendance</span>
                    </a>
                    <a href="{{ route('toolbox-talks.feedback') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('toolbox-talks/feedback') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Feedback">
                        <i class="fas fa-comment-dots w-5 text-center text-pink-600"></i>
                        <span class="sidebar-text">Feedback</span>
                    </a>
                    <a href="{{ route('toolbox-talks.reporting') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('toolbox-talks/reporting') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Reports">
                        <i class="fas fa-chart-bar w-5 text-center text-yellow-600"></i>
                        <span class="sidebar-text">Reports</span>
                    </a>
                    <a href="{{ route('toolbox-topics.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('toolbox-topics*') ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Topics">
                        <i class="fas fa-book w-5 text-center text-green-600"></i>
                        <span class="sidebar-text">Topics Library</span>
                    </a>
                </div>
            </div>
            
            <!-- Incident Management - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('incidents')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                        <span class="sidebar-text">Incident Management</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="incidents-chevron"></i>
                </button>
                <div id="incidents-section" class="space-y-1 pl-4 border-l-2 border-red-100">
                    <a href="{{ route('incidents.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('incidents') && !request()->is('incidents/*') ? 'bg-red-50 text-red-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="All Incidents">
                        <i class="fas fa-list w-5 text-center text-red-600"></i>
                        <span class="sidebar-text">All Incidents</span>
                    </a>
                    <a href="{{ route('incidents.create') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('incidents/create') ? 'bg-red-50 text-red-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Report">
                        <i class="fas fa-plus-circle w-5 text-center text-red-600"></i>
                        <span class="sidebar-text">Report Incident</span>
                    </a>
                    <a href="{{ route('incidents.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('incidents/dashboard') ? 'bg-red-50 text-red-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center text-red-600"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('incidents.trend-analysis') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('incidents/trend-analysis') ? 'bg-red-50 text-red-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Trend Analysis">
                        <i class="fas fa-chart-line w-5 text-center text-orange-600"></i>
                        <span class="sidebar-text">Trend Analysis</span>
                    </a>
                    <div class="pt-2 mt-2 border-t border-red-100">
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 py-1 mb-1 sidebar-text">Investigation</div>
                        <a href="{{ route('incidents.index') }}?filter=investigating" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->get('filter') == 'investigating' ? 'bg-red-50 text-red-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Investigations">
                            <i class="fas fa-search w-5 text-center text-blue-600"></i>
                            <span class="sidebar-text">Investigations</span>
                        </a>
                        <a href="{{ route('incidents.index') }}?filter=rca" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->get('filter') == 'rca' ? 'bg-red-50 text-red-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Root Cause">
                            <i class="fas fa-project-diagram w-5 text-center text-purple-600"></i>
                            <span class="sidebar-text">Root Cause Analysis</span>
                        </a>
                        <a href="{{ route('incidents.index') }}?filter=capa" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->get('filter') == 'capa' ? 'bg-red-50 text-red-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="CAPAs">
                            <i class="fas fa-tasks w-5 text-center text-green-600"></i>
                            <span class="sidebar-text">CAPAs</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Risk Assessment & Hazard Management - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('risk-assessment')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-shield-alt text-orange-600"></i>
                        <span class="sidebar-text">Risk Assessment</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="risk-assessment-chevron"></i>
                </button>
                <div id="risk-assessment-section" class="space-y-1 pl-4 border-l-2 border-orange-100">
                    <a href="{{ route('risk-assessment.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('risk-assessment/dashboard') ? 'bg-orange-50 text-orange-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center text-orange-600"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('risk-assessment.hazards.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('risk-assessment/hazards*') ? 'bg-orange-50 text-orange-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Hazards">
                        <i class="fas fa-exclamation-triangle w-5 text-center text-red-600"></i>
                        <span class="sidebar-text">Hazards (HAZID)</span>
                    </a>
                    <a href="{{ route('risk-assessment.risk-assessments.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('risk-assessment/risk-assessments*') ? 'bg-orange-50 text-orange-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Risk Register">
                        <i class="fas fa-clipboard-list w-5 text-center text-orange-600"></i>
                        <span class="sidebar-text">Risk Register</span>
                    </a>
                    <a href="{{ route('risk-assessment.jsas.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('risk-assessment/jsas*') ? 'bg-orange-50 text-orange-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="JSA">
                        <i class="fas fa-tasks w-5 text-center text-blue-600"></i>
                        <span class="sidebar-text">Job Safety Analysis</span>
                    </a>
                    <a href="{{ route('risk-assessment.control-measures.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('risk-assessment/control-measures*') ? 'bg-orange-50 text-orange-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Controls">
                        <i class="fas fa-shield-alt w-5 text-center text-green-600"></i>
                        <span class="sidebar-text">Control Measures</span>
                    </a>
                    <a href="{{ route('risk-assessment.risk-reviews.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('risk-assessment/risk-reviews*') ? 'bg-orange-50 text-orange-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Reviews">
                        <i class="fas fa-sync-alt w-5 text-center text-purple-600"></i>
                        <span class="sidebar-text">Risk Reviews</span>
                    </a>
                </div>
            </div>
            
            <!-- Safety Communications -->
            <a href="{{ route('safety-communications.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ request()->is('safety-communications*') ? 'bg-purple-50 text-purple-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Communications">
                <i class="fas fa-bullhorn w-5 text-center text-purple-600"></i>
                <span class="sidebar-text font-medium">Communications</span>
            </a>
            
            <!-- Administration - Collapsible -->
            @if(Auth::user()->role && (Auth::user()->role->name === 'super_admin' || Auth::user()->role->name === 'admin'))
            <div class="space-y-1">
                <button onclick="toggleSection('admin')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-cog text-gray-600"></i>
                        <span class="sidebar-text">Administration</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="admin-chevron"></i>
                </button>
                <div id="admin-section" class="space-y-1 pl-4 border-l-2 border-gray-100">
                    <a href="{{ route('admin.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('admin') && !request()->is('admin/*') ? 'bg-gray-50 text-gray-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Admin Dashboard">
                        <i class="fas fa-tachometer-alt w-5 text-center text-gray-600"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.employees.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('admin/employees*') ? 'bg-gray-50 text-gray-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Employees">
                        <i class="fas fa-user-tie w-5 text-center text-gray-600"></i>
                        <span class="sidebar-text">Employees</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('admin/users*') ? 'bg-gray-50 text-gray-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Users">
                        <i class="fas fa-users w-5 text-center text-gray-600"></i>
                        <span class="sidebar-text">Users</span>
                    </a>
                    <a href="{{ route('admin.companies.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('admin/companies*') ? 'bg-gray-50 text-gray-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Companies">
                        <i class="fas fa-building w-5 text-center text-gray-600"></i>
                        <span class="sidebar-text">Companies</span>
                    </a>
                    <a href="{{ route('admin.departments.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('admin/departments*') ? 'bg-gray-50 text-gray-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Departments">
                        <i class="fas fa-sitemap w-5 text-center text-gray-600"></i>
                        <span class="sidebar-text">Departments</span>
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('admin/roles*') ? 'bg-gray-50 text-gray-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Roles">
                        <i class="fas fa-user-shield w-5 text-center text-gray-600"></i>
                        <span class="sidebar-text">Roles & Permissions</span>
                    </a>
                    <a href="{{ route('admin.activity-logs.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 rounded-lg transition-all {{ request()->is('admin/activity-logs*') ? 'bg-gray-50 text-gray-700' : 'hover:bg-gray-100 text-gray-700' }}" data-tooltip="Activity Logs">
                        <i class="fas fa-history w-5 text-center text-gray-600"></i>
                        <span class="sidebar-text">Activity Logs</span>
                    </a>
                </div>
            </div>
            @endif
        </nav>
        
        <!-- User Info -->
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center space-x-3 mb-3 sidebar-user-info">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center shadow-md">
                    <span class="text-white font-semibold text-sm">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                </div>
                <div class="flex-1 sidebar-user-info">
                    <div class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-gray-500">{{ Auth::user()->role->display_name ?? Auth::user()->role->name ?? 'User' }}</div>
                    @if(Auth::user()->department)
                    <div class="text-xs text-gray-400 mt-0.5">{{ Auth::user()->department->name }}</div>
                    @endif
                </div>
            </div>
            <div class="flex space-x-2 sidebar-text">
                <a href="#" onclick="alert('Profile settings coming soon!'); return false;" class="flex-1 flex items-center justify-center space-x-1 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50 transition-colors" data-tooltip="Profile">
                    <i class="fas fa-user-cog"></i>
                    <span class="sidebar-text">Profile</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-1 px-3 py-2 bg-red-500 text-white rounded-lg text-xs font-medium hover:bg-red-600 transition-colors" data-tooltip="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="sidebar-text">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

<style>
.sidebar-collapsed {
    width: 4rem !important;
}

.sidebar-collapsed .sidebar-text {
    display: none !important;
}

.sidebar-collapsed .sidebar-user-info {
    display: none !important;
}

.sidebar-collapsed .sidebar-brand > div:last-child {
    display: none;
}

.sidebar-collapsed .sidebar-brand {
    justify-content: center;
}

.sidebar-collapsed .sidebar-toggle i {
    transform: rotate(180deg);
}

.sidebar-collapsed .sidebar-toggle {
    position: absolute;
    right: 0.5rem;
    top: 1rem;
}

.sidebar-collapsed .sidebar-quick-actions {
    padding: 0.5rem;
}

.sidebar-collapsed .sidebar-quick-actions .grid {
    grid-template-columns: 1fr;
}

.sidebar-collapsed .sidebar-nav-item {
    justify-content: center;
    padding: 0.75rem;
}

.sidebar-collapsed .sidebar-nav-item i {
    margin: 0;
}

.sidebar-collapsed .space-y-1 > button {
    padding: 0.5rem;
    justify-content: center;
}

.sidebar-collapsed .space-y-1 > button > div {
    display: none;
}

.sidebar-collapsed .space-y-1 > button > i:last-child {
    display: none;
}

.sidebar-collapsed #toolbox-section,
.sidebar-collapsed #incidents-section,
.sidebar-collapsed #admin-section {
    padding-left: 0;
    border-left: none;
}

.sidebar-collapsed .pl-4 {
    padding-left: 0;
}

/* Tooltip styles */
.sidebar-collapsed .sidebar-nav-item,
.sidebar-collapsed button[onclick*="toggleSection"],
.sidebar-collapsed .sidebar-quick-actions a {
    position: relative;
}

.sidebar-collapsed .sidebar-nav-item::after,
.sidebar-collapsed button[onclick*="toggleSection"]::after,
.sidebar-collapsed .sidebar-quick-actions a::after {
    content: attr(data-tooltip);
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    background: #1f2937;
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s, visibility 0.2s;
    z-index: 50;
    margin-left: 0.5rem;
    pointer-events: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.sidebar-collapsed .sidebar-nav-item:hover::after,
.sidebar-collapsed button[onclick*="toggleSection"]:hover::after,
.sidebar-collapsed .sidebar-quick-actions a:hover::after {
    opacity: 1;
    visibility: visible;
}

.sidebar-toggle i {
    transition: transform 0.3s ease;
}

.section-collapsed {
    display: none !important;
}

.section-chevron {
    transition: transform 0.3s ease;
}

.section-chevron.rotated {
    transform: rotate(-90deg);
}
</style>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('sidebar-collapsed');
    
    const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
    localStorage.setItem('sidebarCollapsed', isCollapsed);
    
    // Update main content margin
    const mainContent = document.getElementById('main-content');
    if (mainContent) {
        if (isCollapsed) {
            mainContent.classList.remove('lg:ml-64');
            mainContent.classList.add('lg:ml-16');
        } else {
            mainContent.classList.remove('lg:ml-16');
            mainContent.classList.add('lg:ml-64');
        }
    }
}

function toggleSection(sectionName) {
    const section = document.getElementById(sectionName + '-section');
    const chevron = document.getElementById(sectionName + '-chevron');
    
    if (section && chevron) {
        section.classList.toggle('section-collapsed');
        chevron.classList.toggle('rotated');
        
        // Save state
        const isCollapsed = section.classList.contains('section-collapsed');
        localStorage.setItem('section-' + sectionName + '-collapsed', isCollapsed);
    }
}

// Restore sidebar and section states on page load
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    
    if (isCollapsed) {
        sidebar.classList.add('sidebar-collapsed');
        const mainContent = document.getElementById('main-content');
        if (mainContent) {
            mainContent.classList.remove('lg:ml-64');
            mainContent.classList.add('lg:ml-16');
        }
    }
    
    // Restore section states
    ['toolbox', 'incidents', 'admin'].forEach(sectionName => {
        const section = document.getElementById(sectionName + '-section');
        const chevron = document.getElementById(sectionName + '-chevron');
        const isSectionCollapsed = localStorage.getItem('section-' + sectionName + '-collapsed') === 'true';
        
        if (section && chevron) {
            if (isSectionCollapsed) {
                section.classList.add('section-collapsed');
                chevron.classList.add('rotated');
            } else {
                // Default: expand toolbox and incidents sections
                if (sectionName === 'toolbox' || sectionName === 'incidents') {
                    section.classList.remove('section-collapsed');
                    chevron.classList.remove('rotated');
                }
            }
        }
    });
});
</script>
