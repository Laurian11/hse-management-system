<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MultiDeviceZKTecoService;
use App\Models\BiometricDevice;
use Carbon\Carbon;

class SyncDailyAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:sync-daily {--date=} {--device=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync daily attendance from all biometric devices';

    protected $zkService;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->zkService = new MultiDeviceZKTecoService();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') 
            ? Carbon::parse($this->option('date')) 
            : now();
        
        $deviceId = $this->option('device');
        
        if ($deviceId) {
            // Sync specific device
            $device = BiometricDevice::find($deviceId);
            
            if (!$device) {
                $this->error("Device with ID {$deviceId} not found.");
                return 1;
            }
            
            $this->info("Syncing attendance for device: {$device->device_name} ({$device->location_name})");
            
            $results = $this->zkService->syncDailyAttendance($device, $date);
            
            $this->info("Processed: {$results['processed']}");
            $this->info("New records: {$results['new_records']}");
            $this->info("Updated records: {$results['updated_records']}");
            
            if (!empty($results['errors'])) {
                $this->warn("Errors: " . count($results['errors']));
                foreach ($results['errors'] as $error) {
                    $this->error("  - {$error}");
                }
            }
        } else {
            // Sync all devices
            $this->info("Syncing daily attendance from all devices for date: {$date->format('Y-m-d')}");
            
            $results = $this->zkService->syncAllDevices($date);
            
            $this->info("Devices processed: {$results['devices_processed']}");
            $this->info("Devices successful: {$results['devices_successful']}");
            $this->info("Devices failed: {$results['devices_failed']}");
            $this->info("Total new records: {$results['total_new_records']}");
            $this->info("Total updated records: {$results['total_updated_records']}");
            
            foreach ($results['device_results'] as $deviceName => $deviceResult) {
                if (isset($deviceResult['error'])) {
                    $this->error("{$deviceName}: {$deviceResult['error']}");
                } else {
                    $this->info("{$deviceName}: {$deviceResult['new_records']} new, {$deviceResult['updated_records']} updated");
                }
            }
        }
        
        $this->info("Sync completed!");
        return 0;
    }
}
