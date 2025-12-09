<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Employee;

class SyncUserDepartments extends Command
{
    protected $signature = 'users:sync-departments';
    protected $description = 'Sync user department_id with employee department_id';

    public function handle()
    {
        $this->info('Syncing user departments with employee departments...');
        
        $users = User::whereNotNull('company_id')->get();
        $synced = 0;
        $skipped = 0;
        
        foreach ($users as $user) {
            $employee = Employee::where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();
            
            if ($employee && $employee->department_id) {
                if ($user->department_id !== $employee->department_id) {
                    $user->update(['department_id' => $employee->department_id]);
                    $deptName = $employee->department ? $employee->department->name : 'N/A';
                    $this->line("✓ Synced {$user->name} (ID: {$user->id}) - Department: {$deptName}");
                    $synced++;
                } else {
                    $skipped++;
                }
            } else {
                $skipped++;
            }
        }
        
        $this->info("\n✓ Sync complete!");
        $this->info("  Synced: {$synced} users");
        $this->info("  Skipped: {$skipped} users");
        
        return 0;
    }
}
