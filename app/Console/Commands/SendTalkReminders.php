<?php

namespace App\Console\Commands;

use App\Models\ToolboxTalk;
use App\Models\User;
use App\Notifications\TalkReminderNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendTalkReminders extends Command
{
    protected $signature = 'talks:send-reminders {--type=24h}';
    protected $description = 'Send email reminders for upcoming toolbox talks';

    public function handle()
    {
        $type = $this->option('type'); // 24h, 1h
        
        $timeRange = match($type) {
            '24h' => [Carbon::now()->addDay()->startOfDay(), Carbon::now()->addDay()->endOfDay()],
            '1h' => [Carbon::now()->addHour()->startOfHour(), Carbon::now()->addHour()->endOfHour()],
            default => [Carbon::now()->addDay()->startOfDay(), Carbon::now()->addDay()->endOfDay()],
        };

        $talks = ToolboxTalk::where('status', 'scheduled')
            ->whereBetween('scheduled_date', $timeRange)
            ->with(['supervisor', 'department'])
            ->get();

        $this->info("Found {$talks->count()} talks to send reminders for ({$type} reminder)");

        $sentCount = 0;
        foreach ($talks as $talk) {
            // Send to supervisor
            if ($talk->supervisor) {
                $talk->supervisor->notify(new TalkReminderNotification($talk, $type));
                $sentCount++;
            }

            // Send to department employees (if department is set)
            if ($talk->department_id) {
                $employees = User::where('company_id', $talk->company_id)
                    ->whereHas('departments', function($q) use ($talk) {
                        $q->where('departments.id', $talk->department_id);
                    })
                    ->orWhere('department_id', $talk->department_id)
                    ->get();

                foreach ($employees as $employee) {
                    if ($employee->id !== $talk->supervisor_id) {
                        $employee->notify(new TalkReminderNotification($talk, $type));
                        $sentCount++;
                    }
                }
            }
        }

        $this->info("Sent {$sentCount} reminder notifications");
        return Command::SUCCESS;
    }
}
