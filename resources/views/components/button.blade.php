{{-- Button Component --}}
@props(['type' => 'primary', 'size' => 'md', 'disabled' => false, 'class' => ''])

@php
    $baseClasses = 'font-medium transition-colors inline-flex items-center justify-center';
    
    $sizeClasses = match($size) {
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-6 py-3 text-base',
        'lg' => 'px-8 py-4 text-lg',
        default => 'px-6 py-3 text-base'
    };
    
    $typeClasses = match($type) {
        'primary' => 'bg-primary-black text-white hover:bg-dark-gray focus:ring-2 focus:ring-medium-gray',
        'secondary' => 'bg-primary-white text-primary-black border border-border-gray hover:bg-hover-gray focus:ring-2 focus:ring-medium-gray',
        'ghost' => 'bg-transparent text-primary-black hover:bg-hover-gray focus:ring-2 focus:ring-medium-gray',
        'success' => 'bg-green-500 text-white hover:bg-green-600 focus:ring-2 focus:ring-green-300',
        'warning' => 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-300',
        'error' => 'bg-red-500 text-white hover:bg-red-600 focus:ring-2 focus:ring-red-300',
        default => 'bg-primary-black text-white hover:bg-dark-gray focus:ring-2 focus:ring-medium-gray'
    };
    
    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';
    
    $allClasses = implode(' ', array_filter([$baseClasses, $sizeClasses, $typeClasses, $disabledClasses, $class]));
@endphp

<button {{ $attributes->merge(['class' => $allClasses, 'disabled' => $disabled]) }}>
    {{ $slot }}
</button>
