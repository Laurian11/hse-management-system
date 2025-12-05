<?php

namespace App\Observers;

use App\Models\User;
use App\Services\TNAEngineService;

class UserObserver
{
    protected $tnaEngine;

    public function __construct(TNAEngineService $tnaEngine)
    {
        $this->tnaEngine = $tnaEngine;
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // If user has a job competency matrix, create training needs
        if ($user->job_competency_matrix_id) {
            $this->tnaEngine->processUserJobChangeTrigger($user);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // If job competency matrix changed, create training needs
        if ($user->wasChanged('job_competency_matrix_id') && $user->job_competency_matrix_id) {
            $oldMatrixId = $user->getOriginal('job_competency_matrix_id');
            $oldMatrix = $oldMatrixId ? \App\Models\JobCompetencyMatrix::find($oldMatrixId) : null;
            $this->tnaEngine->processUserJobChangeTrigger($user, $oldMatrix);
        }
    }
}
