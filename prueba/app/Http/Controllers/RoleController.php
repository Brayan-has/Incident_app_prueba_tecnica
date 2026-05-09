<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Concerns\Traits\PaginationTrait;

class RoleController extends Controller
{
    use PaginationTrait;

    // Get all roles
    public function getAllRoles()
    {
        $roles = Role::all();
        return response()->json([
            'data' => $roles
        ], 200);
    }

    // Get all permissions
    public function getAllPermissions()
    {
        $permissions = Permission::paginate(10);
        return response()->json($this->paginate($permissions, 10), 200);
    }

    public function assignRoleToUser(Request $request, $user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $user->assignRole([$request->role_name]);

        return response()->json([
            'message' => 'Role assigned successfully'
        ], 200);
    }

    public function getRoleByUser(Request $request)
    {
        $userLoged = Auth()->user;

        $role = Role::find($userLoged->id);

        return response()->json($role, 200);
    }
}
