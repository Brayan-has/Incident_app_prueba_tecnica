<?php

namespace App\Policies;

use App\Models\User;

class IncidentPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function index(User $user)
    {
        return $user->can('view-incident');
    }

    public function show(User $user)
    {
        return $user->can('view-incident');
    }

    public function create(User $user)
    {
        return $user->can('create-incident');
    }

    public function update(User $user)
    {
        return $user->can('edit-incident');
    }

    public function delete(User $user)
    {
        return $user->can('delete-incident');
    }
}
