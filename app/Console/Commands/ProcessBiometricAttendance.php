<?php

namespace App\Console\Commands;

use App\Services\ZKTecoService;
use App\Models\ToolboxTalk;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ProcessBiometricAttendance extends Command
{
    protected $signature = 'biometric:process-attendance 
                            {--talk= : Specific toolbox talk ID to process}
                            {--all : Process all active talks}
                            {--date= : Process talks for specific date (Y-m-d)}';
    
    protected $description = 'Process biometric attendance for toolbox talks';

    public function handle()
    {
        $this->info('=== Processing Biometric Attendance ===');
        $this->info('');

        $zkService = new ZKTecoService();

        // Test device connection
        $connectionTest = $zkService->testConnection();
        if (!$connectionTest['connected']) {
            $this->warn('⚠️  Device connection failed, but continuing...');
            $this->warn('Error: ' . ($connectionTest['error'] ?? 'Unknown'));
            $this->info('');
        } else {
            $this->info('✅ Device connected');
            $this->info('');
        }

        if ($this->option('talk')) {
            // Process specific talk
            $talk = ToolboxTalk::find($this->option('talk'));
            if (!$talk) {
                $this->error("Toolbox talk not found: {$this->option('talk')}");
                return Command::FAILURE;
            }

            if (!$talk->biometric_required) {
                $this->warn("⚠️  Biometric not required for this talk");
                return Command::SUCCESS;
            }

            $this->processTalk($zkService, $talk);
        } elseif ($this->option('all')) {
            // Process all active talks
            $query = ToolboxTalk::where('biometric_required', true)
                ->whereDate('scheduled_date', '<=', now());

            if ($this->option('date')) {
                $date = Carbon::parse($this->option('date'));
                $query->whereDate('scheduled_date', $date);
            } else {
                // Default: talks happening today or recently
                $query->where(function($q) {
                    $q->where('start_time', '<=', now())
                      ->where(function($sq) {
                          $sq->where('end_time', '>=', now())
                            ->orWhere('end_time', '>=', now()->subHours(2));
                      });
                });
            }

            $talks = $query->get();

            if ($talks->isEmpty()) {
                $this->warn('No toolbox talks found to process.');
                return Command::SUCCESS;
            }

            $this->info("Found {$talks->count()} talk(s) to process");
            $this->info('');

            $totalProcessed = 0;
            $totalNew = 0;

            foreach ($talks as $talk) {
                $result = $this->processTalk($zkService, $talk);
                if ($result) {
                    $totalProcessed += $result['processed'];
                    $totalNew += $result['new_attendances'];
                }
            }

            $this->info('');
            $this->info('=== Summary ===');
            $this->info("Total logs processed: {$totalProcessed}");
            $this->info("New attendances: {$totalNew}");
        } else {
            $this->error('Please specify --talk=ID or --all');
            $this->info('');
            $this->info('Examples:');
            $this->info('  php artisan biometric:process-attendance --talk=1');
            $this->info('  php artisan biometric:process-attendance --all');
            $this->info('  php artisan biometric:process-attendance --all --date=2025-12-08');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function processTalk(ZKTecoService $zkService, ToolboxTalk $talk): ?array
    {
        $this->info("Processing: {$talk->title}");
        $this->info("Reference: {$talk->reference_number}");
        $this->info("Scheduled: {$talk->scheduled_date->format('Y-m-d')} at {$talk->start_time->format('H:i')}");
        $this->info('');

        try {
            $results = $zkService->processToolboxTalkAttendance($talk);

            $this->info("✅ Processed: {$results['processed']} log(s)");
            $this->info("✅ New attendances: {$results['new_attendances']}");

            if (!empty($results['errors'])) {
                $this->warn("⚠️  Errors: " . count($results['errors']));
                foreach ($results['errors'] as $error) {
                    $this->warn("  - {$error}");
                }
            }

            $this->info('');

            return $results;
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->info('');
            return null;
        }
    }
}

