<?php

namespace App\Observers;

use App\Models\RootCauseAnalysis;
use App\Services\TNAEngineService;

class RootCauseAnalysisObserver
{
    protected $tnaEngine;

    public function __construct(TNAEngineService $tnaEngine)
    {
        $this->tnaEngine = $tnaEngine;
    }

    /**
     * Handle the RootCauseAnalysis "updated" event.
     */
    public function updated(RootCauseAnalysis $rootCauseAnalysis): void
    {
        // If training gap was just identified, create training need
        if ($rootCauseAnalysis->wasChanged('training_gap_identified') && 
            $rootCauseAnalysis->training_gap_identified) {
            $this->tnaEngine->processRCATrigger($rootCauseAnalysis);
        }
    }
}
