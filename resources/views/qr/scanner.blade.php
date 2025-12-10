@extends('layouts.app')

@section('title', 'QR Code Scanner')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-md mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">
                <i class="fas fa-qrcode mr-2"></i>QR Code Scanner
            </h1>

            <!-- Camera Scanner -->
            <div id="scanner-container" class="mb-6">
                <div class="relative bg-black rounded-lg overflow-hidden" style="aspect-ratio: 1;">
                    <video id="video" class="w-full h-full object-cover" autoplay playsinline></video>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="border-4 border-blue-500 rounded-lg" style="width: 70%; height: 70%;"></div>
                    </div>
                    <div class="absolute bottom-4 left-0 right-0 text-center text-white">
                        <p class="text-sm">Position QR code within the frame</p>
                    </div>
                </div>
            </div>

            <!-- Manual Input -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Or enter QR code data manually:
                </label>
                <div class="flex gap-2">
                    <input type="text" id="manual-input" 
                           placeholder="Paste QR code URL or data"
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button onclick="processManualInput()" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Status Messages -->
            <div id="status-message" class="hidden mb-4 p-4 rounded-lg"></div>

            <!-- Quick Actions -->
            <div class="space-y-3">
                <a href="{{ route('inspections.create') }}" 
                   class="block w-full text-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-clipboard-check mr-2"></i>Create New Inspection
                </a>
                <a href="{{ route('inspections.audits.create') }}" 
                   class="block w-full text-center px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-search mr-2"></i>Create New Audit
                </a>
                <a href="{{ route('inspections.index') }}" 
                   class="block w-full text-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-list mr-2"></i>View All Inspections
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
let video = null;
let canvas = null;
let context = null;
let scanning = false;

// Initialize scanner
document.addEventListener('DOMContentLoaded', function() {
    video = document.getElementById('video');
    canvas = document.createElement('canvas');
    context = canvas.getContext('2d');

    // Check for camera access
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        startScanning();
    } else {
        document.getElementById('scanner-container').innerHTML = 
            '<div class="p-8 text-center text-gray-500">Camera access not available. Please use manual input.</div>';
    }
});

function startScanning() {
    navigator.mediaDevices.getUserMedia({ 
        video: { 
            facingMode: 'environment' // Use back camera on mobile
        } 
    })
    .then(function(stream) {
        video.srcObject = stream;
        video.setAttribute('playsinline', true);
        video.play();
        scanning = true;
        scanQRCode();
    })
    .catch(function(err) {
        console.error('Camera access error:', err);
        document.getElementById('scanner-container').innerHTML = 
            '<div class="p-8 text-center text-gray-500">Camera access denied. Please use manual input.</div>';
    });
}

function scanQRCode() {
    if (!scanning) return;

    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.height = video.videoHeight;
        canvas.width = video.videoWidth;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);

        if (code) {
            processQRCode(code.data);
            return;
        }
    }

    requestAnimationFrame(scanQRCode);
}

function processQRCode(qrData) {
    scanning = false;
    
    // Stop video stream
    if (video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
    }

    // Show processing message
    showStatus('Processing QR code...', 'info');

    // Extract type and ID from URL
    try {
        const url = new URL(qrData);
        const pathParts = url.pathname.split('/').filter(p => p);
        
        if (pathParts.length >= 3 && pathParts[0] === 'qr') {
            const type = pathParts[1];
            const id = pathParts[2];
            const action = url.searchParams.get('action') || 'view';
            
            // Redirect to scan handler
            window.location.href = `/qr/${type}/${id}?action=${action}`;
        } else {
            // Try to process as direct URL
            window.location.href = qrData;
        }
    } catch (e) {
        // If not a URL, try to process as data
        showStatus('Invalid QR code format', 'error');
        setTimeout(() => {
            scanning = true;
            startScanning();
        }, 2000);
    }
}

function processManualInput() {
    const input = document.getElementById('manual-input');
    const qrData = input.value.trim();
    
    if (!qrData) {
        showStatus('Please enter QR code data', 'error');
        return;
    }

    processQRCode(qrData);
}

function showStatus(message, type) {
    const statusDiv = document.getElementById('status-message');
    statusDiv.className = `p-4 rounded-lg ${type === 'error' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'}`;
    statusDiv.textContent = message;
    statusDiv.classList.remove('hidden');
    
    if (type !== 'error') {
        setTimeout(() => {
            statusDiv.classList.add('hidden');
        }, 3000);
    }
}

// Handle page visibility change
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        scanning = false;
        if (video.srcObject) {
            video.srcObject.getTracks().forEach(track => track.stop());
        }
    } else if (video && !video.srcObject) {
        startScanning();
    }
});
</script>
@endpush
@endsection

