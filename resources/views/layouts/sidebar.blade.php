<!-- Sidebar -->
<aside class="w-64 bg-white border-r border-gray-300 fixed left-0 top-0 h-full z-10 lg:translate-x-0 -translate-x-full transition-all duration-300 overflow-y-auto" id="sidebar">
    <div class="flex flex-col h-full">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-300 bg-white">
            <div class="flex items-center space-x-2 sidebar-brand">
                <div class="w-10 h-10 bg-[#0066CC] flex items-center justify-center">
                    <i class="fas fa-shield-alt text-white text-lg"></i>
                </div>
                <div class="sidebar-text">
                    <span class="text-lg font-bold text-black">HSE System</span>
                    <div class="text-xs text-gray-500 font-normal">Safety Management</div>
                </div>
            </div>
            <button onclick="toggleSidebar()" class="text-gray-500 hover:text-black hover:bg-[#F5F5F5] p-2 transition-colors sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <!-- Quick Actions -->
        <div class="p-4 border-b border-gray-300 bg-[#F5F5F5] sidebar-quick-actions">
            <div class="grid grid-cols-2 gap-2 sidebar-text" style="grid-template-columns: repeat(2, 1fr);">
                <a href="{{ route('incidents.create') }}" class="flex items-center justify-center space-x-1 px-3 py-2 bg-white hover:bg-[#F5F5F5] text-black border border-gray-300 text-xs font-medium transition-colors" data-tooltip="Report Incident">
                    <i class="fas fa-plus text-xs"></i>
                    <span class="sidebar-text">Report</span>
                </a>
                <a href="{{ route('toolbox-talks.create') }}" class="flex items-center justify-center space-x-1 px-3 py-2 bg-white hover:bg-[#F5F5F5] text-black border border-gray-300 text-xs font-medium transition-colors" data-tooltip="New Talk">
                    <i class="fas fa-plus text-xs"></i>
                    <span class="sidebar-text">New Talk</span>
                </a>
                <a href="{{ route('risk-assessment.jsas.create') }}" class="flex items-center justify-center space-x-1 px-3 py-2 bg-white hover:bg-[#F5F5F5] text-black border border-gray-300 text-xs font-medium transition-colors" data-tooltip="New JSA">
                    <i class="fas fa-plus text-xs"></i>
                    <span class="sidebar-text">New JSA</span>
                </a>
                <a href="{{ route('training.training-plans.create') }}" class="flex items-center justify-center space-x-1 px-3 py-2 bg-white hover:bg-[#F5F5F5] text-black border border-gray-300 text-xs font-medium transition-colors" data-tooltip="New Training Plan">
                    <i class="fas fa-plus text-xs"></i>
                    <span class="sidebar-text">Training</span>
                </a>
                <a href="{{ route('ppe.items.create') }}" class="flex items-center justify-center space-x-1 px-3 py-2 bg-white hover:bg-[#F5F5F5] text-black border border-gray-300 text-xs font-medium transition-colors" data-tooltip="New PPE Item">
                    <i class="fas fa-plus text-xs"></i>
                    <span class="sidebar-text">PPE Item</span>
                </a>
                <a href="{{ route('ppe.issuances.create') }}" class="flex items-center justify-center space-x-1 px-3 py-2 bg-white hover:bg-[#F5F5F5] text-black border border-gray-300 text-xs font-medium transition-colors" data-tooltip="Issue PPE">
                    <i class="fas fa-plus text-xs"></i>
                    <span class="sidebar-text">Issue PPE</span>
                </a>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2.5 transition-all {{ request()->is('dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
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
                <div id="toolbox-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('toolbox-talks.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('toolbox-talks') && !request()->is('toolbox-talks/*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="All Talks">
                        <i class="fas fa-list w-5 text-center"></i>
                        <span class="sidebar-text">All Talks</span>
                    </a>
                    <a href="{{ route('toolbox-talks.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('toolbox-talks/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('toolbox-talks.schedule') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('toolbox-talks/schedule') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Schedule">
                        <i class="fas fa-calendar-plus w-5 text-center"></i>
                        <span class="sidebar-text">Schedule</span>
                    </a>
                    <a href="{{ route('toolbox-talks.calendar') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('toolbox-talks/calendar') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Calendar">
                        <i class="fas fa-calendar-alt w-5 text-center"></i>
                        <span class="sidebar-text">Calendar</span>
                    </a>
                    <a href="{{ route('toolbox-talks.attendance') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('toolbox-talks/attendance') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Attendance">
                        <i class="fas fa-users w-5 text-center"></i>
                        <span class="sidebar-text">Attendance</span>
                    </a>
                    <a href="{{ route('toolbox-talks.feedback') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('toolbox-talks/feedback') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Feedback">
                        <i class="fas fa-comment-dots w-5 text-center"></i>
                        <span class="sidebar-text">Feedback</span>
                    </a>
                    <a href="{{ route('toolbox-talks.reporting') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('toolbox-talks/reporting') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Reports">
                        <i class="fas fa-chart-bar w-5 text-center"></i>
                        <span class="sidebar-text">Reports</span>
                    </a>
                    <a href="{{ route('toolbox-topics.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('toolbox-topics*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Topics">
                        <i class="fas fa-book w-5 text-center"></i>
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
                <div id="incidents-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('incidents.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('incidents') && !request()->is('incidents/*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="All Incidents">
                        <i class="fas fa-list w-5 text-center"></i>
                        <span class="sidebar-text">All Incidents</span>
                    </a>
                    <a href="{{ route('incidents.create') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('incidents/create') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Report">
                        <i class="fas fa-plus-circle w-5 text-center"></i>
                        <span class="sidebar-text">Report Incident</span>
                    </a>
                    <a href="{{ route('incidents.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('incidents/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('incidents.trend-analysis') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('incidents/trend-analysis') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Trend Analysis">
                        <i class="fas fa-chart-line w-5 text-center"></i>
                        <span class="sidebar-text">Trend Analysis</span>
                    </a>
                    <div class="pt-2 mt-2 border-t border-gray-300">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 py-1 mb-1 sidebar-text">Investigation</div>
                        <a href="{{ route('incidents.index') }}?filter=investigating" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->get('filter') == 'investigating' ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Investigations">
                            <i class="fas fa-search w-5 text-center"></i>
                            <span class="sidebar-text">Investigations</span>
                        </a>
                        <a href="{{ route('incidents.index') }}?filter=rca" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->get('filter') == 'rca' ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Root Cause">
                            <i class="fas fa-project-diagram w-5 text-center"></i>
                            <span class="sidebar-text">Root Cause Analysis</span>
                        </a>
                        <a href="{{ route('incidents.index') }}?filter=capa" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->get('filter') == 'capa' ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="CAPAs">
                            <i class="fas fa-tasks w-5 text-center"></i>
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
                <div id="risk-assessment-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('risk-assessment.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('risk-assessment/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('risk-assessment.hazards.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('risk-assessment/hazards*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Hazards">
                        <i class="fas fa-exclamation-triangle w-5 text-center"></i>
                        <span class="sidebar-text">Hazards (HAZID)</span>
                    </a>
                    <a href="{{ route('risk-assessment.risk-assessments.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('risk-assessment/risk-assessments*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Risk Register">
                        <i class="fas fa-clipboard-list w-5 text-center"></i>
                        <span class="sidebar-text">Risk Register</span>
                    </a>
                    <a href="{{ route('risk-assessment.jsas.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('risk-assessment/jsas*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="JSA">
                        <i class="fas fa-tasks w-5 text-center"></i>
                        <span class="sidebar-text">Job Safety Analysis</span>
                    </a>
                    <a href="{{ route('risk-assessment.control-measures.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('risk-assessment/control-measures*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Controls">
                        <i class="fas fa-shield-alt w-5 text-center"></i>
                        <span class="sidebar-text">Control Measures</span>
                    </a>
                    <a href="{{ route('risk-assessment.risk-reviews.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('risk-assessment/risk-reviews*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Reviews">
                        <i class="fas fa-sync-alt w-5 text-center"></i>
                        <span class="sidebar-text">Risk Reviews</span>
                    </a>
                </div>
            </div>
            
            <!-- Safety Communications - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('communications')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-bullhorn text-purple-600"></i>
                        <span class="sidebar-text">Safety Communications</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="communications-chevron"></i>
                </button>
                <div id="communications-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('safety-communications.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('safety-communications') && !request()->is('safety-communications/*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="All Communications">
                        <i class="fas fa-list w-5 text-center"></i>
                        <span class="sidebar-text">All Communications</span>
                    </a>
                    <a href="{{ route('safety-communications.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('safety-communications/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('safety-communications.create') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('safety-communications/create') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="New Communication">
                        <i class="fas fa-plus-circle w-5 text-center"></i>
                        <span class="sidebar-text">New Communication</span>
                    </a>
                </div>
            </div>
            
            <!-- PPE Management - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('ppe')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-hard-hat text-teal-600"></i>
                        <span class="sidebar-text">PPE Management</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="ppe-chevron"></i>
                </button>
                <div id="ppe-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('ppe.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('ppe/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('ppe.items.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('ppe/items*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Inventory">
                        <i class="fas fa-boxes w-5 text-center"></i>
                        <span class="sidebar-text">Inventory</span>
                    </a>
                    <a href="{{ route('ppe.issuances.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('ppe/issuances*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Issuances">
                        <i class="fas fa-hand-holding w-5 text-center"></i>
                        <span class="sidebar-text">Issuances & Returns</span>
                    </a>
                    <a href="{{ route('ppe.inspections.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('ppe/inspections*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Inspections">
                        <i class="fas fa-search w-5 text-center"></i>
                        <span class="sidebar-text">Inspections</span>
                    </a>
                    <a href="{{ route('ppe.suppliers.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('ppe/suppliers*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Suppliers">
                        <i class="fas fa-truck w-5 text-center"></i>
                        <span class="sidebar-text">Suppliers</span>
                    </a>
                    <a href="{{ route('ppe.reports.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('ppe/reports*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Compliance Reports">
                        <i class="fas fa-file-alt w-5 text-center"></i>
                        <span class="sidebar-text">Compliance Reports</span>
                    </a>
                </div>
            </div>
            
            <!-- Permit to Work (PTW) - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('work-permits')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-file-contract"></i>
                        <span class="sidebar-text">Permit to Work</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="work-permits-chevron"></i>
                </button>
                <div id="work-permits-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('work-permits.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('work-permits/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('work-permits.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('work-permits') && !request()->is('work-permits/*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="All Permits">
                        <i class="fas fa-list w-5 text-center"></i>
                        <span class="sidebar-text">All Permits</span>
                    </a>
                    <a href="{{ route('work-permits.create') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('work-permits/create') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="New Permit">
                        <i class="fas fa-plus-circle w-5 text-center"></i>
                        <span class="sidebar-text">New Permit</span>
                    </a>
                    <a href="{{ route('work-permits.types.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('work-permits/types*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Permit Types">
                        <i class="fas fa-tags w-5 text-center"></i>
                        <span class="sidebar-text">Permit Types</span>
                    </a>
                    <a href="{{ route('work-permits.gca-logs.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('work-permits/gca-logs*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="GCLA Logs">
                        <i class="fas fa-clipboard-check w-5 text-center"></i>
                        <span class="sidebar-text">GCLA Logs</span>
                    </a>
                </div>
            </div>
            
            <!-- Inspection & Audit - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('inspections')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-clipboard-check text-green-600"></i>
                        <span class="sidebar-text">Inspection & Audit</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="inspections-chevron"></i>
                </button>
                <div id="inspections-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('inspections.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('inspections/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('inspections.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('inspections') && !request()->is('inspections/*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="All Inspections">
                        <i class="fas fa-list w-5 text-center"></i>
                        <span class="sidebar-text">Inspections</span>
                    </a>
                    <a href="{{ route('inspections.schedules.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('inspections/schedules*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Schedules">
                        <i class="fas fa-calendar-alt w-5 text-center"></i>
                        <span class="sidebar-text">Schedules</span>
                    </a>
                    <a href="{{ route('inspections.checklists.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('inspections/checklists*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Checklists">
                        <i class="fas fa-tasks w-5 text-center"></i>
                        <span class="sidebar-text">Checklists</span>
                    </a>
                    <a href="{{ route('inspections.ncrs.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('inspections/ncrs*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="NCRs">
                        <i class="fas fa-exclamation-triangle w-5 text-center"></i>
                        <span class="sidebar-text">NCRs</span>
                    </a>
                    <a href="{{ route('inspections.audits.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('inspections/audits*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Audits">
                        <i class="fas fa-search w-5 text-center"></i>
                        <span class="sidebar-text">Audits</span>
                    </a>
                </div>
            </div>
            
            <!-- Emergency Preparedness - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('emergency')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                        <span class="sidebar-text">Emergency Preparedness</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="emergency-chevron"></i>
                </button>
                <div id="emergency-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('emergency.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('emergency/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('emergency.fire-drills.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('emergency/fire-drills*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Fire Drills">
                        <i class="fas fa-fire w-5 text-center"></i>
                        <span class="sidebar-text">Fire Drills</span>
                    </a>
                    <a href="{{ route('emergency.contacts.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('emergency/contacts*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Contacts">
                        <i class="fas fa-phone w-5 text-center"></i>
                        <span class="sidebar-text">Emergency Contacts</span>
                    </a>
                    <a href="{{ route('emergency.equipment.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('emergency/equipment*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Equipment">
                        <i class="fas fa-tools w-5 text-center"></i>
                        <span class="sidebar-text">Equipment</span>
                    </a>
                    <a href="{{ route('emergency.evacuation-plans.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('emergency/evacuation-plans*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Evacuation Plans">
                        <i class="fas fa-map-marked-alt w-5 text-center"></i>
                        <span class="sidebar-text">Evacuation Plans</span>
                    </a>
                    <a href="{{ route('emergency.response-teams.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('emergency/response-teams*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Response Teams">
                        <i class="fas fa-users w-5 text-center"></i>
                        <span class="sidebar-text">Response Teams</span>
                    </a>
                </div>
            </div>
            
            <!-- Training & Competency - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('training')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-graduation-cap text-indigo-600"></i>
                        <span class="sidebar-text">Training & Competency</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="training-chevron"></i>
                </button>
                <div id="training-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('training.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('training/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('training.training-needs.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('training/training-needs*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Training Needs">
                        <i class="fas fa-clipboard-list w-5 text-center"></i>
                        <span class="sidebar-text">Training Needs</span>
                    </a>
                    <a href="{{ route('training.training-plans.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('training/training-plans*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Training Plans">
                        <i class="fas fa-calendar-check w-5 text-center"></i>
                        <span class="sidebar-text">Training Plans</span>
                    </a>
                    <a href="{{ route('training.training-sessions.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('training/training-sessions') && !request()->is('training/training-sessions/calendar') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Sessions">
                        <i class="fas fa-chalkboard-teacher w-5 text-center"></i>
                        <span class="sidebar-text">Training Sessions</span>
                    </a>
                    <a href="{{ route('training.training-sessions.calendar') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('training/training-sessions/calendar') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Calendar">
                        <i class="fas fa-calendar-alt w-5 text-center"></i>
                        <span class="sidebar-text">Calendar</span>
                    </a>
                    <a href="{{ route('training.reporting.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('training/reporting*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Reporting">
                        <i class="fas fa-chart-bar w-5 text-center"></i>
                        <span class="sidebar-text">Reporting</span>
                    </a>
                </div>
            </div>
            
            <!-- Environmental Management - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('environmental')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-leaf text-green-600"></i>
                        <span class="sidebar-text">Environmental Management</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="environmental-chevron"></i>
                </button>
                <div id="environmental-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('environmental.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('environmental/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('environmental.waste-management.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('environmental/waste-management*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Waste Management">
                        <i class="fas fa-trash-alt w-5 text-center"></i>
                        <span class="sidebar-text">Waste Management</span>
                    </a>
                    <a href="{{ route('environmental.waste-tracking.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('environmental/waste-tracking*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Waste Tracking">
                        <i class="fas fa-route w-5 text-center"></i>
                        <span class="sidebar-text">Waste Tracking</span>
                    </a>
                    <a href="{{ route('environmental.emissions.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('environmental/emissions*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Emission Monitoring">
                        <i class="fas fa-wind w-5 text-center"></i>
                        <span class="sidebar-text">Emission Monitoring</span>
                    </a>
                    <a href="{{ route('environmental.spills.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('environmental/spills*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Spill Incidents">
                        <i class="fas fa-tint w-5 text-center"></i>
                        <span class="sidebar-text">Spill Incidents</span>
                    </a>
                    <a href="{{ route('environmental.resource-usage.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('environmental/resource-usage*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Resource Usage">
                        <i class="fas fa-tachometer-alt w-5 text-center"></i>
                        <span class="sidebar-text">Resource Usage</span>
                    </a>
                    <a href="{{ route('environmental.iso14001.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('environmental/iso14001*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="ISO 14001">
                        <i class="fas fa-certificate w-5 text-center"></i>
                        <span class="sidebar-text">ISO 14001 Compliance</span>
                    </a>
                </div>
            </div>
            
            <!-- Health & Wellness - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('health')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-heartbeat text-red-600"></i>
                        <span class="sidebar-text">Health & Wellness</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="health-chevron"></i>
                </button>
                <div id="health-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('health.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('health/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('health.surveillance.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('health/surveillance*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Health Surveillance">
                        <i class="fas fa-user-md w-5 text-center"></i>
                        <span class="sidebar-text">Health Surveillance</span>
                    </a>
                    <a href="{{ route('health.first-aid.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('health/first-aid*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="First Aid">
                        <i class="fas fa-first-aid w-5 text-center"></i>
                        <span class="sidebar-text">First Aid Logbook</span>
                    </a>
                    <a href="{{ route('health.ergonomic.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('health/ergonomic*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Ergonomic">
                        <i class="fas fa-chair w-5 text-center"></i>
                        <span class="sidebar-text">Ergonomic Assessments</span>
                    </a>
                    <a href="{{ route('health.hygiene.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('health/hygiene*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Hygiene">
                        <i class="fas fa-spray-can w-5 text-center"></i>
                        <span class="sidebar-text">Hygiene Inspections</span>
                    </a>
                    <a href="{{ route('health.campaigns.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('health/campaigns*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Campaigns">
                        <i class="fas fa-bullhorn w-5 text-center"></i>
                        <span class="sidebar-text">Health Campaigns</span>
                    </a>
                    <a href="{{ route('health.sick-leave.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('health/sick-leave*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Sick Leave">
                        <i class="fas fa-notes-medical w-5 text-center"></i>
                        <span class="sidebar-text">Sick Leave Records</span>
                    </a>
                </div>
            </div>
            
            <!-- Procurement & Resource Management - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('procurement')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-shopping-cart text-purple-600"></i>
                        <span class="sidebar-text">Procurement & Resources</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="procurement-chevron"></i>
                </button>
                <div id="procurement-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('procurement.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('procurement/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('procurement.requests.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('procurement/requests*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Requests">
                        <i class="fas fa-shopping-cart w-5 text-center"></i>
                        <span class="sidebar-text">Procurement Requests</span>
                    </a>
                    <a href="{{ route('procurement.suppliers.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('procurement/suppliers*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Suppliers">
                        <i class="fas fa-truck w-5 text-center"></i>
                        <span class="sidebar-text">Suppliers</span>
                    </a>
                    <a href="{{ route('procurement.equipment-certifications.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('procurement/equipment-certifications*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Certifications">
                        <i class="fas fa-certificate w-5 text-center"></i>
                        <span class="sidebar-text">Equipment Certifications</span>
                    </a>
                    <a href="{{ route('procurement.stock-reports.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('procurement/stock-reports*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Stock Reports">
                        <i class="fas fa-chart-line w-5 text-center"></i>
                        <span class="sidebar-text">Stock Reports</span>
                    </a>
                    <a href="{{ route('procurement.gap-analysis.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('procurement/gap-analysis*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Gap Analysis">
                        <i class="fas fa-exclamation-circle w-5 text-center"></i>
                        <span class="sidebar-text">Gap Analysis</span>
                    </a>
                </div>
            </div>
            
            <!-- Document & Record Management - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('documents')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-folder-open text-blue-600"></i>
                        <span class="sidebar-text">Document Management</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="documents-chevron"></i>
                </button>
                <div id="documents-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('documents.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('documents/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('documents.hse-documents.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('documents/hse-documents*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Documents">
                        <i class="fas fa-file-alt w-5 text-center"></i>
                        <span class="sidebar-text">Documents</span>
                    </a>
                    <a href="{{ route('documents.versions.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('documents/versions*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Versions">
                        <i class="fas fa-code-branch w-5 text-center"></i>
                        <span class="sidebar-text">Versions</span>
                    </a>
                    <a href="{{ route('documents.templates.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('documents/templates*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Templates">
                        <i class="fas fa-file-invoice w-5 text-center"></i>
                        <span class="sidebar-text">Templates</span>
                    </a>
                </div>
            </div>
            
            <!-- Compliance & Legal - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('compliance')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-gavel text-yellow-600"></i>
                        <span class="sidebar-text">Compliance & Legal</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="compliance-chevron"></i>
                </button>
                <div id="compliance-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('compliance.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('compliance/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('compliance.requirements.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('compliance/requirements*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Requirements">
                        <i class="fas fa-list-check w-5 text-center"></i>
                        <span class="sidebar-text">Requirements</span>
                    </a>
                    <a href="{{ route('compliance.permits-licenses.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('compliance/permits-licenses*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Permits & Licenses">
                        <i class="fas fa-id-card w-5 text-center"></i>
                        <span class="sidebar-text">Permits & Licenses</span>
                    </a>
                    <a href="{{ route('compliance.audits.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('compliance/audits*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Audits">
                        <i class="fas fa-clipboard-list w-5 text-center"></i>
                        <span class="sidebar-text">Compliance Audits</span>
                    </a>
                </div>
            </div>
            
            <!-- Housekeeping & Workplace Organization - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('housekeeping')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-broom text-green-600"></i>
                        <span class="sidebar-text">Housekeeping</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="housekeeping-chevron"></i>
                </button>
                <div id="housekeeping-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('housekeeping.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('housekeeping/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('housekeeping.inspections.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('housekeeping/inspections*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Inspections">
                        <i class="fas fa-clipboard-check w-5 text-center"></i>
                        <span class="sidebar-text">Inspections</span>
                    </a>
                    <a href="{{ route('housekeeping.5s-audits.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('housekeeping/5s-audits*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="5S Audits">
                        <i class="fas fa-star w-5 text-center"></i>
                        <span class="sidebar-text">5S Audits</span>
                    </a>
                </div>
            </div>
            
            <!-- Waste & Sustainability - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('waste-sustainability')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-recycle text-green-600"></i>
                        <span class="sidebar-text">Waste & Sustainability</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="waste-sustainability-chevron"></i>
                </button>
                <div id="waste-sustainability-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('waste-sustainability.dashboard') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('waste-sustainability/dashboard') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Dashboard">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('waste-sustainability.records.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('waste-sustainability/records*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Records">
                        <i class="fas fa-list w-5 text-center"></i>
                        <span class="sidebar-text">Waste Records</span>
                    </a>
                    <a href="{{ route('waste-sustainability.carbon-footprint.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('waste-sustainability/carbon-footprint*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Carbon Footprint">
                        <i class="fas fa-cloud w-5 text-center"></i>
                        <span class="sidebar-text">Carbon Footprint</span>
                    </a>
                </div>
            </div>
            
            <!-- Notifications & Alerts - Collapsible -->
            <div class="space-y-1">
                <button onclick="toggleSection('notifications')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors sidebar-text">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-bell text-orange-600"></i>
                        <span class="sidebar-text">Notifications & Alerts</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" id="notifications-chevron"></i>
                </button>
                <div id="notifications-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('notifications.rules.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('notifications/rules*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Notification Rules">
                        <i class="fas fa-cog w-5 text-center"></i>
                        <span class="sidebar-text">Notification Rules</span>
                    </a>
                    <a href="{{ route('notifications.escalation-matrices.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('notifications/escalation-matrices*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Escalation">
                        <i class="fas fa-arrow-up w-5 text-center"></i>
                        <span class="sidebar-text">Escalation Matrices</span>
                    </a>
                </div>
            </div>
            
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
                <div id="admin-section" class="space-y-1 pl-4 border-l-2 border-gray-300">
                    <a href="{{ route('admin.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('admin') && !request()->is('admin/*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Admin Dashboard">
                        <i class="fas fa-tachometer-alt w-5 text-center"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.employees.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('admin/employees*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Employees">
                        <i class="fas fa-user-tie w-5 text-center"></i>
                        <span class="sidebar-text">Employees</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('admin/users*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Users">
                        <i class="fas fa-users w-5 text-center"></i>
                        <span class="sidebar-text">Users</span>
                    </a>
                    <a href="{{ route('admin.companies.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('admin/companies*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Companies">
                        <i class="fas fa-building w-5 text-center"></i>
                        <span class="sidebar-text">Companies</span>
                    </a>
                    <a href="{{ route('admin.departments.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('admin/departments*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Departments">
                        <i class="fas fa-sitemap w-5 text-center"></i>
                        <span class="sidebar-text">Departments</span>
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('admin/roles*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Roles">
                        <i class="fas fa-user-shield w-5 text-center"></i>
                        <span class="sidebar-text">Roles & Permissions</span>
                    </a>
                    <a href="{{ route('admin.activity-logs.index') }}" class="sidebar-nav-item flex items-center space-x-3 px-3 py-2 transition-all {{ request()->is('admin/activity-logs*') ? 'bg-[#0066CC] text-white' : 'hover:bg-[#F5F5F5] text-black' }}" data-tooltip="Activity Logs">
                        <i class="fas fa-history w-5 text-center"></i>
                        <span class="sidebar-text">Activity Logs</span>
                    </a>
                </div>
            </div>
            @endif
        </nav>
        
        <!-- User Info -->
        <div class="p-4 border-t border-gray-300 bg-[#F5F5F5]">
            <div class="flex items-center space-x-3 mb-3 sidebar-user-info">
                <div class="w-10 h-10 bg-[#0066CC] flex items-center justify-center">
                    <span class="text-white font-semibold text-sm">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                </div>
                <div class="flex-1 sidebar-user-info">
                    <div class="text-sm font-semibold text-black">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-gray-500">{{ Auth::user()->role->display_name ?? Auth::user()->role->name ?? 'User' }}</div>
                    @if(Auth::user()->department)
                    <div class="text-xs text-gray-500 mt-0.5">{{ Auth::user()->department->name }}</div>
                    @endif
                </div>
            </div>
            <div class="flex space-x-2 sidebar-text">
                <a href="#" onclick="alert('Profile settings coming soon!'); return false;" class="flex-1 flex items-center justify-center space-x-1 px-3 py-2 bg-white border border-gray-300 text-black text-xs font-medium hover:bg-[#F5F5F5] transition-colors" data-tooltip="Profile">
                    <i class="fas fa-user-cog"></i>
                    <span class="sidebar-text">Profile</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-1 px-3 py-2 bg-[#CC0000] text-white text-xs font-medium hover:bg-[#AA0000] transition-colors" data-tooltip="Logout">
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
.sidebar-collapsed #risk-assessment-section,
.sidebar-collapsed #communications-section,
.sidebar-collapsed #ppe-section,
.sidebar-collapsed #work-permits-section,
.sidebar-collapsed #inspections-section,
.sidebar-collapsed #emergency-section,
.sidebar-collapsed #training-section,
.sidebar-collapsed #environmental-section,
.sidebar-collapsed #health-section,
.sidebar-collapsed #procurement-section,
.sidebar-collapsed #documents-section,
.sidebar-collapsed #compliance-section,
.sidebar-collapsed #housekeeping-section,
.sidebar-collapsed #waste-sustainability-section,
.sidebar-collapsed #notifications-section,
.sidebar-collapsed #admin-section {
    padding-left: 0;
    border-left: none
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
    ['toolbox', 'incidents', 'risk-assessment', 'communications', 'ppe', 'work-permits', 'inspections', 'emergency', 'training', 'environmental', 'health', 'procurement', 'documents', 'compliance', 'housekeeping', 'waste-sustainability', 'notifications', 'admin'].forEach(sectionName => {
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
