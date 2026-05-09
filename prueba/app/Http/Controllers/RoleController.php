<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Concerns\Traits\PaginationTrait;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    use PaginationTrait;

    // Get all roles
    public function getAllRoles()
    {
        if(!auth()->user()->can(['view-role'])) {
            return response()->json([
                'message' => 'You are not authorized to perform this action'
            ], 403);
        }
        $roles = Role::all();
        return response()->json([
            'data' => $roles
        ], 200);
    }

    public function assignRoleToUser(Request $request, $user_id)
    {
        if(!auth()->user()->can(['assign-role'])) {
            return response()->json([
                'message' => 'You are not authorized to perform this action'
            ], 403);
        }

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

    public function getRoleByUser(Request $request, $user_id)
    {
        if(!auth()->user()->can(['get-user-role'])) {
            return response()->json([
                'message' => 'You are not authorized to perform this action'
            ], 403);
        }
        $user = User::find($user_id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $role = $user->roles;

        return response()->json($role, 200);
    }

    // Get all permissions
    public function getAllPermissions()
    {
        if(!auth()->user()->can(['view-permission'])) {
            return response()->json([
                'message' => 'You are not authorized to perform this action'
            ], 403);
        }
        $permissions = Permission::paginate(10);
        return response()->json($this->paginate($permissions, 10), 200);
    }

    // assign permission 
    public function assignPermission(Request $request, $user_id)
    {
        if(!auth()->user()->can(['assign-permission'])) {
            return response()->json([
                'message' => 'You are not authorized to perform this action'
            ], 403);
        }

        $user = User::find($user_id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // validate if the permission exist
        $permission = Permission::where('name', $request->permission_name)->first();

        if (!$permission) {
            return response()->json([
                'message' => 'Permission not found'
            ], 404);
        }
    
        $user->givePermissionTo([$permission]);

        return response()->json([
            'message' => 'Permission assigned successfully'
        ], 200);
    }
}
