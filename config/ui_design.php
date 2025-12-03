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
        // Primary Colors - White & Black Theme
        'primary' => [
            'black' => '#000000',
            'white' => '#FFFFFF',
        ],
        
        // Gray Scale
        'gray' => [
            '50' => '#f5f5f5',   // background-gray
            '100' => '#f0f0f0',  // hover-gray
            '200' => '#e0e0e0',  // border-gray
            '300' => '#d0d0d0',  // light-border
            '400' => '#999999',  // light-gray
            '500' => '#666666',  // medium-gray
            '600' => '#333333',  // accent-gray
            '700' => '#1a1a1a',  // dark-gray
            '800' => '#000000',  // primary-black
        ],
        
        // Semantic Colors (for specific use cases)
        'semantic' => [
            'success' => '#28a745',
            'warning' => '#ffc107',
            'error' => '#dc3545',
            'info' => '#17a2b8',
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
        'none' => '0',
        'sm' => '0.25rem',   // 4px
        'md' => '0.5rem',    // 8px
        'lg' => '0.75rem',   // 12px
        'xl' => '1rem',      // 16px
        'full' => '9999px',
    ],

    'shadows' => [
        'none' => 'none',
        'sm' => '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
        'md' => '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
        'lg' => '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
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
                'bg' => '#000000',
                'text' => '#FFFFFF',
                'hover_bg' => '#1a1a1a',
                'border' => '#000000',
                'focus_ring' => '#666666',
            ],
            'secondary' => [
                'bg' => '#FFFFFF',
                'text' => '#000000',
                'hover_bg' => '#f0f0f0',
                'border' => '#e0e0e0',
                'focus_ring' => '#666666',
            ],
            'ghost' => [
                'bg' => 'transparent',
                'text' => '#000000',
                'hover_bg' => '#f0f0f0',
                'border' => 'transparent',
                'focus_ring' => '#666666',
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
            'border' => '#e0e0e0',
            'hover_border' => '#000000',
            'shadow' => 'none',
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
