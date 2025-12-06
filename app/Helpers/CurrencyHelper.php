<?php

if (!function_exists('format_currency')) {
    /**
     * Format currency amount with proper symbol and formatting
     * 
     * @param float|null $amount
     * @param string|null $currency
     * @return string
     */
    function format_currency(?float $amount, ?string $currency = null): string
    {
        if ($amount === null) {
            return 'N/A';
        }

        $currency = $currency ?? config('system.defaults.currency', 'TZS');
        
        // Format based on currency
        switch (strtoupper($currency)) {
            case 'TZS':
            case 'TSH':
                // Tanzanian Shillings - no decimal places, comma separator
                return 'TSh ' . number_format($amount, 0, '.', ',');
            
            case 'USD':
                return '$' . number_format($amount, 2, '.', ',');
            
            case 'EUR':
                return '€' . number_format($amount, 2, '.', ',');
            
            case 'GBP':
                return '£' . number_format($amount, 2, '.', ',');
            
            default:
                return $currency . ' ' . number_format($amount, 2, '.', ',');
        }
    }
}

