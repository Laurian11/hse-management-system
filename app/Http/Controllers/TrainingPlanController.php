<?php

namespace App\Http\Controllers;

use App\Models\TrainingPlan;
use App\Models\TrainingNeedsAnalysis;
use App\Models\ActivityLog;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingPlanController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = TrainingPlan::where('company_id', $companyId)
            ->with(['trainingNeed', 'instructor', 'creator']);
        
        if ($request->has('status')) {
            $query->byStatus($request->get('status'));
        }
        
        $plans = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('training.training-plans.index', compact('plans'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $trainingNeedId = $request->get('training_need_id');
        
        $trainingNeed = $trainingNeedId 
            ? TrainingNeedsAnalysis::where('company_id', $companyId)->findOrFail($trainingNeedId)
            : null;
        
        $users = \App\Models\User::where('company_id', $companyId)->get();
        $materials = \App\Models\TrainingMaterial::where('company_id', $companyId)->active()->get();
        
        return view('training.training-plans.create', compact('trainingNeed', 'users', 'materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'training_need_id' => 'required|exists:training_needs_analyses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'training_type' => 'required|in:classroom,e_learning,on_job_training,workshop,simulation,refresher,certification,combination',
            'planned_start_date' => 'nullable|date',
            'planned_end_date' => 'nullable|date|after_or_equal:planned_start_date',
            'duration_hours' => 'nullable|integer|min:1',
            'duration_days' => 'nullable|integer|min:1',
            'delivery_method' => 'required|in:internal,external,mixed',
            'instructor_id' => 'nullable|exists:users,id',
            'external_instructor_name' => 'nullable|string|max:255',
            'external_instructor_qualifications' => 'nullable|string',
            'location_name' => 'nullable|string|max:255',
            'location_address' => 'nullable|string',
            'training_materials' => 'nullable|array',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $validated['created_by'] = Auth::user()->id;
        $validated['status'] = 'draft';
        
        $plan = TrainingPlan::create($validated);
        
        // Update TNA status
        $trainingNeed = TrainingNeedsAnalysis::find($validated['training_need_id']);
        $trainingNeed->update(['status' => 'planned']);
        
        return redirect()->route('training.training-plans.show', $plan)
            ->with('success', 'Training plan created successfully.');
    }

    public function show(TrainingPlan $trainingPlan)
    {
        if ($trainingPlan->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $trainingPlan->load([
            'trainingNeed',
            'instructor',
            'creator',
            'approver',
            'sessions',
            'effectivenessEvaluations',
        ]);

        return view('training.training-plans.show', compact('trainingPlan'));
    }

    public function approve(Request $request, TrainingPlan $trainingPlan)
    {
        if ($trainingPlan->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $trainingPlan->approve(Auth::user());
        
        return redirect()->route('training.training-plans.show', $trainingPlan)
            ->with('success', 'Training plan approved successfully.');
    }

    public function approveBudget(Request $request, TrainingPlan $trainingPlan)
    {
        if ($trainingPlan->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $trainingPlan->approveBudget(Auth::user());
        
        return redirect()->route('training.training-plans.show', $trainingPlan)
            ->with('success', 'Budget approved successfully.');
    }

    public function edit(TrainingPlan $trainingPlan)
    {
        if ($trainingPlan->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $companyId = Auth::user()->company_id;
        $users = \App\Models\User::where('company_id', $companyId)->get();
        $materials = \App\Models\TrainingMaterial::where('company_id', $companyId)->active()->get();
        $trainingNeeds = \App\Models\TrainingNeedsAnalysis::where('company_id', $companyId)->where('status', 'validated')->get();

        return view('training.training-plans.edit', compact('trainingPlan', 'users', 'materials', 'trainingNeeds'));
    }

    public function update(Request $request, TrainingPlan $trainingPlan)
    {
        if ($trainingPlan->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'training_type' => 'required|in:classroom,e_learning,on_job_training,workshop,simulation,refresher,certification,combination',
            'planned_start_date' => 'nullable|date',
            'planned_end_date' => 'nullable|date|after_or_equal:planned_start_date',
            'duration_hours' => 'nullable|integer|min:1',
            'duration_days' => 'nullable|integer|min:1',
            'delivery_method' => 'required|in:internal,external,mixed',
            'instructor_id' => 'nullable|exists:users,id',
            'external_instructor_name' => 'nullable|string|max:255',
            'external_instructor_qualifications' => 'nullable|string',
            'location_name' => 'nullable|string|max:255',
            'location_address' => 'nullable|string',
            'training_materials' => 'nullable|array',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        $trainingPlan->update($validated);

        return redirect()->route('training.training-plans.show', $trainingPlan)
            ->with('success', 'Training plan updated successfully.');
    }

    public function destroy(TrainingPlan $trainingPlan)
    {
        if ($trainingPlan->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        // Check if sessions exist
        if ($trainingPlan->sessions->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete training plan with existing sessions. Please delete sessions first.');
        }

        $trainingPlan->delete();

        return redirect()->route('training.training-plans.index')
            ->with('success', 'Training plan deleted successfully.');
    }

    public function export(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = TrainingPlan::where('company_id', $companyId)
            ->with(['trainingNeed', 'instructor', 'creator', 'approver']);
        
        if ($request->has('status')) {
            $query->byStatus($request->get('status'));
        }

        $plans = $query->orderBy('created_at', 'desc')->get();

        ActivityLog::log('export', 'training', 'TrainingPlan', null, 'Exported training plans list');

        $format = $request->get('format', 'csv');

        if ($format === 'excel') {
            $data = $plans->map(function($plan) {
                return [
                    'Title' => $plan->title,
                    'Training Need' => $plan->trainingNeed->training_title ?? 'N/A',
                    'Training Type' => ucfirst(str_replace('_', ' ', $plan->training_type)),
                    'Delivery Method' => ucfirst($plan->delivery_method),
                    'Status' => ucfirst($plan->status),
                    'Planned Start Date' => $plan->planned_start_date ? $plan->planned_start_date->format('Y-m-d') : 'N/A',
                    'Planned End Date' => $plan->planned_end_date ? $plan->planned_end_date->format('Y-m-d') : 'N/A',
                    'Duration (Hours)' => $plan->duration_hours ?? 'N/A',
                    'Duration (Days)' => $plan->duration_days ?? 'N/A',
                    'Instructor' => $plan->instructor->name ?? $plan->external_instructor_name ?? 'N/A',
                    'Location' => $plan->location_name ?? 'N/A',
                    'Estimated Cost' => $plan->estimated_cost ? number_format($plan->estimated_cost, 2) : 'N/A',
                    'Budget Approved' => $plan->budget_approved ? 'Yes' : 'No',
                    'Created By' => $plan->creator->name ?? 'N/A',
                    'Approved By' => $plan->approver->name ?? 'N/A',
                    'Created At' => $plan->created_at->format('Y-m-d H:i:s'),
                ];
            });

            $exportData = $data->toArray();
            array_unshift($exportData, ['Training Plans Export']);
            array_unshift($exportData, ['Generated: ' . now()->format('Y-m-d H:i:s')]);
            array_unshift($exportData, []);

            return Excel::create('training-plans-export-' . date('Y-m-d_His'), function($excel) use ($exportData) {
                $excel->sheet('Training Plans', function($sheet) use ($exportData) {
                    $sheet->fromArray($exportData, null, 'A1', false, false);
                });
            })->export('xlsx');
        } else {
            $filename = 'training_plans_export_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($plans) {
                $file = fopen('php://output', 'w');
                
                fputcsv($file, [
                    'Title', 'Training Need', 'Training Type', 'Delivery Method', 'Status',
                    'Planned Start Date', 'Planned End Date', 'Duration (Hours)', 'Duration (Days)',
                    'Instructor', 'Location', 'Estimated Cost', 'Budget Approved', 'Created By', 'Approved By', 'Created At'
                ]);
                
                foreach ($plans as $plan) {
                    fputcsv($file, [
                        $plan->title,
                        $plan->trainingNeed->training_title ?? 'N/A',
                        ucfirst(str_replace('_', ' ', $plan->training_type)),
                        ucfirst($plan->delivery_method),
                        ucfirst($plan->status),
                        $plan->planned_start_date ? $plan->planned_start_date->format('Y-m-d') : 'N/A',
                        $plan->planned_end_date ? $plan->planned_end_date->format('Y-m-d') : 'N/A',
                        $plan->duration_hours ?? 'N/A',
                        $plan->duration_days ?? 'N/A',
                        $plan->instructor->name ?? $plan->external_instructor_name ?? 'N/A',
                        $plan->location_name ?? 'N/A',
                        $plan->estimated_cost ? number_format($plan->estimated_cost, 2) : 'N/A',
                        $plan->budget_approved ? 'Yes' : 'No',
                        $plan->creator->name ?? 'N/A',
                        $plan->approver->name ?? 'N/A',
                        $plan->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }
}
