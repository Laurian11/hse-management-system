<?php

namespace App\Observers;

use App\Models\SpillIncident;
use App\Models\ActivityLog;
use App\Models\Incident;

class SpillIncidentObserver
{
    /**
     * Handle the SpillIncident "created" event.
     */
    public function created(SpillIncident $spillIncident): void
    {
        // Log activity
        ActivityLog::log(
            $spillIncident->company_id,
            'spill_incident_created',
            "Spill incident {$spillIncident->reference_number} reported",
            $spillIncident->id,
            'App\Models\SpillIncident'
        );

        // If severity is major or catastrophic, create related incident record
        if (in_array($spillIncident->severity, ['major', 'catastrophic'])) {
            $this->createRelatedIncident($spillIncident);
        }
    }

    /**
     * Handle the SpillIncident "updated" event.
     */
    public function updated(SpillIncident $spillIncident): void
    {
        $changes = $spillIncident->getChanges();

        // Log activity
        ActivityLog::log(
            $spillIncident->company_id,
            'spill_incident_updated',
            "Spill incident {$spillIncident->reference_number} updated",
            $spillIncident->id,
            'App\Models\SpillIncident'
        );

        // If status changed to 'closed', log closure
        if (isset($changes['status']) && $changes['status'] === 'closed') {
            ActivityLog::log(
                $spillIncident->company_id,
                'spill_incident_closed',
                "Spill incident {$spillIncident->reference_number} closed",
                $spillIncident->id,
                'App\Models\SpillIncident'
            );
        }
    }

    /**
     * Handle the SpillIncident "deleted" event.
     */
    public function deleted(SpillIncident $spillIncident): void
    {
        ActivityLog::log(
            $spillIncident->company_id,
            'spill_incident_deleted',
            "Spill incident {$spillIncident->reference_number} deleted",
            $spillIncident->id,
            'App\Models\SpillIncident'
        );
    }

    /**
     * Create related incident for major/catastrophic spills
     */
    private function createRelatedIncident(SpillIncident $spillIncident): void
    {
        // Create related incident record for tracking
        // Note: This creates a separate incident record for major spills
        try {
            Incident::create([
                'company_id' => $spillIncident->company_id,
                'incident_type' => 'environmental',
                'event_type' => 'spill',
                'severity' => $spillIncident->severity === 'catastrophic' ? 'critical' : 'major',
                'incident_date' => $spillIncident->incident_date,
                'location' => $spillIncident->location,
                'title' => "Environmental Spill: {$spillIncident->material_spilled}",
                'description' => "Environmental spill incident: {$spillIncident->material_spilled} - {$spillIncident->description}",
                'reported_by' => $spillIncident->reported_by,
                'status' => 'reported',
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the observer
            \Log::warning('Failed to create related incident for spill: ' . $e->getMessage());
        }
    }
}

