<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HSE Management System')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Design System Component -->
    <x-design-system />
    
    <!-- Flat Design CSS -->
    <link rel="stylesheet" href="{{ asset('css/flat-design.css') }}">
    
    <!-- Print CSS -->
    <link rel="stylesheet" href="{{ asset('css/print.css') }}" media="print">
    
    <!-- External Libraries -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script>
        // Configure Tailwind to use our design system colors
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        // Use CSS variables from design system
                        'primary-black': 'var(--color-primary-black)',
                        'primary-white': 'var(--color-primary-white)',
                        'background-gray': 'var(--color-gray-50)',
                        'hover-gray': 'var(--color-gray-100)',
                        'border-gray': 'var(--color-gray-200)',
                        'light-border': 'var(--color-gray-300)',
                        'light-gray': 'var(--color-gray-400)',
                        'medium-gray': 'var(--color-gray-500)',
                        'accent-gray': 'var(--color-gray-600)',
                        'dark-gray': 'var(--color-gray-700)',
                    },
                    spacing: {
                        'xs': 'var(--spacing-xs)',
                        'sm': 'var(--spacing-sm)',
                        'md': 'var(--spacing-md)',
                        'lg': 'var(--spacing-lg)',
                        'xl': 'var(--spacing-xl)',
                        '2xl': 'var(--spacing-2xl)',
                    },
                    borderRadius: {
                        'none': 'var(--border-radius-none)',
                        'sm': 'var(--border-radius-sm)',
                        'md': 'var(--border-radius-md)',
                        'lg': 'var(--border-radius-lg)',
                        'xl': 'var(--border-radius-xl)',
                        'full': 'var(--border-radius-full)',
                    },
                    boxShadow: {
                        'none': 'var(--shadow-none)',
                        'sm': 'var(--shadow-sm)',
                        'md': 'var(--shadow-md)',
                        'lg': 'var(--shadow-lg)',
                    },
                    transitionDuration: {
                        'fast': '150ms',
                        'normal': '250ms',
                        'slow': '350ms',
                    },
                    zIndex: {
                        'dropdown': 'var(--z-dropdown)',
                        'sticky': 'var(--z-sticky)',
                        'fixed': 'var(--z-fixed)',
                        'modal-backdrop': 'var(--z-modal-backdrop)',
                        'modal': 'var(--z-modal)',
                        'popover': 'var(--z-popover)',
                        'tooltip': 'var(--z-tooltip)',
                    }
                }
            }
        }
    </script>
    
    @stack('styles')
</head>
<body class="font-inter bg-gray-50 dark:bg-gray-900 min-h-screen" data-theme="light">
    @if(auth()->check())
        <div class="flex min-h-screen">
            <!-- Include Sidebar -->
            @include('layouts.sidebar')
            
            <!-- Main Content -->
            <main class="flex-1 lg:ml-64 transition-all duration-300" id="main-content">
                <!-- Mobile Header -->
                <header class="lg:hidden bg-white dark:bg-gray-800 border-b border-gray-300 dark:border-gray-700 px-4 py-3">
                    <div class="flex items-center justify-between mb-2">
                        <button onclick="toggleSidebar()" class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-[#0066CC] flex items-center justify-center">
                                <i class="fas fa-shield-alt text-primary-white text-xs"></i>
                            </div>
                            <span class="text-sm font-medium text-primary-black dark:text-white">HSE System</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="toggleNotificationCenter()" class="relative text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white p-2" title="Notifications">
                                <i class="fas fa-bell"></i>
                                <span id="notificationBadgeMobile" class="hidden absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>
                            <button onclick="toggleDarkMode()" class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white" title="Toggle Dark Mode">
                                <i class="fas fa-moon dark:hidden"></i>
                                <i class="fas fa-sun hidden dark:inline"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Mobile Global Search -->
                    <div class="relative">
                        <input type="text" 
                               id="globalSearchMobile" 
                               placeholder="Search..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                               onkeyup="handleGlobalSearch(event)"
                               onfocus="showGlobalSearchResultsMobile()">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <!-- Mobile Search Results -->
                        <div id="globalSearchResultsMobile" class="hidden fixed inset-0 z-50 bg-white dark:bg-gray-800 mt-16 border-t border-gray-300 dark:border-gray-600 overflow-y-auto">
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Search Results</h3>
                                <button onclick="closeMobileSearch()" class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="globalSearchResultsContentMobile" class="p-2">
                                <!-- Results will be loaded here -->
                            </div>
                        </div>
                    </div>
                </header>
                
                <!-- Desktop Header with Global Search and Dark Mode Toggle -->
                <header class="hidden lg:block bg-white dark:bg-gray-800 border-b border-gray-300 dark:border-gray-700 px-6 py-3">
                    <div class="flex items-center justify-between">
                        <!-- Global Search -->
                        <div class="flex-1 max-w-md relative">
                            <div class="relative">
                                <input type="text" 
                                       id="globalSearch" 
                                       placeholder="Search across all modules..." 
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                       onkeyup="handleGlobalSearch(event)"
                                       onfocus="showGlobalSearchResults()">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <!-- Search Results Dropdown -->
                            <div id="globalSearchResults" class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-96 overflow-y-auto">
                                <div id="globalSearchResultsContent" class="p-2">
                                    <!-- Results will be loaded here -->
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <!-- Notification Bell -->
                            <button onclick="toggleNotificationCenter()" class="relative text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white p-2" title="Notifications">
                                <i class="fas fa-bell"></i>
                                <span id="notificationBadge" class="hidden absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>
                            
                            <!-- Dark Mode Toggle -->
                            <button onclick="toggleDarkMode()" class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white p-2" title="Toggle Dark Mode">
                                <i class="fas fa-moon dark:hidden"></i>
                                <i class="fas fa-sun hidden dark:inline"></i>
                            </button>
                        </div>
                    </div>
                </header>
                
                <!-- Notification Center -->
                <div id="notificationCenter" class="hidden fixed right-4 top-16 w-80 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifications</h3>
                            <button onclick="toggleNotificationCenter()" class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div id="notificationList" class="p-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400 p-4 text-center">No notifications</p>
                    </div>
                </div>
                
                <!-- Sub Navigation -->
                <x-sub-nav />
                
                <!-- Breadcrumbs -->
                <x-breadcrumbs :breadcrumbs="$breadcrumbs ?? null" />
                
                <!-- Recent Items -->
                <x-recent-items />
                
                <!-- Page Content -->
                <div class="p-6">
                    @yield('content')
                </div>
            </main>
        </div>
    @else
        <!-- Guest layout (login/register pages) -->
        @yield('content')
    @endif

    @stack('scripts')
    
    <!-- Common JavaScript -->
    <script>
        // Restore sidebar state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            
            if (isCollapsed) {
                sidebar.classList.add('sidebar-collapsed');
                mainContent.classList.remove('lg:ml-64');
                mainContent.classList.add('lg:ml-16');
            }
        });

        // Update main content margin when sidebar toggles
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            sidebar.classList.toggle('sidebar-collapsed');
            
            const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
            
            if (isCollapsed) {
                mainContent.classList.remove('lg:ml-64');
                mainContent.classList.add('lg:ml-16');
            } else {
                mainContent.classList.remove('lg:ml-16');
                mainContent.classList.add('lg:ml-64');
            }
        }
        
        // Dark Mode Toggle
        function toggleDarkMode() {
            const body = document.body;
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            body.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update all elements that need theme class
            document.documentElement.setAttribute('data-theme', newTheme);
        }
        
        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.body.setAttribute('data-theme', savedTheme);
            document.documentElement.setAttribute('data-theme', savedTheme);
        });
        
        // Keyboard Shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+N or Cmd+N - New record (context-aware)
            if ((e.ctrlKey || e.metaKey) && e.key === 'n' && !e.shiftKey) {
                e.preventDefault();
                const newButton = document.querySelector('a[href*="/create"], button[onclick*="create"]');
                if (newButton) {
                    if (newButton.tagName === 'A') {
                        window.location.href = newButton.href;
                    } else {
                        newButton.click();
                    }
                }
            }
            
            // Ctrl+S or Cmd+S - Save form
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                const form = document.querySelector('form');
                const submitButton = form?.querySelector('button[type="submit"], input[type="submit"]');
                if (submitButton && !submitButton.disabled) {
                    e.preventDefault();
                    form?.submit();
                }
            }
            
            // Ctrl+F or Cmd+F - Focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f' && !e.shiftKey) {
                const searchInput = document.querySelector('input[type="search"], input[name*="search"], input[placeholder*="Search"]');
                if (searchInput) {
                    e.preventDefault();
                    searchInput.focus();
                    searchInput.select();
                }
            }
            
            // Ctrl+/ or Cmd+/ - Show keyboard shortcuts help
            if ((e.ctrlKey || e.metaKey) && e.key === '/') {
                e.preventDefault();
                alert('Keyboard Shortcuts:\n\nCtrl+N - New Record\nCtrl+S - Save Form\nCtrl+F - Focus Search\nCtrl+/ - Show Help');
            }
        });
        
        // Global Search Functionality
        let globalSearchTimeout;
        
        function handleGlobalSearch(event) {
            clearTimeout(globalSearchTimeout);
            
            const query = event.target.value.trim();
            const isMobile = window.innerWidth < 1024;
            const resultsId = isMobile ? 'globalSearchResultsMobile' : 'globalSearchResults';
            const contentId = isMobile ? 'globalSearchResultsContentMobile' : 'globalSearchResultsContent';
            
            if (query.length < 2) {
                document.getElementById(resultsId).classList.add('hidden');
                return;
            }
            
            globalSearchTimeout = setTimeout(() => {
                performGlobalSearch(query, resultsId, contentId);
            }, 300);
        }
        
        function showGlobalSearchResultsMobile() {
            const input = document.getElementById('globalSearchMobile');
            if (input && input.value.trim().length >= 2) {
                document.getElementById('globalSearchResultsMobile').classList.remove('hidden');
            }
        }
        
        function closeMobileSearch() {
            document.getElementById('globalSearchResultsMobile').classList.add('hidden');
            document.getElementById('globalSearchMobile').value = '';
        }
        
        function performGlobalSearch(query, resultsId, contentId) {
            const resultsDiv = document.getElementById(contentId);
            resultsDiv.innerHTML = '<div class="p-4 text-center text-gray-500 dark:text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Searching...</div>';
            document.getElementById(resultsId).classList.remove('hidden');
            
            // Try API first, fallback to quick links
            fetch(`/api/search?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('API not available');
            })
            .then(data => {
                displayGlobalSearchResults(data, query, contentId);
            })
            .catch(error => {
                // Fallback: show quick links
                displayQuickSearchResults(query, contentId);
            });
        }
        
        function displayQuickSearchResults(query, contentId) {
            const resultsDiv = document.getElementById(contentId);
            const lowerQuery = query.toLowerCase();
            
            const quickLinks = [
                { label: 'Incidents', route: 'incidents.index', icon: 'fa-exclamation-triangle', pattern: 'incident' },
                { label: 'PPE Items', route: 'ppe.items.index', icon: 'fa-hard-hat', pattern: 'ppe' },
                { label: 'Training Plans', route: 'training.training-plans.index', icon: 'fa-graduation-cap', pattern: 'training' },
                { label: 'Risk Assessments', route: 'risk-assessment.assessments.index', icon: 'fa-shield-alt', pattern: 'risk' },
                { label: 'Toolbox Talks', route: 'toolbox-talks.index', icon: 'fa-comments', pattern: 'toolbox' },
            ];
            
            const filtered = quickLinks.filter(link => 
                link.label.toLowerCase().includes(lowerQuery) || 
                link.pattern.includes(lowerQuery)
            );
            
            if (filtered.length === 0) {
                resultsDiv.innerHTML = '<div class="p-4 text-center text-gray-500">No results found</div>';
                return;
            }
            
            resultsDiv.innerHTML = filtered.map(link => `
                <a href="{{ route('${link.route}') }}" class="flex items-center space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded transition-colors">
                    <i class="fas ${link.icon} text-blue-600 dark:text-blue-400"></i>
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">${link.label}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Go to ${link.label}</div>
                    </div>
                </a>
            `).join('');
        }
        
        function displayGlobalSearchResults(data, query, contentId) {
            const resultsDiv = document.getElementById(contentId);
            
            if (!data || !data.results || data.results.length === 0) {
                displayQuickSearchResults(query);
                return;
            }
            
            let html = '';
            data.results.forEach(result => {
                html += `
                    <a href="${result.url}" class="flex items-center space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded transition-colors">
                        <i class="fas ${result.icon} text-blue-600 dark:text-blue-400"></i>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 dark:text-white">${result.title}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">${result.module} • ${result.type}</div>
                        </div>
                    </a>
                `;
            });
            
            resultsDiv.innerHTML = html;
        }
        
        function showGlobalSearchResults() {
            const input = document.getElementById('globalSearch');
            if (input.value.trim().length >= 2) {
                document.getElementById('globalSearchResults').classList.remove('hidden');
            }
        }
        
        // Close search results when clicking outside
        document.addEventListener('click', function(event) {
            const searchContainer = document.getElementById('globalSearch')?.closest('.relative');
            const resultsDiv = document.getElementById('globalSearchResults');
            
            if (searchContainer && !searchContainer.contains(event.target)) {
                resultsDiv.classList.add('hidden');
            }
        });
        
        // Notification Center
        function toggleNotificationCenter() {
            const center = document.getElementById('notificationCenter');
            center.classList.toggle('hidden');
            
            if (!center.classList.contains('hidden')) {
                loadNotifications();
            }
        }
        
        function loadNotifications() {
            // In production, this would fetch from API
            const list = document.getElementById('notificationList');
            list.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 p-4 text-center">No notifications</p>';
        }
        
        // Close notification center when clicking outside
        document.addEventListener('click', function(event) {
            const center = document.getElementById('notificationCenter');
            const bell = event.target.closest('button[onclick*="toggleNotificationCenter"]');
            
            if (center && !center.contains(event.target) && !bell) {
                center.classList.add('hidden');
            }
        });
        
        // Common utility functions using design system
        window.HSEUtils = {
            // Show success message
            showSuccess: function(message) {
                const successDiv = document.getElementById('successMessage');
                if (successDiv) {
                    const textElement = successDiv.querySelector('#successText');
                    if (textElement) {
                        textElement.textContent = message;
                    }
                    successDiv.classList.remove('hidden');
                    setTimeout(() => {
                        successDiv.classList.add('hidden');
                    }, 5000);
                }
            },
            
            // Show error message
            showError: function(message) {
                alert(message); // Simple error display, can be enhanced
            },
            
            // Format date
            formatDate: function(date) {
                return new Date(date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            },
            
            // Debounce function
            debounce: function(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
        };
    </script>
</body>
</html>
