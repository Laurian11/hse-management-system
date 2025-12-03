<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\View\Components\DesignSystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Design System component
        Blade::component('design-system', DesignSystem::class);
        
        // Create custom Blade directives for design tokens
        Blade::directive('color', function ($expression) {
            return "<?php echo config('ui_design.colors.' . $expression); ?>";
        });
        
        Blade::directive('spacing', function ($expression) {
            return "<?php echo config('ui_design.spacing.' . $expression); ?>";
        });
        
        Blade::directive('fontSize', function ($expression) {
            return "<?php echo config('ui_design.typography.sizes.' . $expression); ?>";
        });
        
        Blade::directive('borderRadius', function ($expression) {
            return "<?php echo config('ui_design.border_radius.' . $expression); ?>";
        });
        
        // Create component shortcuts
        Blade::directive('btnPrimary', function () {
            return 'class="bg-primary-black text-white px-6 py-3 font-medium hover:bg-dark-gray transition-colors"';
        });
        
        Blade::directive('btnSecondary', function () {
            return 'class="bg-white text-primary-black px-6 py-3 font-medium border border-border-gray hover:bg-hover-gray transition-colors"';
        });
        
        Blade::directive('card', function () {
            return 'class="bg-white border border-border-gray p-6"';
        });
        
        Blade::directive('inputField', function () {
            return 'class="w-full px-4 py-3 border border-border-gray focus:border-primary-black focus:outline-none"';
        });
        
        Blade::directive('textPrimary', function () {
            return 'class="text-primary-black"';
        });
        
        Blade::directive('textSecondary', function () {
            return 'class="text-medium-gray"';
        });
    }
}
