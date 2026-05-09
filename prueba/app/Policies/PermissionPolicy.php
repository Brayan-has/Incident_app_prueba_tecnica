<?php

namespace App\Policies;

use App\Models\User;

class PermissionPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function getAllPermissions(User $user)
    {
        return $user->can('view-permission');
    }

    public function assignPermissionToRole(User $user)
    {
        return $user->can('assign-permission');
    }
    
}
