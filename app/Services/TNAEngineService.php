<?php

namespace App\Services;

use App\Models\TrainingNeedsAnalysis;
use App\Models\ControlMeasure;
use App\Models\RootCauseAnalysis;
use App\Models\CAPA;
use App\Models\Incident;
use App\Models\RiskAssessment;
use App\Models\User;
use App\Models\JobCompetencyMatrix;
use Illuminate\Support\Facades\Log;

class TNAEngineService
{
    /**
     * Process trigger from Risk Assessment - Control Measure
     * When a control measure is marked as "Administrative Control", auto-create training need
     */
    public function processControlMeasureTrigger(ControlMeasure $controlMeasure): ?TrainingNeedsAnalysis
    {
        // Only process if it's an administrative control
        if ($controlMeasure->control_type !== 'administrative') {
            return null;
        }

        // Check if training need already exists
        if ($controlMeasure->related_training_need_id) {
            return TrainingNeedsAnalysis::find($controlMeasure->related_training_need_id);
        }

        // Determine priority based on risk assessment
        $priority = 'medium';
        if ($controlMeasure->riskAssessment) {
            $riskLevel = $controlMeasure->riskAssessment->risk_level;
            $priority = match($riskLevel) {
                'extreme', 'critical' => 'critical',
                'high' => 'high',
                default => 'medium',
            };
        }

        // Create training need
        $tna = TrainingNeedsAnalysis::create([
            'company_id' => $controlMeasure->company_id,
            'trigger_source' => 'risk_assessment',
            'triggered_by_risk_assessment_id' => $controlMeasure->risk_assessment_id,
            'triggered_by_control_measure_id' => $controlMeasure->id,
            'training_title' => "Training for: {$controlMeasure->title}",
            'training_description' => "Administrative control training required for: {$controlMeasure->description}",
            'gap_analysis' => "New administrative control measure requires training to ensure proper implementation.",
            'priority' => $priority,
            'training_type' => 'classroom',
            'target_departments' => $controlMeasure->department_id ? [$controlMeasure->department_id] : null,
            'is_mandatory' => true,
            'status' => 'identified',
            'created_by' => auth()->id() ?? 1,
        ]);

        // Link back to control measure
        $controlMeasure->update([
            'related_training_need_id' => $tna->id,
            'training_required' => true,
        ]);

        Log::info("TNA Engine: Created training need from control measure", [
            'control_measure_id' => $controlMeasure->id,
            'tna_id' => $tna->id,
        ]);

        return $tna;
    }

    /**
     * Process trigger from Incident RCA
     * When RCA identifies training gap, auto-create training need
     */
    public function processRCATrigger(RootCauseAnalysis $rca): ?TrainingNeedsAnalysis
    {
        // Check if training gap was identified
        if (!$rca->training_gap_identified) {
            return null;
        }

        // Check if training need already exists
        $existingTna = TrainingNeedsAnalysis::where('triggered_by_rca_id', $rca->id)->first();
        if ($existingTna) {
            return $existingTna;
        }

        // Determine priority based on incident severity
        $priority = 'high';
        if ($rca->incident) {
            $priority = match($rca->incident->severity) {
                'critical' => 'critical',
                'high' => 'high',
                default => 'medium',
            };
        }

        // Create training need
        $tna = TrainingNeedsAnalysis::create([
            'company_id' => $rca->company_id,
            'trigger_source' => 'incident_rca',
            'triggered_by_incident_id' => $rca->incident_id,
            'triggered_by_rca_id' => $rca->id,
            'training_title' => "Training Gap Identified: " . ($rca->training_gap_description ?? 'Training Required'),
            'training_description' => $rca->training_gap_description ?? "Training gap identified during root cause analysis.",
            'gap_analysis' => $rca->training_gap_description,
            'priority' => $priority,
            'training_type' => 'classroom',
            'target_departments' => $rca->incident->department_id ? [$rca->incident->department_id] : null,
            'is_mandatory' => true,
            'status' => 'identified',
            'created_by' => auth()->id() ?? 1,
        ]);

        Log::info("TNA Engine: Created training need from RCA", [
            'rca_id' => $rca->id,
            'incident_id' => $rca->incident_id,
            'tna_id' => $tna->id,
        ]);

        return $tna;
    }

    /**
     * Process trigger from CAPA
     * When CAPA action type is "Provide Training", auto-create training need
     */
    public function processCAPATrigger(CAPA $capa): ?TrainingNeedsAnalysis
    {
        // Check if this CAPA is for training
        $isTrainingCAPA = stripos($capa->title, 'training') !== false ||
                          stripos($capa->description, 'training') !== false ||
                          stripos($capa->description, 'train') !== false;

        if (!$isTrainingCAPA) {
            return null;
        }

        // Check if training need already exists
        if ($capa->related_training_need_id) {
            return TrainingNeedsAnalysis::find($capa->related_training_need_id);
        }

        // Create training need
        $tna = TrainingNeedsAnalysis::create([
            'company_id' => $capa->company_id,
            'trigger_source' => 'incident_rca', // CAPA comes from incident/RCA
            'triggered_by_incident_id' => $capa->incident_id,
            'triggered_by_rca_id' => $capa->root_cause_analysis_id,
            'triggered_by_capa_id' => $capa->id,
            'training_title' => $capa->title,
            'training_description' => $capa->description,
            'gap_analysis' => "Training required as part of CAPA: {$capa->reference_number}",
            'priority' => $capa->priority,
            'training_type' => 'classroom',
            'target_departments' => $capa->department_id ? [$capa->department_id] : null,
            'target_user_ids' => $capa->assigned_to ? [$capa->assigned_to] : null,
            'is_mandatory' => true,
            'status' => 'identified',
            'created_by' => auth()->id() ?? 1,
        ]);

        // Link back to CAPA
        $capa->update([
            'related_training_need_id' => $tna->id,
        ]);

        Log::info("TNA Engine: Created training need from CAPA", [
            'capa_id' => $capa->id,
            'tna_id' => $tna->id,
        ]);

        return $tna;
    }

    /**
     * Process trigger from New Hire / Job Role Change
     * When user is hired or changes role, check competency matrix and create training needs
     */
    public function processUserJobChangeTrigger(User $user, ?JobCompetencyMatrix $oldMatrix = null): array
    {
        $createdTnas = [];

        // Get current job competency matrix
        $matrix = $user->jobCompetencyMatrix;
        if (!$matrix || !$matrix->isActive()) {
            return $createdTnas;
        }

        // Get mandatory trainings from matrix
        $mandatoryTrainings = $matrix->mandatory_trainings ?? [];
        if (empty($mandatoryTrainings)) {
            return $createdTnas;
        }

        // Check if user already has these trainings
        $userTrainingRecords = $user->trainingRecords()
            ->where('status', 'completed')
            ->pluck('training_title')
            ->toArray();

        // Create training needs for missing mandatory trainings
        foreach ($mandatoryTrainings as $trainingId) {
            // Check if training need already exists for this user and training
            $existingTna = TrainingNeedsAnalysis::where('triggered_by_user_id', $user->id)
                ->where('trigger_source', 'new_hire')
                ->whereJsonContains('target_user_ids', $user->id)
                ->first();

            if ($existingTna) {
                continue;
            }

            $tna = TrainingNeedsAnalysis::create([
                'company_id' => $user->company_id,
                'trigger_source' => $oldMatrix ? 'job_role_change' : 'new_hire',
                'triggered_by_user_id' => $user->id,
                'triggered_by_job_matrix_id' => $matrix->id,
                'training_title' => "Mandatory Training for {$user->job_title}",
                'training_description' => "Required training based on job competency matrix: {$matrix->job_title}",
                'gap_analysis' => "New hire or role change requires completion of mandatory trainings.",
                'priority' => 'high',
                'training_type' => 'classroom',
                'target_user_ids' => [$user->id],
                'target_departments' => $user->department_id ? [$user->department_id] : null,
                'is_mandatory' => true,
                'status' => 'identified',
                'created_by' => auth()->id() ?? 1,
            ]);

            $createdTnas[] = $tna;
        }

        Log::info("TNA Engine: Created training needs for user job change", [
            'user_id' => $user->id,
            'matrix_id' => $matrix->id,
            'tna_count' => count($createdTnas),
        ]);

        return $createdTnas;
    }

    /**
     * Process trigger from Certificate Expiry
     * When certificate is about to expire, create refresher training need
     */
    public function processCertificateExpiryTrigger($certificate): ?TrainingNeedsAnalysis
    {
        // Check if training need already exists
        $existingTna = TrainingNeedsAnalysis::where('trigger_source', 'certificate_expiry')
            ->whereJsonContains('target_user_ids', $certificate->user_id)
            ->where('status', '!=', 'completed')
            ->first();

        if ($existingTna) {
            return $existingTna;
        }

        $daysUntilExpiry = $certificate->daysUntilExpiry();
        if ($daysUntilExpiry === null || $daysUntilExpiry > 60) {
            return null; // Only create if expiring within 60 days
        }

        // Create refresher training need
        $tna = TrainingNeedsAnalysis::create([
            'company_id' => $certificate->company_id,
            'trigger_source' => 'certificate_expiry',
            'triggered_by_user_id' => $certificate->user_id,
            'training_title' => "Refresher Training: {$certificate->certificate_title}",
            'training_description' => "Certificate expiring soon. Refresher training required to maintain competency.",
            'gap_analysis' => "Certificate {$certificate->certificate_number} expires on {$certificate->expiry_date->format('Y-m-d')}",
            'priority' => $daysUntilExpiry <= 30 ? 'critical' : 'high',
            'training_type' => 'refresher',
            'target_user_ids' => [$certificate->user_id],
            'is_mandatory' => true,
            'status' => 'identified',
            'created_by' => auth()->id() ?? 1,
        ]);

        Log::info("TNA Engine: Created refresher training need from certificate expiry", [
            'certificate_id' => $certificate->id,
            'user_id' => $certificate->user_id,
            'tna_id' => $tna->id,
        ]);

        return $tna;
    }

    /**
     * Validate and prioritize training needs
     */
    public function validateAndPrioritize(TrainingNeedsAnalysis $tna, User $validator, string $notes = null): bool
    {
        return $tna->validate($validator, $notes);
    }

    /**
     * Consolidate multiple training needs into a single plan
     */
    public function consolidateTrainingNeeds(array $tnaIds, array $options = []): ?TrainingPlan
    {
        $tnas = TrainingNeedsAnalysis::whereIn('id', $tnaIds)->get();
        if ($tnas->isEmpty()) {
            return null;
        }

        // Get common attributes
        $companyId = $tnas->first()->company_id;
        $trainingType = $options['training_type'] ?? 'classroom';
        $priority = $tnas->max('priority'); // Use highest priority

        // Combine target audiences
        $targetUserIds = [];
        $targetDepartments = [];
        foreach ($tnas as $tna) {
            if ($tna->target_user_ids) {
                $targetUserIds = array_merge($targetUserIds, $tna->target_user_ids);
            }
            if ($tna->target_departments) {
                $targetDepartments = array_merge($targetDepartments, $tna->target_departments);
            }
        }
        $targetUserIds = array_unique($targetUserIds);
        $targetDepartments = array_unique($targetDepartments);

        // Create consolidated training plan
        $plan = \App\Models\TrainingPlan::create([
            'company_id' => $companyId,
            'training_need_id' => $tnas->first()->id, // Link to first TNA
            'title' => $options['title'] ?? 'Consolidated Training Plan',
            'description' => $options['description'] ?? 'Training plan consolidating multiple training needs.',
            'training_type' => $trainingType,
            'status' => 'draft',
            'created_by' => auth()->id() ?? 1,
        ]);

        // Update TNA statuses
        foreach ($tnas as $tna) {
            $tna->update([
                'status' => 'planned',
            ]);
        }

        return $plan;
    }
}
