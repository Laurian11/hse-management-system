<?php

namespace App\Http\Controllers;

use App\Models\TrainingNeedsAnalysis;
use App\Models\ControlMeasure;
use App\Models\RootCauseAnalysis;
use App\Models\CAPA;
use App\Services\TNAEngineService;
use App\Models\ActivityLog;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingNeedsAnalysisController extends Controller
{
    protected $tnaEngine;

    public function __construct(TNAEngineService $tnaEngine)
    {
        $this->tnaEngine = $tnaEngine;
    }

    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = TrainingNeedsAnalysis::where('company_id', $companyId)
            ->with(['creator', 'validator', 'trainingPlans']);
        
        // Apply filters
        if ($request->has('status')) {
            $query->byStatus($request->get('status'));
        }
        
        if ($request->has('priority')) {
            $query->byPriority($request->get('priority'));
        }
        
        if ($request->has('trigger_source')) {
            $query->byTriggerSource($request->get('trigger_source'));
        }
        
        if ($request->has('mandatory')) {
            $query->mandatory();
        }
        
        $tnas = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('training.training-needs.index', compact('tnas'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $users = \App\Models\User::where('company_id', $companyId)->get();
        $departments = \App\Models\Department::where('company_id', $companyId)->get();
        
        // Pre-fill from trigger if provided
        $triggerSource = $request->get('trigger_source');
        $triggerId = $request->get('trigger_id');
        
        return view('training.training-needs.create', compact('users', 'departments', 'triggerSource', 'triggerId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'training_title' => 'required|string|max:255',
            'training_description' => 'required|string',
            'gap_analysis' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,critical',
            'training_type' => 'required|in:classroom,e_learning,on_job_training,workshop,simulation,refresher,certification,combination',
            'target_departments' => 'nullable|array',
            'target_job_roles' => 'nullable|array',
            'target_user_ids' => 'nullable|array',
            'is_mandatory' => 'boolean',
            'is_regulatory' => 'boolean',
            'regulatory_reference' => 'nullable|string',
            'regulatory_deadline' => 'nullable|date',
            'trigger_source' => 'required|in:risk_assessment,incident_rca,new_hire,job_role_change,audit_finding,legal_register,competency_gap,certificate_expiry,manual',
            'triggered_by_risk_assessment_id' => 'nullable|exists:risk_assessments,id',
            'triggered_by_control_measure_id' => 'nullable|exists:control_measures,id',
            'triggered_by_incident_id' => 'nullable|exists:incidents,id',
            'triggered_by_rca_id' => 'nullable|exists:root_cause_analyses,id',
            'triggered_by_capa_id' => 'nullable|exists:capas,id',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $validated['created_by'] = Auth::user()->id;
        $validated['status'] = 'identified';
        
        $tna = TrainingNeedsAnalysis::create($validated);
        
        return redirect()->route('training.training-needs.show', $tna)
            ->with('success', 'Training need identified successfully.');
    }

    public function show(TrainingNeedsAnalysis $trainingNeedsAnalysis)
    {
        if ($trainingNeedsAnalysis->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $trainingNeedsAnalysis->load([
            'creator',
            'validator',
            'trainingPlans',
            'triggeredByRiskAssessment',
            'triggeredByControlMeasure',
            'triggeredByIncident',
            'triggeredByRCA',
            'triggeredByCAPA',
        ]);

        return view('training.training-needs.show', compact('trainingNeedsAnalysis'));
    }

    public function validateTNA(Request $request, TrainingNeedsAnalysis $trainingNeedsAnalysis)
    {
        if ($trainingNeedsAnalysis->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'validation_notes' => 'nullable|string',
        ]);

        $this->tnaEngine->validateAndPrioritize(
            $trainingNeedsAnalysis,
            Auth::user(),
            $validated['validation_notes'] ?? null
        );

        return redirect()->route('training.training-needs.show', $trainingNeedsAnalysis)
            ->with('success', 'Training need validated successfully.');
    }

    public function createFromControlMeasure(ControlMeasure $controlMeasure)
    {
        if ($controlMeasure->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $tna = $this->tnaEngine->processControlMeasureTrigger($controlMeasure);
        
        return redirect()->route('training.training-needs.show', $tna)
            ->with('success', 'Training need created from control measure.');
    }

    public function createFromRCA(RootCauseAnalysis $rootCauseAnalysis)
    {
        if ($rootCauseAnalysis->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $tna = $this->tnaEngine->processRCATrigger($rootCauseAnalysis);
        
        if ($tna) {
            return redirect()->route('training.training-needs.show', $tna)
                ->with('success', 'Training need created from RCA.');
        }

        return redirect()->back()
            ->with('error', 'No training gap identified in this RCA.');
    }

    public function createFromCAPA(CAPA $capa)
    {
        if ($capa->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $tna = $this->tnaEngine->processCAPATrigger($capa);
        
        if ($tna) {
            return redirect()->route('training.training-needs.show', $tna)
                ->with('success', 'Training need created from CAPA.');
        }

        return redirect()->back()
            ->with('error', 'This CAPA does not require training.');
    }

    public function edit(TrainingNeedsAnalysis $trainingNeedsAnalysis)
    {
        if ($trainingNeedsAnalysis->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $companyId = Auth::user()->company_id;
        $users = \App\Models\User::where('company_id', $companyId)->get();
        $departments = \App\Models\Department::where('company_id', $companyId)->get();

        return view('training.training-needs.edit', compact('trainingNeedsAnalysis', 'users', 'departments'));
    }

    public function update(Request $request, TrainingNeedsAnalysis $trainingNeedsAnalysis)
    {
        if ($trainingNeedsAnalysis->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'training_title' => 'required|string|max:255',
            'training_description' => 'required|string',
            'gap_analysis' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,critical',
            'training_type' => 'required|in:classroom,e_learning,on_job_training,workshop,simulation,refresher,certification,combination',
            'target_departments' => 'nullable|array',
            'target_job_roles' => 'nullable|array',
            'target_user_ids' => 'nullable|array',
            'is_mandatory' => 'boolean',
            'is_regulatory' => 'boolean',
            'regulatory_reference' => 'nullable|string',
            'regulatory_deadline' => 'nullable|date',
        ]);

        $trainingNeedsAnalysis->update($validated);

        return redirect()->route('training.training-needs.show', $trainingNeedsAnalysis)
            ->with('success', 'Training need updated successfully.');
    }

    public function destroy(TrainingNeedsAnalysis $trainingNeedsAnalysis)
    {
        if ($trainingNeedsAnalysis->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        // Check if training plans exist
        if ($trainingNeedsAnalysis->trainingPlans->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete training need with existing training plans. Please delete plans first.');
        }

        $trainingNeedsAnalysis->delete();

        return redirect()->route('training.training-needs.index')
            ->with('success', 'Training need deleted successfully.');
    }

    public function export(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = TrainingNeedsAnalysis::where('company_id', $companyId)
            ->with(['creator', 'validator', 'trainingPlans']);
        
        // Apply same filters as index
        if ($request->has('status')) {
            $query->byStatus($request->get('status'));
        }
        if ($request->has('priority')) {
            $query->byPriority($request->get('priority'));
        }
        if ($request->has('trigger_source')) {
            $query->byTriggerSource($request->get('trigger_source'));
        }

        $tnas = $query->orderBy('created_at', 'desc')->get();

        ActivityLog::log('export', 'training', 'TrainingNeedsAnalysis', null, 'Exported training needs list');

        $format = $request->get('format', 'csv'); // csv or excel

        if ($format === 'excel') {
            $data = $tnas->map(function($tna) {
                return [
                    'Reference Number' => $tna->reference_number,
                    'Training Title' => $tna->training_title,
                    'Description' => $tna->training_description,
                    'Priority' => ucfirst($tna->priority),
                    'Training Type' => ucfirst(str_replace('_', ' ', $tna->training_type)),
                    'Status' => ucfirst($tna->status),
                    'Trigger Source' => ucfirst(str_replace('_', ' ', $tna->trigger_source)),
                    'Is Mandatory' => $tna->is_mandatory ? 'Yes' : 'No',
                    'Is Regulatory' => $tna->is_regulatory ? 'Yes' : 'No',
                    'Created By' => $tna->creator->name ?? 'N/A',
                    'Validated By' => $tna->validator->name ?? 'N/A',
                    'Validated At' => $tna->validated_at ? $tna->validated_at->format('Y-m-d H:i:s') : 'N/A',
                    'Created At' => $tna->created_at->format('Y-m-d H:i:s'),
                ];
            });

            $exportData = $data->toArray();
            array_unshift($exportData, ['Training Needs Analysis Export']);
            array_unshift($exportData, ['Generated: ' . now()->format('Y-m-d H:i:s')]);
            array_unshift($exportData, []);

            return Excel::create('training-needs-export-' . date('Y-m-d_His'), function($excel) use ($exportData) {
                $excel->sheet('Training Needs', function($sheet) use ($exportData) {
                    $sheet->fromArray($exportData, null, 'A1', false, false);
                });
            })->export('xlsx');
        } else {
            // CSV Export
            $filename = 'training_needs_export_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($tnas) {
                $file = fopen('php://output', 'w');
                
                // Headers
                fputcsv($file, [
                    'Reference Number', 'Training Title', 'Description', 'Priority', 
                    'Training Type', 'Status', 'Trigger Source', 'Is Mandatory', 
                    'Is Regulatory', 'Created By', 'Validated By', 'Validated At', 'Created At'
                ]);
                
                // Data
                foreach ($tnas as $tna) {
                    fputcsv($file, [
                        $tna->reference_number,
                        $tna->training_title,
                        $tna->training_description,
                        ucfirst($tna->priority),
                        ucfirst(str_replace('_', ' ', $tna->training_type)),
                        ucfirst($tna->status),
                        ucfirst(str_replace('_', ' ', $tna->trigger_source)),
                        $tna->is_mandatory ? 'Yes' : 'No',
                        $tna->is_regulatory ? 'Yes' : 'No',
                        $tna->creator->name ?? 'N/A',
                        $tna->validator->name ?? 'N/A',
                        $tna->validated_at ? $tna->validated_at->format('Y-m-d H:i:s') : 'N/A',
                        $tna->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }
}
