# HSE Management System - Design System Documentation

## Overview
The HSE Management System now features a centralized design system that ensures UI consistency and makes global color scheme management easy throughout the entire application.

## Architecture

### 1. Configuration File (`config/ui_design.php`)
Central configuration file containing all design tokens:
- **Colors**: Complete color palette (white & black theme)
- **Typography**: Font families, weights, and sizes
- **Spacing**: Consistent spacing scale
- **Border Radius**: Rounded corner values
- **Shadows**: Shadow definitions
- **Transitions**: Animation timing
- **Z-Index**: Layer management
- **Breakpoints**: Responsive breakpoints
- **Components**: Pre-defined component styles

### 2. Design System Component (`app/View/Components/DesignSystem.php`)
Laravel component that:
- Loads design configuration
- Generates CSS variables
- Provides JavaScript helpers
- Makes design tokens available to views

### 3. Layout Template (`resources/views/layouts/app.blade.php`)
Master layout that:
- Includes the design system component
- Configures Tailwind CSS with design tokens
- Provides common JavaScript utilities
- Sets up HTML structure

### 4. Blade Components
Reusable components with built-in design tokens:
- **Button** (`<x-button>`) - Multiple variants and sizes
- **Card** (`<x-card>`) - Configurable containers
- **Input** (`<x-input>`) - Form fields with validation

### 5. Blade Directives
Custom Blade directives for common patterns:
- `@btnPrimary` - Primary button styling
- `@btnSecondary` - Secondary button styling
- `@card` - Card container styling
- `@inputField` - Input field styling
- `@textPrimary` - Primary text color
- `@textSecondary` - Secondary text color

## Color Scheme

### Primary Colors (White & Black Theme)
```css
--color-primary-black: #000000;
--color-primary-white: #FFFFFF;
```

### Gray Scale
```css
--color-gray-50: #f5f5f5;   /* Backgrounds */
--color-gray-100: #f0f0f0;  /* Hover states */
--color-gray-200: #e0e0e0;  /* Borders */
--color-gray-300: #d0d0d0;  /* Light borders */
--color-gray-400: #999999;  /* Muted text */
--color-gray-500: #666666;  /* Body text */
--color-gray-600: #333333;  /* Accent text */
--color-gray-700: #1a1a1a;  /* Dark text */
--color-gray-800: #000000;  /* Primary black */
```

### Semantic Colors
```css
--color-success: #28a745;
--color-warning: #ffc107;
--color-error: #dc3545;
--color-info: #17a2b8;
```

## Usage Examples

### Using Design System in Views
```blade
@extends('layouts.app')

@section('content')
    <!-- Design system is automatically included -->
    <div class="bg-primary-white border border-border-gray p-6">
        <h1 class="text-primary-black text-2xl font-medium">
            Title using design tokens
        </h1>
        <p class="text-medium-gray">
            Body text with consistent styling
        </p>
    </div>
@endsection
```

### Using Blade Components
```blade
<!-- Button with design tokens -->
<x-button type="primary" size="lg">
    Submit Form
</x-button>

<!-- Card component -->
<x-card border shadow>
    <h3>Card Title</h3>
    <p>Card content</p>
</x-card>

<!-- Input with validation -->
<x-input type="email" label="Email" required error="Invalid email" />
```

### Using Blade Directives
```blade
<button @btnPrimary>Primary Button</button>
<div @card>Card Content</div>
<input @inputField placeholder="Enter text">
<p @textPrimary>Important text</p>
<p @textSecondary>Secondary text</p>
```

### JavaScript Access
```javascript
// Access design tokens in JavaScript
const primaryColor = HSEDesignSystem.colors.primary.black;
const spacing = HSEDesignSystem.getCSSVar('--spacing-md');

// Update theme dynamically
HSEDesignSystem.applyTheme({
    colors: {
        'primary-black': '#FF0000' // Change to red
    }
});
```

## Benefits

### 1. **Consistency**
- All components use the same design tokens
- No hardcoded colors in views
- Consistent spacing and typography

### 2. **Maintainability**
- Change colors globally from one file
- Update spacing scales centrally
- Modify component styles in one place

### 3. **Scalability**
- Easy to add new color themes
- Simple to extend component library
- Consistent design as system grows

### 4. **Developer Experience**
- IntelliSense support for design tokens
- Reusable components
- Clear naming conventions

## Global Color Management

### Changing the Color Scheme
To change the entire system's colors:

1. **Update Configuration** (`config/ui_design.php`)
```php
'colors' => [
    'primary' => [
        'black' => '#1a1a1a',  // Darker black
        'white' => '#fafafa',  // Off-white
    ],
    // ... other colors
]
```

2. **Clear Cache**
```bash
php artisan config:clear
php artisan view:clear
```

3. **All Views Update Automatically**
- No need to modify individual views
- Components automatically use new colors
- CSS variables update globally

### Adding New Themes
```php
// In config/ui_design.php
'themes' => [
    'default' => [
        'primary' => ['black' => '#000000', 'white' => '#FFFFFF'],
        // ... other tokens
    ],
    'dark' => [
        'primary' => ['black' => '#FFFFFF', 'white' => '#000000'],
        // ... other tokens
    ]
]
```

## Implementation Status

✅ **Completed Features:**
- Centralized design configuration
- CSS variables generation
- Blade components library
- Custom Blade directives
- JavaScript design helpers
- Layout template integration
- Landing page conversion
- White & black color theme

✅ **Active Views Using Design System:**
- Landing page (`resources/views/landing.blade.php`)
- Company dashboard (`resources/views/company-dashboard.blade.php`)

🔄 **Next Steps:**
- Convert remaining views to use design system
- Add theme switching capability
- Create additional Blade components
- Implement design token validation

## File Structure
```
config/
└── ui_design.php                 # Design configuration

app/View/Components/
├── DesignSystem.php             # Main design system component
├── button.blade.php             # Button component
├── card.blade.php               # Card component
└── input.blade.php              # Input component

resources/views/
├── layouts/
│   └── app.blade.php           # Master layout
├── components/
│   └── design-system.blade.php # CSS/JS generation
├── landing.blade.php           # Using design system
└── company-dashboard.blade.php # Using design system

app/Providers/
└── AppServiceProvider.php      # Blade directives registration
```

This design system provides a robust foundation for maintaining UI consistency and makes global color scheme management simple and efficient throughout the entire HSE Management System.
