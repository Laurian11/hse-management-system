<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HSE Management System')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Design System Component -->
    <x-design-system />
    
    <!-- Flat Design CSS -->
    <link rel="stylesheet" href="{{ asset('css/flat-design.css') }}">
    
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
                    zIndex: {
                        'fixed': 'var(--z-fixed)',
                        'modal-backdrop': 'var(--z-modal-backdrop)',
                        'modal': 'var(--z-modal)',
                    }
                }
            }
        }
    </script>
    
    @stack('styles')
</head>
<body class="font-inter bg-gray-50 min-h-screen">
    @yield('content')
    
    <script>
        // Common utility functions
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
                } else {
                    alert('Success: ' + message);
                }
            },
            
            // Show error message
            showError: function(message) {
                alert('Error: ' + message);
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
            }
        };
    </script>
    
    @stack('scripts')
</body>
</html>

