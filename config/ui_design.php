<?php

return [
    /*
    |--------------------------------------------------------------------------
    | UI Design System Configuration
    |--------------------------------------------------------------------------
    |
    | Centralized color scheme and design tokens for the HSE Management System.
    | This ensures consistency across all views and components.
    |
    */

    'colors' => [
        // Minimal 3-Color Theme
        'primary' => [
            'black' => '#000000',  // Primary: Text, borders, primary actions
            'white' => '#FFFFFF',  // Background
        ],
        
        'secondary' => [
            'gray' => '#F5F5F5',  // Secondary: Backgrounds, cards
        ],
        
        'accent' => [
            'blue' => '#0066CC',  // Accent: Links, buttons, interactive elements
        ],
        
        // Simplified Gray Scale (derived from 3 colors)
        'gray' => [
            '50' => '#F5F5F5',   // Secondary background
            '100' => '#E0E0E0',  // Hover states
            '200' => '#CCCCCC',  // Borders
            '300' => '#999999',  // Muted text
            '400' => '#666666',  // Secondary text
            '500' => '#333333',  // Dark text
            '600' => '#000000',  // Primary black
        ],
        
        // Semantic Colors (minimal, using accent)
        'semantic' => [
            'success' => '#0066CC',  // Use accent blue
            'warning' => '#FF9900',  // Orange for warnings
            'error' => '#CC0000',     // Red for errors
            'info' => '#0066CC',      // Use accent blue
        ],
    ],

    'typography' => [
        'font_family' => 'Inter, sans-serif',
        'weights' => [
            'light' => '300',
            'normal' => '400',
            'medium' => '500',
            'semibold' => '600',
            'bold' => '700',
        ],
        'sizes' => [
            'xs' => '0.75rem',    // 12px
            'sm' => '0.875rem',   // 14px
            'base' => '1rem',     // 16px
            'lg' => '1.125rem',   // 18px
            'xl' => '1.25rem',    // 20px
            '2xl' => '1.5rem',    // 24px
            '3xl' => '1.875rem',  // 30px
            '4xl' => '2.25rem',   // 36px
        ],
    ],

    'spacing' => [
        'xs' => '0.25rem',   // 4px
        'sm' => '0.5rem',    // 8px
        'md' => '1rem',      // 16px
        'lg' => '1.5rem',    // 24px
        'xl' => '2rem',      // 32px
        '2xl' => '3rem',     // 48px
    ],

    'border_radius' => [
        'none' => '0',  // Flat design - no rounded corners
        'sm' => '0',
        'md' => '0',
        'lg' => '0',
        'xl' => '0',
        'full' => '0',
    ],

    'shadows' => [
        'none' => 'none',  // Flat design - no shadows
        'sm' => 'none',
        'md' => 'none',
        'lg' => 'none',
    ],

    'transitions' => [
        'fast' => '150ms ease-in-out',
        'normal' => '250ms ease-in-out',
        'slow' => '350ms ease-in-out',
    ],

    'z_index' => [
        'dropdown' => '10',
        'sticky' => '20',
        'fixed' => '30',
        'modal_backdrop' => '40',
        'modal' => '50',
        'popover' => '60',
        'tooltip' => '70',
    ],

    'breakpoints' => [
        'sm' => '640px',
        'md' => '768px',
        'lg' => '1024px',
        'xl' => '1280px',
        '2xl' => '1536px',
    ],

    // Component-specific styling
    'components' => [
        'buttons' => [
            'primary' => [
                'bg' => '#0066CC',  // Accent blue
                'text' => '#FFFFFF',
                'hover_bg' => '#0052A3',
                'border' => '#0066CC',
                'focus_ring' => '#0066CC',
            ],
            'secondary' => [
                'bg' => '#F5F5F5',  // Secondary gray
                'text' => '#000000',
                'hover_bg' => '#E0E0E0',
                'border' => '#CCCCCC',
                'focus_ring' => '#0066CC',
            ],
            'ghost' => [
                'bg' => 'transparent',
                'text' => '#000000',
                'hover_bg' => '#F5F5F5',
                'border' => 'transparent',
                'focus_ring' => '#0066CC',
            ],
        ],
        
        'forms' => [
            'input' => [
                'bg' => '#FFFFFF',
                'text' => '#000000',
                'placeholder' => '#666666',
                'border' => '#e0e0e0',
                'focus_border' => '#000000',
                'focus_ring' => '#666666',
                'disabled_bg' => '#f5f5f5',
                'disabled_text' => '#999999',
            ],
            'label' => [
                'text' => '#000000',
                'required' => '#dc3545',
            ],
        ],
        
        'cards' => [
            'bg' => '#FFFFFF',
            'text' => '#000000',
            'border' => '#CCCCCC',  // Flat border
            'hover_border' => '#0066CC',  // Accent on hover
            'shadow' => 'none',  // No shadows
        ],
        
        'modals' => [
            'backdrop' => 'rgba(0, 0, 0, 0.5)',
            'bg' => '#FFFFFF',
            'text' => '#000000',
            'border' => '#e0e0e0',
        ],
        
        'navigation' => [
            'bg' => '#FFFFFF',
            'text' => '#000000',
            'border' => '#e0e0e0',
            'hover_text' => '#1a1a1a',
            'active_text' => '#000000',
        ],
    ],
];
