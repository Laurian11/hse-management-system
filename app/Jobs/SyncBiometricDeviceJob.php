<?php

namespace App\Jobs;

use App\Models\BiometricDevice;
use App\Services\MultiDeviceZKTecoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncBiometricDeviceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 60, 120]; // Retry after 30s, 60s, 120s

    /**
     * Create a new job instance.
     */
    public function __construct(
        public BiometricDevice $device,
        public ?Carbon $date = null
    ) {
        $this->date = $date ?? now();
    }

    /**
     * Execute the job.
     */
    public function handle(MultiDeviceZKTecoService $zkService): void
    {
        try {
            Log::info("Syncing device {$this->device->device_name} (ID: {$this->device->id}) for date {$this->date->format('Y-m-d')}");

            $result = $zkService->syncDailyAttendance($this->device, $this->date);

            Log::info("Device sync completed", [
                'device' => $this->device->device_name,
                'processed' => $result['processed'] ?? 0,
                'new_records' => $result['new_records'] ?? 0,
                'updated_records' => $result['updated_records'] ?? 0,
            ]);

            // Dispatch event for successful sync
            event(new \App\Events\BiometricDeviceSynced($this->device, $result));
        } catch (\Exception $e) {
            Log::error("Failed to sync device {$this->device->device_name}: " . $e->getMessage());
            
            // Dispatch event for failed sync
            event(new \App\Events\BiometricDeviceSyncFailed($this->device, $e->getMessage()));
            
            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job failed after {$this->tries} attempts for device {$this->device->device_name}: " . $exception->getMessage());
        
        // Mark device as offline if all retries failed
        $this->device->update(['status' => 'offline']);
        
        // Dispatch event
        event(new \App\Events\BiometricDeviceSyncFailed($this->device, $exception->getMessage()));
    }
}

