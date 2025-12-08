<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConfigureHesuSmtp extends Command
{
    protected $signature = 'smtp:configure-hesu {--password=}';
    protected $description = 'Configure HESU SMTP settings in .env file';

    public function handle()
    {
        $this->info('=== Configuring HESU SMTP Settings ===');
        $this->info('');

        $envPath = base_path('.env');
        
        if (!File::exists($envPath)) {
            $this->error('.env file not found!');
            return Command::FAILURE;
        }

        $password = $this->option('password');
        if (!$password) {
            $password = $this->secret('Enter password for noreply@hesu.co.tz:');
        }

        $this->info('Updating .env file...');

        $envContent = File::get($envPath);

        // SMTP Configuration
        $smtpConfig = [
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => 'mail.hesu.co.tz',
            'MAIL_PORT' => '465',
            'MAIL_USERNAME' => 'noreply@hesu.co.tz',
            'MAIL_PASSWORD' => $password,
            'MAIL_ENCRYPTION' => 'ssl',
            'MAIL_FROM_ADDRESS' => 'noreply@hesu.co.tz',
            'MAIL_FROM_NAME' => '"HSE Management System"',
        ];

        // Update or add each setting
        foreach ($smtpConfig as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
                $this->info("✓ Updated: {$key}");
            } else {
                // Add new line
                $envContent .= "\n{$replacement}";
                $this->info("✓ Added: {$key}");
            }
        }

        // Save the file
        File::put($envPath, $envContent);

        $this->info('');
        $this->info('✅ .env file updated successfully!');
        $this->info('');
        $this->info('Next steps:');
        $this->info('1. Run: php artisan config:clear');
        $this->info('2. Test: php artisan smtp:test --email=laurianlawrence@hesu.co.tz');
        $this->info('3. Send test email: php artisan test:email test --email=laurianlawrence@hesu.co.tz');

        return Command::SUCCESS;
    }
}

