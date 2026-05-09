<?php

namespace App\Policies;

use App\Models\User;

class RolePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function getAllRoles(User $user)
    {
        return $user->can('view-role');
    }

    public function assignRoleToUser(User $user)
    {
        return $user->can('assign-role');
    }

    public function getRoleByUser(User $user)
    {
        return $user->can('get-user-role');
    }

   
 
}
