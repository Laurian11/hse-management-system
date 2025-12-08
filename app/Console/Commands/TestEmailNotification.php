<?php

namespace App\Console\Commands;

use App\Models\ToolboxTalk;
use App\Models\ToolboxTalkTopic;
use App\Models\User;
use App\Notifications\TalkReminderNotification;
use App\Notifications\TopicCreatedNotification;
use App\Notifications\TestNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailNotification extends Command
{
    protected $signature = 'test:email {type=test} {--email=} {--message=}';
    protected $description = 'Test email notifications (test|topic|talk)';

    public function handle()
    {
        $type = $this->argument('type');
        $email = $this->option('email');
        $message = $this->option('message');

        $this->info("Testing {$type} email notification...");

        if ($type === 'test') {
            $this->testBasicNotification($email, $message);
        } elseif ($type === 'topic') {
            $this->testTopicNotification($email);
        } elseif ($type === 'talk') {
            $this->testTalkNotification($email);
        } else {
            $this->error("Invalid type. Use 'test', 'topic', or 'talk'");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function testBasicNotification(?string $email, ?string $message): void
    {
        $user = null;
        if ($email) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("User with email {$email} not found.");
                return;
            }
        } else {
            $user = User::first();
        }

        if (!$user) {
            $this->error('No users found in the system.');
            return;
        }

        $testMessage = $message ?? 'This is a test notification from the HSE Management System.';

        $this->info("Sending test notification to: {$user->name} ({$user->email})");
        $this->info("Message: {$testMessage}");

        try {
            $user->notify(new TestNotification($testMessage));
            $this->info('✅ Email notification sent successfully!');
            $this->info("Check your email inbox or logs (if using 'log' mailer)");
            $this->info("Log file location: storage/logs/laravel.log");
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    private function testTopicNotification(?string $email): void
    {
        $topic = ToolboxTalkTopic::first();
        
        if (!$topic) {
            $this->error('No topics found. Please create a topic first.');
            return;
        }

        $user = null;
        if ($email) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("User with email {$email} not found.");
                return;
            }
        } else {
            // Find HSE officer or use first user
            $user = User::whereHas('role', function($q) {
                $q->where('name', 'hse_officer')->orWhere('level', 'hse_officer');
            })->first() ?? User::first();
        }

        if (!$user) {
            $this->error('No users found in the system.');
            return;
        }

        $this->info("Sending topic notification to: {$user->name} ({$user->email})");
        $this->info("Topic: {$topic->title}");

        try {
            $user->notify(new TopicCreatedNotification($topic));
            $this->info('✅ Email notification sent successfully!');
            $this->info("Check your email inbox or logs (if using 'log' mailer)");
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
        }
    }

    private function testTalkNotification(?string $email): void
    {
        $talk = ToolboxTalk::first();
        
        if (!$talk) {
            $this->error('No talks found. Please create a talk first.');
            return;
        }

        $user = null;
        if ($email) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("User with email {$email} not found.");
                return;
            }
        } else {
            $user = $talk->supervisor ?? User::first();
        }

        if (!$user) {
            $this->error('No users found in the system.');
            return;
        }

        $this->info("Sending talk reminder to: {$user->name} ({$user->email})");
        $this->info("Talk: {$talk->title}");

        try {
            $user->notify(new TalkReminderNotification($talk, '24h'));
            $this->info('✅ Email notification sent successfully!');
            $this->info("Check your email inbox or logs (if using 'log' mailer)");
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
        }
    }
}
