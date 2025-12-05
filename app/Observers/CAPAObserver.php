<?php

namespace App\Observers;

use App\Models\CAPA;
use App\Services\TNAEngineService;

class CAPAObserver
{
    protected $tnaEngine;

    public function __construct(TNAEngineService $tnaEngine)
    {
        $this->tnaEngine = $tnaEngine;
    }

    /**
     * Handle the CAPA "created" event.
     */
    public function created(CAPA $capa): void
    {
        // Check if this CAPA is for training
        $this->tnaEngine->processCAPATrigger($capa);
    }
}
