<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class TestSmtpConnection extends Command
{
    protected $signature = 'smtp:test {--email=}';
    protected $description = 'Test SMTP connection and send a test email';

    public function handle()
    {
        $this->info('=== Testing SMTP Configuration ===');
        $this->info('');

        // Display current configuration
        $this->displayConfiguration();

        // Test connection
        $this->testConnection();

        // Send test email if email provided
        $email = $this->option('email');
        if ($email) {
            $this->sendTestEmail($email);
        } else {
            $this->warn('No email provided. Use --email=your@email.com to send a test email.');
        }

        return Command::SUCCESS;
    }

    private function displayConfiguration(): void
    {
        $this->info('Current Mail Configuration:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['MAIL_MAILER', config('mail.default')],
                ['MAIL_HOST', config('mail.mailers.smtp.host')],
                ['MAIL_PORT', config('mail.mailers.smtp.port')],
                ['MAIL_USERNAME', config('mail.mailers.smtp.username') ?: '(not set)'],
                ['MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption') ?: '(not set)'],
                ['MAIL_FROM_ADDRESS', config('mail.from.address')],
                ['MAIL_FROM_NAME', config('mail.from.name')],
            ]
        );
        $this->info('');
    }

    private function testConnection(): void
    {
        $this->info('Testing SMTP connection...');

        try {
            $host = config('mail.mailers.smtp.host');
            $port = config('mail.mailers.smtp.port');

            if (!$host || $host === '127.0.0.1') {
                $this->error('❌ SMTP host is not configured properly.');
                $this->warn('Please update your .env file with correct SMTP settings.');
                return;
            }

            $this->info("Attempting to connect to: {$host}:{$port}");

            // Try to connect
            $connection = @fsockopen($host, $port, $errno, $errstr, 10);

            if ($connection) {
                $this->info("✅ Successfully connected to {$host}:{$port}");
                fclose($connection);
            } else {
                $this->warn("⚠️  Could not connect to {$host}:{$port}");
                $this->warn("Error: {$errstr} (Code: {$errno})");
                $this->warn("This might be normal if your firewall blocks the connection.");
                $this->warn("The actual email sending might still work.");
            }
        } catch (\Exception $e) {
            $this->warn("⚠️  Connection test failed: " . $e->getMessage());
            $this->warn("This doesn't necessarily mean SMTP won't work.");
        }

        $this->info('');
    }

    private function sendTestEmail(string $email): void
    {
        $this->info("Sending test email to: {$email}");

        try {
            Mail::raw('This is a test email from the HSE Management System. If you receive this, your SMTP configuration is working correctly!', function ($message) use ($email) {
                $message->to($email)
                    ->subject('HSE Management System - SMTP Test');
            });

            $this->info('✅ Test email sent successfully!');
            $this->info("Please check the inbox (and spam folder) for: {$email}");
        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email: ' . $e->getMessage());
            $this->error('');
            $this->error('Common issues:');
            $this->error('1. Incorrect SMTP credentials');
            $this->error('2. Wrong port or encryption settings');
            $this->error('3. Firewall blocking SMTP connection');
            $this->error('4. Email account not active or suspended');
            $this->error('');
            $this->error('Check storage/logs/laravel.log for detailed error messages.');
        }
    }
}

