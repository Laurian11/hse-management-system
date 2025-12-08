<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HSE Management System</title>
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
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'primary-black': 'var(--color-primary-black)',
                        'primary-white': 'var(--color-primary-white)',
                        'background-gray': 'var(--color-gray-50)',
                        'hover-gray': 'var(--color-gray-100)',
                        'border-gray': 'var(--color-gray-200)',
                        'medium-gray': 'var(--color-gray-500)',
                        'dark-gray': 'var(--color-gray-700)',
                    }
                }
            }
        }
    </script>
</head>
<body class="font-inter bg-background-gray min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-primary-white border border-border-gray p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="mx-auto h-12 w-12 bg-primary-black rounded flex items-center justify-center mb-4">
                    <i class="fas fa-shield-alt text-primary-white text-xl"></i>
                </div>
                <h2 class="text-2xl font-medium text-primary-black">HSE Management System</h2>
                <p class="mt-2 text-sm text-medium-gray">Sign in to your account</p>
            </div>

            <!-- Login Form -->
            <form id="loginForm" class="space-y-6" method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-primary-black mb-2">Email Address</label>
                    <div class="relative">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="w-full pl-10 pr-4 py-3 border border-border-gray focus:border-primary-black focus:outline-none @error('email') border-red-500 @enderror"
                               placeholder="Enter your email"
                               value="{{ old('email') }}">
                        <i class="fas fa-user absolute left-3 top-3.5 w-4 h-4 text-medium-gray"></i>
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-primary-black mb-2">Password</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="w-full pl-10 pr-12 py-3 border border-border-gray focus:border-primary-black focus:outline-none @error('password') border-red-500 @enderror"
                               placeholder="Enter your password">
                        <i class="fas fa-lock absolute left-3 top-3.5 w-4 h-4 text-medium-gray"></i>
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-3.5 text-medium-gray hover:text-primary-black">
                            <i id="eyeIcon" class="fas fa-eye w-4 h-4"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                               class="mr-2 rounded border-gray-300" {{ old('remember') ? 'checked' : '' }}>
                        <span class="text-sm text-medium-gray">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-dark-gray hover:underline">Forgot Password?</a>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            class="w-full bg-primary-black text-primary-white py-3 font-medium hover:bg-dark-gray transition-colors flex items-center justify-center space-x-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Sign in</span>
                    </button>
                </div>
            </form>

            <!-- Error Message -->
            @if (session('error'))
                <div class="mt-4 bg-red-50 border border-red-200 p-4">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Footer -->
            <div class="mt-6 text-center">
                <a href="{{ route('landing') }}" class="text-sm text-medium-gray hover:text-dark-gray">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // Handle form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
            
            // Form will submit normally, but if there's an error, re-enable button
            setTimeout(() => {
                if (document.querySelector('.text-red-500')) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            }, 2000);
        });
    </script>
</body>
</html>
