<?php

namespace App\Http\Controllers;

use App\Models\TrainingNeedsAnalysis;
use App\Models\TrainingPlan;
use App\Models\TrainingSession;
use App\Models\TrainingRecord;
use App\Models\TrainingCertificate;
use App\Models\User;
use App\Models\Department;
use App\Models\ActivityLog;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrainingReportingController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        // Date range filters
        $startDate = $request->get('start_date', now()->subMonths(6)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        // Overall Statistics
        $stats = [
            'total_training_needs' => TrainingNeedsAnalysis::where('company_id', $companyId)->count(),
            'completed_training_needs' => TrainingNeedsAnalysis::where('company_id', $companyId)
                ->where('status', 'completed')
                ->count(),
            'total_sessions' => TrainingSession::where('company_id', $companyId)->count(),
            'completed_sessions' => TrainingSession::where('company_id', $companyId)
                ->where('status', 'completed')
                ->count(),
            'total_certificates' => TrainingCertificate::where('company_id', $companyId)->count(),
            'active_certificates' => TrainingCertificate::where('company_id', $companyId)
                ->where('status', 'active')
                ->count(),
            'expired_certificates' => TrainingCertificate::where('company_id', $companyId)
                ->where('status', 'expired')
                ->count(),
        ];

        // Training Completion Rate
        $completionRate = $stats['total_training_needs'] > 0 
            ? ($stats['completed_training_needs'] / $stats['total_training_needs']) * 100 
            : 0;

        // Training by Department
        $trainingByDepartment = TrainingRecord::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('user.department')
            ->get()
            ->groupBy(function($record) {
                return $record->user->department->name ?? 'Unassigned';
            })
            ->map(function($group) {
                return [
                    'department' => $group->first()->user->department->name ?? 'Unassigned',
                    'count' => $group->count(),
                    'unique_employees' => $group->pluck('user_id')->unique()->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();

        // Training Effectiveness (by completion and competency)
        $effectivenessData = TrainingSession::where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->withCount(['competencyAssessments as passed_assessments' => function($query) {
                $query->where('assessment_result', 'passed');
            }])
            ->get()
            ->map(function($session) {
                $totalAssessments = $session->competencyAssessments->count();
                $passedAssessments = $session->passed_assessments;
                $passRate = $totalAssessments > 0 ? ($passedAssessments / $totalAssessments) * 100 : 0;
                
                return [
                    'session_id' => $session->id,
                    'title' => $session->title,
                    'total_assessments' => $totalAssessments,
                    'passed' => $passedAssessments,
                    'pass_rate' => $passRate,
                ];
            });

        // Training Cost Analysis
        $costAnalysis = TrainingPlan::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(estimated_cost) as total_cost'),
                DB::raw('AVG(estimated_cost) as avg_cost'),
                DB::raw('COUNT(*) as total_plans')
            )
            ->first();

        // Competency Gap Analysis
        $competencyGaps = TrainingNeedsAnalysis::where('company_id', $companyId)
            ->where('status', '!=', 'completed')
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority');

        // Training Trends (Monthly)
        $monthlyTrends = TrainingSession::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function($session) {
                return $session->created_at->format('Y-m');
            })
            ->map(function($group) {
                return [
                    'month' => $group->first()->created_at->format('M Y'),
                    'total_sessions' => $group->count(),
                    'completed' => $group->where('status', 'completed')->count(),
                    'scheduled' => $group->where('status', 'scheduled')->count(),
                ];
            })
            ->sortBy('month')
            ->values();

        // Top Training Types
        $topTrainingTypes = TrainingSession::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('session_type', DB::raw('count(*) as count'))
            ->groupBy('session_type')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->pluck('count', 'session_type');

        // Certificate Expiry Analysis
        $certificateExpiry = [
            'expiring_30_days' => TrainingCertificate::where('company_id', $companyId)
                ->where('status', 'active')
                ->whereBetween('expiry_date', [now(), now()->addDays(30)])
                ->count(),
            'expiring_60_days' => TrainingCertificate::where('company_id', $companyId)
                ->where('status', 'active')
                ->whereBetween('expiry_date', [now()->addDays(30), now()->addDays(60)])
                ->count(),
            'expired' => TrainingCertificate::where('company_id', $companyId)
                ->where('status', 'expired')
                ->count(),
        ];

        // Departments for filter
        $departments = Department::where('company_id', $companyId)->get();

        return view('training.reporting.index', compact(
            'stats',
            'completionRate',
            'trainingByDepartment',
            'effectivenessData',
            'costAnalysis',
            'competencyGaps',
            'monthlyTrends',
            'topTrainingTypes',
            'certificateExpiry',
            'startDate',
            'endDate',
            'departments'
        ));
    }

    public function export(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $startDate = $request->get('start_date', now()->subMonths(6)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Get comprehensive training data
        $trainingRecords = TrainingRecord::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['user', 'trainingSession.trainingPlan.trainingNeed', 'certificate', 'competencyAssessment'])
            ->get();

        $data = $trainingRecords->map(function($record) {
            return [
                'Employee Name' => $record->user->name ?? 'N/A',
                'Employee ID' => $record->user->employee_id_number ?? 'N/A',
                'Department' => $record->user->department->name ?? 'N/A',
                'Training Title' => $record->trainingSession->title ?? 'N/A',
                'Training Need' => $record->trainingSession->trainingPlan->trainingNeed->training_title ?? 'N/A',
                'Session Date' => $record->trainingSession->scheduled_start ? $record->trainingSession->scheduled_start->format('Y-m-d') : 'N/A',
                'Attendance Status' => $record->attendance ? ucfirst($record->attendance->attendance_status) : 'N/A',
                'Competency Result' => $record->competencyAssessment ? ucfirst($record->competencyAssessment->assessment_result) : 'N/A',
                'Competency Score' => $record->competencyAssessment ? $record->competencyAssessment->overall_score : 'N/A',
                'Certificate Number' => $record->certificate->certificate_number ?? 'N/A',
                'Certificate Status' => $record->certificate ? ucfirst($record->certificate->status) : 'N/A',
                'Certificate Expiry' => $record->certificate && $record->certificate->expiry_date 
                    ? $record->certificate->expiry_date->format('Y-m-d') 
                    : 'N/A',
                'Record Date' => $record->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $format = $request->get('format', 'excel');

        if ($format === 'excel') {
            $exportData = $data->toArray();
            array_unshift($exportData, ['Training Records Export']);
            array_unshift($exportData, ['Period: ' . $startDate . ' to ' . $endDate]);
            array_unshift($exportData, ['Generated: ' . now()->format('Y-m-d H:i:s')]);
            array_unshift($exportData, []);

            return Excel::create('training-records-export-' . date('Y-m-d_His'), function($excel) use ($exportData) {
                $excel->sheet('Training Records', function($sheet) use ($exportData) {
                    $sheet->fromArray($exportData, null, 'A1', false, false);
                });
            })->export('xlsx');
        } else {
            $filename = 'training_records_export_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                
                // Headers
                fputcsv($file, array_keys($data->first() ?? []));
                
                // Data
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }
}
