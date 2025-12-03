@extends('layouts.app')

@section('title', 'HSE Management System')

@section('content')
    <!-- Navigation Bar -->
    <header class="fixed top-0 left-0 right-0 bg-primary-white z-fixed border-b border-border-gray">
        <nav class="flex justify-between items-center px-6 py-4">
            <!-- Login Button -->
            <button onclick="openLoginModal()" class="bg-primary-black text-primary-white px-6 py-2 hover:bg-dark-gray transition-colors">
                Login
            </button>
            
            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-primary-black rounded flex items-center justify-center">
                    <i class="fas fa-shield-alt text-primary-white text-sm"></i>
                </div>
                <span class="text-xl font-medium text-primary-black">HSE Management System</span>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="pt-20 px-6 pb-12">
        <!-- Hero Section -->
        <section class="max-w-4xl mx-auto text-center py-16">
            <h1 class="text-4xl font-normal text-primary-black mb-6">
                HSE Management System
            </h1>
            <p class="text-lg text-medium-gray mb-12 max-w-2xl mx-auto">
                Streamlining Safety, Ensuring Compliance, Protecting People
            </p>
            
            <!-- Report Incident Button -->
            <button onclick="openIncidentModal()" class="bg-primary-black text-white px-8 py-4 text-base font-medium hover:bg-dark-gray transition-colors flex items-center mx-auto space-x-2">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Report Incident</span>
            </button>
        </section>

        <!-- Company Portal Grid -->
        <section class="max-w-6xl mx-auto">
            <h2 class="text-2xl font-normal text-primary-black mb-8 text-center">Company Portals</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse ($companies as $company)
                    <div onclick="window.location.href='{{ route('company.dashboard', $company->id) }}'" class="bg-white border border-border-gray p-6 cursor-pointer hover:border-dark-gray transition-colors">
                        <div class="w-12 h-12 bg-primary-black rounded flex items-center justify-center mb-4">
                            <i class="fas fa-building text-white text-lg"></i>
                        </div>
                        <h3 class="text-base font-medium text-primary-black mb-2">{{ $company->name }}</h3>
                        <p class="text-sm text-medium-gray">{{ $company->description ?? 'Safety & Compliance Partner' }}</p>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-medium-gray">No companies available at the moment.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-background-gray border-t border-border-gray py-8">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h4 class="font-medium text-primary-black mb-3">Contact Information</h4>
                    <p class="text-sm text-medium-gray">Support: support@hse-system.com</p>
                    <p class="text-sm text-medium-gray">Emergency: 1-800-HSE-HELP</p>
                </div>
                <div>
                    <h4 class="font-medium text-primary-black mb-3">Quick Links</h4>
                    <a href="#" class="text-sm text-medium-gray hover:text-dark-gray block">Privacy Policy</a>
                    <a href="#" class="text-sm text-medium-gray hover:text-dark-gray block">Terms of Service</a>
                </div>
                <div>
                    <h4 class="font-medium text-primary-black mb-3">System Information</h4>
                    <p class="text-sm text-medium-gray">Version 1.0.0</p>
                    <p class="text-sm text-medium-gray">&copy; 2024 HSE Management System</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 bg-primary-black bg-opacity-50 z-modal-backdrop hidden flex items-center justify-center p-4">
        <div class="bg-primary-white max-w-md w-full p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-medium text-primary-black">Login</h3>
                <button onclick="closeLoginModal()" class="text-medium-gray hover:text-primary-black">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="loginForm" class="space-y-4" method="POST" action="/login">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-primary-black mb-2">Username/Email</label>
                    <div class="relative">
                        <input type="text" name="email" class="w-full pl-10 pr-4 py-3 border border-border-gray focus:border-primary-black focus:outline-none @error('email') border-red-500 @enderror" placeholder="Enter your username or email" value="{{ old('email') }}" required>
                        <i class="fas fa-user absolute left-3 top-3.5 w-4 h-4 text-medium-gray"></i>
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-primary-black mb-2">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="passwordInput" class="w-full pl-10 pr-12 py-3 border border-border-gray focus:border-primary-black focus:outline-none @error('password') border-red-500 @enderror" placeholder="Enter your password" required>
                        <i class="fas fa-lock absolute left-3 top-3.5 w-4 h-4 text-medium-gray"></i>
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-3.5 text-medium-gray hover:text-primary-black">
                            <i id="eyeIcon" class="fas fa-eye w-4 h-4"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300" {{ old('remember') ? 'checked' : '' }}>
                        <span class="text-sm text-medium-gray">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-dark-gray hover:underline">Forgot Password?</a>
                </div>
                
                <button type="submit" class="w-full bg-primary-black text-primary-white py-3 font-medium hover:bg-dark-gray transition-colors flex items-center justify-center space-x-2">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Incident Report Modal -->
    <div id="incidentModal" class="fixed inset-0 bg-primary-black bg-opacity-50 z-modal-backdrop hidden flex items-center justify-center p-4">
        <div class="bg-primary-white max-w-2xl w-full p-8 max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-medium text-primary-black">Report Incident</h3>
                <button onclick="closeIncidentModal()" class="text-medium-gray hover:text-primary-black">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="incidentForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-primary-black mb-2">Your Name *</label>
                        <input type="text" name="reporter_name" required class="w-full px-4 py-3 border border-border-gray focus:border-primary-black focus:outline-none" placeholder="Enter your full name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-black mb-2">Email Address *</label>
                        <input type="email" name="reporter_email" required class="w-full px-4 py-3 border border-border-gray focus:border-primary-black focus:outline-none" placeholder="your.email@example.com">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-primary-black mb-2">Phone Number</label>
                        <input type="tel" name="reporter_phone" class="w-full px-4 py-3 border border-border-gray focus:border-primary-black focus:outline-none" placeholder="+1 (555) 123-4567">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-black mb-2">Incident Type *</label>
                        <select name="incident_type" required class="w-full px-4 py-3 border border-border-gray focus:border-primary-black focus:outline-none">
                            <option value="">Select incident type</option>
                            <option value="Injury">Injury</option>
                            <option value="Illness">Illness</option>
                            <option value="Near Miss">Near Miss</option>
                            <option value="Property Damage">Property Damage</option>
                            <option value="Environmental">Environmental</option>
                            <option value="Vehicle">Vehicle</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-primary-black mb-2">Description *</label>
                    <textarea name="description" required rows="4" class="w-full px-4 py-3 border border-border-gray focus:border-primary-black focus:outline-none" placeholder="Provide detailed description of the incident"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-primary-black mb-2">Images (Optional)</label>
                    <div class="border-2 border-dashed border-border-gray rounded-lg p-6 text-center hover:border-primary-black transition-colors" id="imageUploadArea">
                        <i class="fas fa-cloud-upload-alt text-3xl text-medium-gray mb-2"></i>
                        <p class="text-sm text-medium-gray mb-2">Click to upload or drag and drop</p>
                        <p class="text-xs text-medium-gray">PNG, JPG, GIF up to 5MB each (max 5 files)</p>
                        <input type="file" name="images[]" multiple accept="image/*" class="hidden" id="imageInput">
                        <button type="button" onclick="document.getElementById('imageInput').click()" class="mt-2 px-4 py-2 bg-primary-black text-primary-white text-sm font-medium hover:bg-dark-gray">
                            Choose Files
                        </button>
                    </div>
                    <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-4"></div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-primary-black mb-2">Location *</label>
                        <input type="text" name="location" required class="w-full px-4 py-3 border border-border-gray focus:border-primary-black focus:outline-none" placeholder="Where did the incident occur?">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-black mb-2">Date & Time *</label>
                        <input type="datetime-local" name="incident_date" required class="w-full px-4 py-3 border border-border-gray focus:border-primary-black focus:outline-none">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-primary-black mb-2">Severity *</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="severity" value="low" required class="sr-only peer">
                            <div class="border border-border-gray p-3 text-center peer:border-primary-black peer:bg-hover-gray hover:bg-hover-gray">
                                <span class="text-sm font-medium">Low</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="severity" value="medium" required class="sr-only peer">
                            <div class="border border-border-gray p-3 text-center peer:border-primary-black peer:bg-hover-gray hover:bg-hover-gray">
                                <span class="text-sm font-medium">Medium</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="severity" value="high" required class="sr-only peer">
                            <div class="border border-border-gray p-3 text-center peer:border-primary-black peer:bg-hover-gray hover:bg-hover-gray">
                                <span class="text-sm font-medium">High</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="severity" value="critical" required class="sr-only peer">
                            <div class="border border-border-gray p-3 text-center peer:border-primary-black peer:bg-hover-gray hover:bg-hover-gray">
                                <span class="text-sm font-medium">Critical</span>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeIncidentModal()" class="px-6 py-3 border border-border-gray text-medium-gray hover:bg-hover-gray">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-primary-black text-primary-white font-medium hover:bg-dark-gray transition-colors">
                        Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Message -->
    <div id="successMessage" class="fixed top-24 right-6 bg-primary-black text-primary-white px-6 py-4 hidden z-modal">
        <div class="flex items-center space-x-3">
            <i class="fas fa-check-circle text-green-400"></i>
            <div>
                <p class="font-medium">Success!</p>
                <p id="successText" class="text-sm"></p>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function openLoginModal() {
            document.getElementById('loginModal').classList.remove('hidden');
            document.getElementById('loginModal').classList.add('flex');
            // Focus on email input
            setTimeout(() => {
                document.querySelector('input[name="email"]').focus();
            }, 100);
        }

        function closeLoginModal() {
            document.getElementById('loginModal').classList.add('hidden');
            document.getElementById('loginModal').classList.remove('flex');
        }

        function openIncidentModal() {
            document.getElementById('incidentModal').classList.remove('hidden');
            document.getElementById('incidentModal').classList.add('flex');
        }

        function closeIncidentModal() {
            document.getElementById('incidentModal').classList.add('hidden');
            document.getElementById('incidentModal').classList.remove('flex');
        }

        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
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

        // Form submissions
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/login', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    HSEUtils.showSuccess('Login successful! Redirecting to dashboard...');
                    setTimeout(() => {
                        closeLoginModal();
                        window.location.href = data.redirect || '/dashboard';
                    }, 1500);
                } else {
                    HSEUtils.showError(data.message || 'Login failed. Please check your credentials.');
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                HSEUtils.showError('Login error. Please try again.');
            });
        });

        document.getElementById('incidentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            
            // Add form fields
            const formElements = this.querySelectorAll('input, select, textarea');
            formElements.forEach(element => {
                if (element.type === 'file') {
                    // Add each uploaded image
                    uploadedImages.forEach((file, index) => {
                        formData.append(`images[${index}]`, file);
                    });
                } else if (element.name) {
                    formData.append(element.name, element.value);
                }
            });
            
            fetch('/report-incident', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    HSEUtils.showSuccess(data.message);
                    closeIncidentModal();
                    document.getElementById('incidentForm').reset();
                    imagePreview.innerHTML = '';
                    uploadedImages = [];
                } else {
                    HSEUtils.showError('Error reporting incident. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                HSEUtils.showError('Error reporting incident. Please try again.');
            });
        });

        // Close modals when clicking outside
        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLoginModal();
            }
        });

        document.getElementById('incidentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeIncidentModal();
            }
        });

        // Set current datetime for incident form
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const datetime = now.toISOString().slice(0, 16);
            const incidentDateInput = document.querySelector('input[name="incident_date"]');
            if (incidentDateInput) {
                incidentDateInput.value = datetime;
            }
        });

        // Image upload functionality
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const imageUploadArea = document.getElementById('imageUploadArea');
        let uploadedImages = [];

        // Handle file selection
        imageInput.addEventListener('change', function(e) {
            handleFiles(e.target.files);
        });

        // Handle drag and drop
        imageUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-primary-black', 'bg-hover-gray');
        });

        imageUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-primary-black', 'bg-hover-gray');
        });

        imageUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-primary-black', 'bg-hover-gray');
            handleFiles(e.dataTransfer.files);
        });

        function handleFiles(files) {
            // Clear previous previews
            imagePreview.innerHTML = '';
            uploadedImages = [];

            // Limit to 5 files
            const filesToProcess = Array.from(files).slice(0, 5);

            filesToProcess.forEach((file, index) => {
                if (file.type.startsWith('image/') && file.size <= 5 * 1024 * 1024) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'relative group';
                        previewDiv.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-24 object-cover rounded border border-border-gray">
                            <button type="button" onclick="removeImage(${index})" class="absolute top-1 right-1 bg-red-500 text-primary-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                            <div class="absolute bottom-0 left-0 right-0 bg-primary-black bg-opacity-50 text-primary-white text-xs p-1 rounded-b">
                                ${file.name}
                            </div>
                        `;
                        imagePreview.appendChild(previewDiv);
                        uploadedImages.push(file);
                    };
                    
                    reader.readAsDataURL(file);
                } else {
                    alert(`File "${file.name}" is not a valid image or is too large (max 5MB)`);
                }
            });
        }

        function removeImage(index) {
            uploadedImages.splice(index, 1);
            
            // Recreate previews
            const tempFiles = [...uploadedImages];
            imageInput.value = ''; // Clear input
            handleFiles(tempFiles);
        }

        // Update form submission to handle images
        document.getElementById('incidentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            
            // Add form fields
            const formElements = this.querySelectorAll('input, select, textarea');
            formElements.forEach(element => {
                if (element.type === 'file') {
                    // Add each uploaded image
                    uploadedImages.forEach((file, index) => {
                        formData.append(`images[${index}]`, file);
                    });
                } else if (element.name) {
                    formData.append(element.name, element.value);
                }
            });
            
            fetch('/report-incident', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage(data.message);
                    closeIncidentModal();
                    document.getElementById('incidentForm').reset();
                    imagePreview.innerHTML = '';
                    uploadedImages = [];
                } else {
                    alert('Error reporting incident. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error reporting incident. Please try again.');
            });
        });
    </script>
</body>
</html>
