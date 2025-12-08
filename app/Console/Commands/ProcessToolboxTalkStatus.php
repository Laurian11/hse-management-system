<?php

namespace App\Console\Commands;

use App\Models\ToolboxTalk;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ProcessToolboxTalkStatus extends Command
{
    protected $signature = 'toolbox-talks:process-status';
    protected $description = 'Auto-end scheduled talks and mark overdue talks';

    public function handle()
    {
        $this->info('Processing toolbox talk statuses...');
        $this->info('');

        // Auto-end talks that have passed their end time
        $endedCount = $this->autoEndTalks();
        $this->info("✅ Auto-ended {$endedCount} talk(s)");

        // Mark overdue talks (scheduled but past time without attendance/start)
        $overdueCount = $this->markOverdueTalks();
        $this->info("⚠️  Marked {$overdueCount} talk(s) as overdue");

        $this->info('');
        $this->info('Status processing completed!');

        return Command::SUCCESS;
    }

    private function autoEndTalks(): int
    {
        $count = 0;
        
        // Find talks that are in_progress but past their end time
        $talks = ToolboxTalk::where('status', 'in_progress')
            ->whereNotNull('start_time')
            ->whereNotNull('duration_minutes')
            ->get();

        foreach ($talks as $talk) {
            // Calculate end time
            $endTime = $talk->start_time->copy()->addMinutes($talk->duration_minutes);
            
            // If end time has passed, mark as completed
            if ($endTime->isPast()) {
                $talk->update([
                    'status' => 'completed',
                    'end_time' => $endTime,
                ]);
                
                // Recalculate attendance rate
                $talk->calculateAttendanceRate();
                
                $count++;
                $this->line("  - Auto-ended: {$talk->reference_number} - {$talk->title}");
            }
        }

        return $count;
    }

    private function markOverdueTalks(): int
    {
        $count = 0;
        
        // Find scheduled talks that are past their scheduled time
        // and haven't been started or have no attendance
        $talks = ToolboxTalk::where('status', 'scheduled')
            ->where('scheduled_date', '<=', now())
            ->where(function($query) {
                $query->whereNull('start_time')
                      ->orWhere('start_time', '<=', now()->subMinutes(15)); // 15 min grace period
            })
            ->get();

        foreach ($talks as $talk) {
            // Check if talk has any attendance
            $hasAttendance = $talk->attendances()->count() > 0;
            
            // If no attendance and past scheduled time, mark as overdue
            if (!$hasAttendance) {
                $talk->update([
                    'status' => 'overdue',
                ]);
                
                $count++;
                $this->line("  - Marked overdue: {$talk->reference_number} - {$talk->title}");
            }
        }

        return $count;
    }
}
