<?php

namespace App\Console\Commands;

use App\Models\ToolboxTalk;
use App\Models\User;
use App\Models\Department;
use App\Notifications\TalkReminderNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendTalkDayBeforeNotifications extends Command
{
    protected $signature = 'toolbox-talks:send-day-before-notifications';
    protected $description = 'Send notifications to users and HSE officers 1 day before scheduled talks';

    public function handle()
    {
        $this->info('Sending day-before notifications...');
        $this->info('');

        // Find talks scheduled for tomorrow
        $tomorrow = Carbon::tomorrow();
        $talks = ToolboxTalk::where('status', 'scheduled')
            ->whereDate('scheduled_date', $tomorrow)
            ->with(['supervisor', 'department'])
            ->get();

        if ($talks->isEmpty()) {
            $this->info('No talks scheduled for tomorrow.');
            return Command::SUCCESS;
        }

        $this->info("Found {$talks->count()} talk(s) scheduled for tomorrow");
        $this->info('');

        $totalSent = 0;

        foreach ($talks as $talk) {
            $this->line("Processing: {$talk->reference_number} - {$talk->title}");
            
            $sent = 0;

            // Notify supervisor
            if ($talk->supervisor) {
                try {
                    $talk->supervisor->notify(new TalkReminderNotification($talk, '24h'));
                    $sent++;
                    $this->line("  ✓ Notified supervisor: {$talk->supervisor->name}");
                } catch (\Exception $e) {
                    $this->error("  ✗ Failed to notify supervisor: " . $e->getMessage());
                }
            }

            // Notify department employees if department is assigned
            if ($talk->department) {
                $employees = User::where('company_id', $talk->company_id)
                    ->where('is_active', true)
                    ->whereHas('employee', function($q) use ($talk) {
                        $q->where('department_id', $talk->department_id);
                    })
                    ->get();

                foreach ($employees as $employee) {
                    try {
                        $employee->notify(new TalkReminderNotification($talk, '24h'));
                        $sent++;
                    } catch (\Exception $e) {
                        $this->error("  ✗ Failed to notify {$employee->name}: " . $e->getMessage());
                    }
                }
                
                if ($employees->count() > 0) {
                    $this->line("  ✓ Notified {$employees->count()} department employee(s)");
                }
            }

            // Notify HSE officers
            $hseOfficers = $this->getHSEOfficers($talk->company_id, $talk->department_id);
            
            foreach ($hseOfficers as $officer) {
                try {
                    $officer->notify(new TalkReminderNotification($talk, '24h'));
                    $sent++;
                } catch (\Exception $e) {
                    $this->error("  ✗ Failed to notify HSE officer {$officer->name}: " . $e->getMessage());
                }
            }
            
            if ($hseOfficers->count() > 0) {
                $this->line("  ✓ Notified {$hseOfficers->count()} HSE officer(s)");
            }

            $totalSent += $sent;
            $this->line("  Total notifications sent: {$sent}");
            $this->info('');
        }

        $this->info("✅ Total notifications sent: {$totalSent}");
        
        return Command::SUCCESS;
    }

    private function getHSEOfficers($companyId, $departmentId = null): \Illuminate\Database\Eloquent\Collection
    {
        // Get HSE officers by role
        $hseRole = \App\Models\Role::where('name', 'hse_officer')
            ->orWhere('level', 'hse_officer')
            ->first();
        
        $hseOfficers = User::where('company_id', $companyId)
            ->where('is_active', true);
        
        if ($hseRole) {
            $hseOfficers->where('role_id', $hseRole->id);
        } else {
            $hseOfficers->whereHas('role', function($q) {
                $q->where('name', 'hse_officer')
                  ->orWhere('level', 'hse_officer');
            });
        }
        
        $officers = $hseOfficers->get();
        
        // Also get department HSE officer if department is assigned
        if ($departmentId) {
            $department = Department::find($departmentId);
            if ($department && $department->hse_officer_id) {
                $deptOfficer = User::find($department->hse_officer_id);
                if ($deptOfficer && !$officers->contains('id', $deptOfficer->id)) {
                    $officers->push($deptOfficer);
                }
            }
        }
        
        return $officers->unique('id');
    }
}
