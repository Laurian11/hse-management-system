{{-- Card Component --}}
@props(['border' => true, 'shadow' => false, 'padding' => 'md', 'class' => ''])

@php
    $baseClasses = 'bg-primary-white';
    
    $borderClasses = $border ? 'border border-border-gray' : '';
    
    $shadowClasses = $shadow ? 'shadow-md' : 'shadow-none';
    
    $paddingClasses = match($padding) {
        'none' => '',
        'sm' => 'p-4',
        'md' => 'p-6',
        'lg' => 'p-8',
        'xl' => 'p-10',
        default => 'p-6'
    };
    
    $allClasses = implode(' ', array_filter([$baseClasses, $borderClasses, $shadowClasses, $paddingClasses, $class]));
@endphp

<div {{ $attributes->merge(['class' => $allClasses]) }}>
    {{ $slot }}
</div>
