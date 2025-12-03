{{-- Input Field Component --}}
@props(['type' => 'text', 'label' => null, 'placeholder' => '', 'required' => false, 'disabled' => false, 'error' => null, 'class' => ''])

@php
    $baseClasses = 'w-full px-4 py-3 border border-border-gray focus:border-primary-black focus:outline-none focus:ring-2 focus:ring-medium-gray transition-colors';
    
    $disabledClasses = $disabled ? 'bg-background-gray text-light-gray cursor-not-allowed' : '';
    
    $errorClasses = $error ? 'border-red-500 focus:border-red-500 focus:ring-red-300' : '';
    
    $allClasses = implode(' ', array_filter([$baseClasses, $disabledClasses, $errorClasses, $class]));
@endphp

@if($label)
    <label class="block text-sm font-medium text-primary-black mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
@endif

<input 
    type="{{ $type }}" 
    {{ $attributes->merge([
        'class' => $allClasses,
        'placeholder' => $placeholder,
        'required' => $required,
        'disabled' => $disabled
    ]) }}
>

@if($error)
    <p class="mt-1 text-sm text-red-500">{{ $error }}</p>
@endif
