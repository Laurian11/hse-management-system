<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FindSmtpSettings extends Command
{
    protected $signature = 'smtp:find {domain=hesu.co.tz}';
    protected $description = 'Help find SMTP settings for a domain';

    public function handle()
    {
        $domain = $this->argument('domain');
        
        $this->info("=== Finding SMTP Settings for {$domain} ===");
        $this->info('');

        $this->checkCommonSmtpHosts($domain);
        $this->suggestCommonConfigurations();
        $this->provideInstructions();

        return Command::SUCCESS;
    }

    private function checkCommonSmtpHosts(string $domain): void
    {
        $this->info('Testing common SMTP hostnames...');
        $this->info('');

        $hosts = [
            "smtp.{$domain}",
            "mail.{$domain}",
            "smtp1.{$domain}",
            "email.{$domain}",
        ];

        $found = false;
        foreach ($hosts as $host) {
            $this->info("Testing: {$host}...");
            
            // Try port 587
            $connection = @fsockopen($host, 587, $errno, $errstr, 5);
            if ($connection) {
                $this->info("✅ Found: {$host}:587 (TLS)");
                fclose($connection);
                $found = true;
            } else {
                // Try port 465
                $connection = @fsockopen($host, 465, $errno, $errstr, 5);
                if ($connection) {
                    $this->info("✅ Found: {$host}:465 (SSL)");
                    fclose($connection);
                    $found = true;
                } else {
                    $this->warn("  ❌ {$host} - Not reachable");
                }
            }
        }

        if (!$found) {
            $this->warn('');
            $this->warn('⚠️  Could not automatically detect SMTP server.');
            $this->warn('You may need to check with your hosting provider.');
        }

        $this->info('');
    }

    private function suggestCommonConfigurations(): void
    {
        $this->info('=== Common SMTP Configurations ===');
        $this->info('');

        $configs = [
            [
                'Provider' => 'cPanel / Shared Hosting',
                'Host' => 'mail.hesu.co.tz',
                'Port' => '587',
                'Encryption' => 'tls',
            ],
            [
                'Provider' => 'Microsoft 365',
                'Host' => 'smtp.office365.com',
                'Port' => '587',
                'Encryption' => 'tls',
            ],
            [
                'Provider' => 'Google Workspace',
                'Host' => 'smtp.gmail.com',
                'Port' => '587',
                'Encryption' => 'tls',
            ],
            [
                'Provider' => 'Zoho Mail',
                'Host' => 'smtp.zoho.com',
                'Port' => '587',
                'Encryption' => 'tls',
            ],
        ];

        $this->table(['Provider', 'Host', 'Port', 'Encryption'], $configs);
        $this->info('');
    }

    private function provideInstructions(): void
    {
        $this->info('=== How to Find Your SMTP Settings ===');
        $this->info('');
        $this->line('1. Check your hosting control panel (cPanel, Plesk, etc.)');
        $this->line('   - Look for "Email Accounts" or "Email Settings"');
        $this->line('   - Find "Outgoing Mail Server" or "SMTP Server"');
        $this->line('');
        $this->line('2. Check your email client settings');
        $this->line('   - If you use Outlook/Thunderbird, check their SMTP settings');
        $this->line('   - Settings → Account Settings → Outgoing Server');
        $this->line('');
        $this->line('3. Contact your hosting provider');
        $this->line('   - Ask for SMTP server, port, and encryption settings');
        $this->line('   - They should provide: host, port, encryption type');
        $this->line('');
        $this->line('4. Check your domain\'s DNS records');
        $this->line('   - MX records might indicate mail server');
        $this->line('   - Use: nslookup -type=MX hesu.co.tz');
        $this->info('');
        $this->info('Once you have the SMTP settings, update your .env file:');
        $this->info('');
        $this->line('MAIL_MAILER=smtp');
        $this->line('MAIL_HOST=your-smtp-host-here');
        $this->line('MAIL_PORT=587');
        $this->line('MAIL_USERNAME=noreply@hesu.co.tz');
        $this->line('MAIL_PASSWORD=your-email-password');
        $this->line('MAIL_ENCRYPTION=tls');
        $this->line('MAIL_FROM_ADDRESS=noreply@hesu.co.tz');
        $this->line('MAIL_FROM_NAME="HSE Management System"');
        $this->info('');
        $this->info('Then run: php artisan config:clear');
        $this->info('And test: php artisan smtp:test --email=laurianlawrence@hesu.co.tz');
    }
}

