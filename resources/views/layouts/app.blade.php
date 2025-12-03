<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HSE Management System')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Design System Component -->
    <x-design-system />
    
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
<body class="font-inter bg-gray-50 min-h-screen">
    @if(auth()->check())
        <div class="flex min-h-screen">
            <!-- Include Sidebar -->
            @include('layouts.sidebar')
            
            <!-- Main Content -->
            <main class="flex-1 lg:ml-64 transition-all duration-300" id="main-content">
                <!-- Mobile Header -->
                <header class="lg:hidden bg-white border-b border-gray-200 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-primary-black rounded flex items-center justify-center">
                                <i class="fas fa-shield-alt text-primary-white text-xs"></i>
                            </div>
                            <span class="text-sm font-medium text-primary-black">HSE System</span>
                        </div>
                    </div>
                </header>
                
                <!-- Sub Navigation -->
                <x-sub-nav />
                
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
