<?php

namespace App\Http\Controllers;

use App\Models\TrainingCertificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingCertificateController extends Controller
{
    public function generatePDF(TrainingCertificate $certificate)
    {
        if ($certificate->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $certificate->load([
            'user',
            'trainingSession.trainingPlan.trainingNeed',
            'competencyAssessment',
            'issuer',
            'company'
        ]);

        $pdf = Pdf::loadView('training.certificates.pdf', [
            'certificate' => $certificate,
        ])->setPaper('a4', 'landscape');

        $filename = "certificate-{$certificate->certificate_number}.pdf";
        
        // Update certificate file path if not set
        if (!$certificate->certificate_file_path) {
            $certificate->update([
                'certificate_file_path' => storage_path("app/certificates/{$filename}")
            ]);
        }

        return $pdf->download($filename);
    }

    public function show(TrainingCertificate $certificate)
    {
        if ($certificate->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $certificate->load([
            'user',
            'trainingSession.trainingPlan.trainingNeed',
            'competencyAssessment',
            'issuer',
            'company',
            'trainingRecord'
        ]);

        return view('training.certificates.show', compact('certificate'));
    }
}
