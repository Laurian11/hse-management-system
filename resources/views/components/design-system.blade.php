{{-- Design System CSS Variables --}}
<style>
:root {
    /* Minimal 3-Color Theme */
    --color-primary: {{ $colors['primary']['black'] }};
    --color-secondary: {{ $colors['secondary']['gray'] }};
    --color-accent: {{ $colors['accent']['blue'] }};
    
    --color-primary-black: {{ $colors['primary']['black'] }};
    --color-primary-white: {{ $colors['primary']['white'] }};
    
    --color-gray-50: {{ $colors['gray']['50'] }};
    --color-gray-100: {{ $colors['gray']['100'] }};
    --color-gray-200: {{ $colors['gray']['200'] }};
    --color-gray-300: {{ $colors['gray']['300'] }};
    --color-gray-400: {{ $colors['gray']['400'] }};
    --color-gray-500: {{ $colors['gray']['500'] }};
    --color-gray-600: {{ $colors['gray']['600'] }};
    
    --color-success: {{ $colors['semantic']['success'] }};
    --color-warning: {{ $colors['semantic']['warning'] }};
    --color-error: {{ $colors['semantic']['error'] }};
    --color-info: {{ $colors['semantic']['info'] }};

    /* Typography */
    --font-family: {{ $typography['font_family'] }};
    --font-weight-light: {{ $typography['weights']['light'] }};
    --font-weight-normal: {{ $typography['weights']['normal'] }};
    --font-weight-medium: {{ $typography['weights']['medium'] }};
    --font-weight-semibold: {{ $typography['weights']['semibold'] }};
    --font-weight-bold: {{ $typography['weights']['bold'] }};
    
    --font-size-xs: {{ $typography['sizes']['xs'] }};
    --font-size-sm: {{ $typography['sizes']['sm'] }};
    --font-size-base: {{ $typography['sizes']['base'] }};
    --font-size-lg: {{ $typography['sizes']['lg'] }};
    --font-size-xl: {{ $typography['sizes']['xl'] }};
    --font-size-2xl: {{ $typography['sizes']['2xl'] }};
    --font-size-3xl: {{ $typography['sizes']['3xl'] }};
    --font-size-4xl: {{ $typography['sizes']['4xl'] }};

    /* Spacing */
    --spacing-xs: {{ $spacing['xs'] }};
    --spacing-sm: {{ $spacing['sm'] }};
    --spacing-md: {{ $spacing['md'] }};
    --spacing-lg: {{ $spacing['lg'] }};
    --spacing-xl: {{ $spacing['xl'] }};
    --spacing-2xl: {{ $spacing['2xl'] }};

    /* Border Radius */
    --border-radius-none: {{ $borderRadius['none'] }};
    --border-radius-sm: {{ $borderRadius['sm'] }};
    --border-radius-md: {{ $borderRadius['md'] }};
    --border-radius-lg: {{ $borderRadius['lg'] }};
    --border-radius-xl: {{ $borderRadius['xl'] }};
    --border-radius-full: {{ $borderRadius['full'] }};

    /* Shadows */
    --shadow-none: {{ $shadows['none'] }};
    --shadow-sm: {{ $shadows['sm'] }};
    --shadow-md: {{ $shadows['md'] }};
    --shadow-lg: {{ $shadows['lg'] }};

    /* Transitions */
    --transition-fast: {{ $transitions['fast'] }};
    --transition-normal: {{ $transitions['normal'] }};
    --transition-slow: {{ $transitions['slow'] }};

    /* Z-Index */
    --z-dropdown: {{ $zIndex['dropdown'] }};
    --z-sticky: {{ $zIndex['sticky'] }};
    --z-fixed: {{ $zIndex['fixed'] }};
    --z-modal-backdrop: {{ $zIndex['modal_backdrop'] }};
    --z-modal: {{ $zIndex['modal'] }};
    --z-popover: {{ $zIndex['popover'] }};
    --z-tooltip: {{ $zIndex['tooltip'] }};

    /* Component-specific colors */
    --btn-primary-bg: {{ $components['buttons']['primary']['bg'] }};
    --btn-primary-text: {{ $components['buttons']['primary']['text'] }};
    --btn-primary-hover-bg: {{ $components['buttons']['primary']['hover_bg'] }};
    --btn-primary-border: {{ $components['buttons']['primary']['border'] }};
    --btn-primary-focus-ring: {{ $components['buttons']['primary']['focus_ring'] }};
    
    --btn-secondary-bg: {{ $components['buttons']['secondary']['bg'] }};
    --btn-secondary-text: {{ $components['buttons']['secondary']['text'] }};
    --btn-secondary-hover-bg: {{ $components['buttons']['secondary']['hover_bg'] }};
    --btn-secondary-border: {{ $components['buttons']['secondary']['border'] }};
    --btn-secondary-focus-ring: {{ $components['buttons']['secondary']['focus_ring'] }};
    
    --btn-ghost-bg: {{ $components['buttons']['ghost']['bg'] }};
    --btn-ghost-text: {{ $components['buttons']['ghost']['text'] }};
    --btn-ghost-hover-bg: {{ $components['buttons']['ghost']['hover_bg'] }};
    --btn-ghost-border: {{ $components['buttons']['ghost']['border'] }};
    --btn-ghost-focus-ring: {{ $components['buttons']['ghost']['focus_ring'] }};

    --input-bg: {{ $components['forms']['input']['bg'] }};
    --input-text: {{ $components['forms']['input']['text'] }};
    --input-placeholder: {{ $components['forms']['input']['placeholder'] }};
    --input-border: {{ $components['forms']['input']['border'] }};
    --input-focus-border: {{ $components['forms']['input']['focus_border'] }};
    --input-focus-ring: {{ $components['forms']['input']['focus_ring'] }};
    --input-disabled-bg: {{ $components['forms']['input']['disabled_bg'] }};
    --input-disabled-text: {{ $components['forms']['input']['disabled_text'] }};
    
    --label-text: {{ $components['forms']['label']['text'] }};
    --label-required: {{ $components['forms']['label']['required'] }};

    --card-bg: {{ $components['cards']['bg'] }};
    --card-text: {{ $components['cards']['text'] }};
    --card-border: {{ $components['cards']['border'] }};
    --card-hover-border: {{ $components['cards']['hover_border'] }};
    --card-shadow: {{ $components['cards']['shadow'] }};

    --modal-backdrop: {{ $components['modals']['backdrop'] }};
    --modal-bg: {{ $components['modals']['bg'] }};
    --modal-text: {{ $components['modals']['text'] }};
    --modal-border: {{ $components['modals']['border'] }};

    --nav-bg: {{ $components['navigation']['bg'] }};
    --nav-text: {{ $components['navigation']['text'] }};
    --nav-border: {{ $components['navigation']['border'] }};
    --nav-hover-text: {{ $components['navigation']['hover_text'] }};
    --nav-active-text: {{ $components['navigation']['active_text'] }};
}

/* Dark Mode Variables */
[data-theme="dark"] {
    --color-primary-black: #FFFFFF;
    --color-primary-white: #1A1A1A;
    --color-secondary-gray: #2D2D2D;
    --color-gray-50: #2D2D2D;
    --color-gray-100: #3A3A3A;
    --color-gray-200: #4A4A4A;
    --color-gray-300: #5A5A5A;
    --color-gray-400: #7A7A7A;
    --color-gray-500: #9A9A9A;
    --color-gray-600: #FFFFFF;
    
    --card-bg: #2D2D2D;
    --card-text: #FFFFFF;
    --card-border: #4A4A4A;
    
    --input-bg: #2D2D2D;
    --input-text: #FFFFFF;
    --input-border: #4A4A4A;
    --input-focus-border: #0066CC;
    
    --nav-bg: #1A1A1A;
    --nav-text: #FFFFFF;
    --nav-border: #4A4A4A;
    
    --modal-bg: #2D2D2D;
    --modal-text: #FFFFFF;
    --modal-border: #4A4A4A;
}

/* Smooth theme transition */
* {
    transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
}
</style>

<script>
// Design System JavaScript Helper
window.HSEDesignSystem = {
    colors: {
        primary: {
            black: '{{ $colors['primary']['black'] }}',
            white: '{{ $colors['primary']['white'] }}',
        },
        gray: {
            50: '{{ $colors['gray']['50'] }}',
            100: '{{ $colors['gray']['100'] }}',
            200: '{{ $colors['gray']['200'] }}',
            300: '{{ $colors['gray']['300'] }}',
            400: '{{ $colors['gray']['400'] }}',
            500: '{{ $colors['gray']['500'] }}',
            600: '{{ $colors['gray']['600'] }}',
        },
        semantic: {
            success: '{{ $colors['semantic']['success'] }}',
            warning: '{{ $colors['semantic']['warning'] }}',
            error: '{{ $colors['semantic']['error'] }}',
            info: '{{ $colors['semantic']['info'] }}',
        }
    },
    
    typography: {
        fontFamily: '{{ $typography['font_family'] }}',
        weights: {
            light: {{ $typography['weights']['light'] }},
            normal: {{ $typography['weights']['normal'] }},
            medium: {{ $typography['weights']['medium'] }},
            semibold: {{ $typography['weights']['semibold'] }},
            bold: {{ $typography['weights']['bold'] }},
        },
        sizes: {
            xs: '{{ $typography['sizes']['xs'] }}',
            sm: '{{ $typography['sizes']['sm'] }}',
            base: '{{ $typography['sizes']['base'] }}',
            lg: '{{ $typography['sizes']['lg'] }}',
            xl: '{{ $typography['sizes']['xl'] }}',
            '2xl': '{{ $typography['sizes']['2xl'] }}',
            '3xl': '{{ $typography['sizes']['3xl'] }}',
            '4xl': '{{ $typography['sizes']['4xl'] }}',
        }
    },
    
    components: {{ json_encode($components) }},
    
    // Helper function to get CSS variable value
    getCSSVar: function(variable) {
        return getComputedStyle(document.documentElement).getPropertyValue(variable);
    },
    
    // Helper function to set CSS variable
    setCSSVar: function(variable, value) {
        document.documentElement.style.setProperty(variable, value);
    },
    
    // Apply theme
    applyTheme: function(theme) {
        if (theme.colors) {
            Object.keys(theme.colors).forEach(key => {
                this.setCSSVar(`--color-${key}`, theme.colors[key]);
            });
        }
    }
};
</script>
