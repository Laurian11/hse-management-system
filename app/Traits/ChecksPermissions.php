<?php

namespace App\Traits;

trait ChecksPermissions
{
    /**
     * Check if user has permission, abort if not
     */
    protected function checkPermission(string $permission): void
    {
        if (!auth()->check()) {
            abort(401, 'You must be logged in to perform this action.');
        }

        if (!auth()->user()->hasPermission($permission)) {
            abort(403, 'You do not have permission to perform this action.');
        }
    }

    /**
     * Check if user has any of the given permissions
     */
    protected function checkAnyPermission(array $permissions): void
    {
        if (!auth()->check()) {
            abort(401, 'You must be logged in to perform this action.');
        }

        if (!auth()->user()->hasAnyPermission($permissions)) {
            abort(403, 'You do not have permission to perform this action.');
        }
    }

    /**
     * Check if user has all of the given permissions
     */
    protected function checkAllPermissions(array $permissions): void
    {
        if (!auth()->check()) {
            abort(401, 'You must be logged in to perform this action.');
        }

        if (!auth()->user()->hasAllPermissions($permissions)) {
            abort(403, 'You do not have permission to perform this action.');
        }
    }
}

