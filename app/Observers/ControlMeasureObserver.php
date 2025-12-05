<?php

namespace App\Observers;

use App\Models\ControlMeasure;
use App\Services\TNAEngineService;

class ControlMeasureObserver
{
    protected $tnaEngine;

    public function __construct(TNAEngineService $tnaEngine)
    {
        $this->tnaEngine = $tnaEngine;
    }

    /**
     * Handle the ControlMeasure "created" event.
     */
    public function created(ControlMeasure $controlMeasure): void
    {
        // Auto-create training need if it's an administrative control
        if ($controlMeasure->control_type === 'administrative') {
            $this->tnaEngine->processControlMeasureTrigger($controlMeasure);
        }
    }

    /**
     * Handle the ControlMeasure "updated" event.
     */
    public function updated(ControlMeasure $controlMeasure): void
    {
        // If control type changed to administrative, create training need
        if ($controlMeasure->wasChanged('control_type') && 
            $controlMeasure->control_type === 'administrative' &&
            !$controlMeasure->related_training_need_id) {
            $this->tnaEngine->processControlMeasureTrigger($controlMeasure);
        }
    }
}
