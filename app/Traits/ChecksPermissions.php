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

    /**
     * Authorize a permission (alias for checkPermission for Laravel compatibility)
     */
    protected function authorize(string $permission): void
    {
        $this->checkPermission($permission);
    }

    /**
     * Check if user can access a resource based on company_id
     * Super admins (with null company_id) can access all resources
     * 
     * @param int|null $resourceCompanyId The company_id of the resource
     * @return bool
     */
    protected function canAccessCompanyResource(?int $resourceCompanyId): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        // Super admin can access all resources
        if (!$user->company_id) {
            $user->load('role');
            return $user->role && $user->role->name === 'super_admin';
        }

        // Regular users can only access resources from their company
        return $resourceCompanyId === $user->company_id;
    }

    /**
     * Check if user can access a resource based on company_id, abort if not
     * 
     * @param int|null $resourceCompanyId The company_id of the resource
     * @param string $message Custom error message
     * @return void
     */
    protected function authorizeCompanyResource(?int $resourceCompanyId, string $message = 'Unauthorized'): void
    {
        if (!$this->canAccessCompanyResource($resourceCompanyId)) {
            abort(403, $message);
        }
    }
}

