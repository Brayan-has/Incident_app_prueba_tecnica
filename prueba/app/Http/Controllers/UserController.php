<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Concerns\Traits\PaginationTrait;
use App\Concerns\Traits\CacheTrait;
use App\Concerns\Traits\filterTrait;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Traits\HasRoles;


class UserController extends Controller
{
    use PaginationTrait, CacheTrait, filterTrait, HasRoles;
    /**
     * Display a listing of the resource.
     */
    public function index(UserRequest $request)
    {
        // check authorization
        if(!auth()->user()->can('view-user')) {
            return response()->json([
                'message' => "You don't have permission to view a user"
            ], 403);
        }

        $query = User::query();
        
        if (request("trashed") == "only") {
            $query->onlyTrashed();
        } elseif (request("trashed") == "with") {
            $query->withTrashed();
        }
        
        // parameters for filtering the data
        $search = request("search");
        $id = request("id");
        $page = request("page", 1);

        // cache key for caching the results of the query, it includes the page number, search term and id for filtering
        $cacheKey = "user_page_{$page}_search_" . md5($search ?? 'none') . "_id_" . ($id ?? "none");

        // caching for 1 minute
        $ttl = 60;

        $filter = ['id', 'name', 'email'];

        return $this->cacheData($cacheKey, $ttl, $id, $query, $filter, $search, 'users');        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        // check authorization
        if(!auth()->user()->can('create-user')) {
            return response()->json([
                'message' => "You don't have permission to create a user"
            ], 403);
        }
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // flush the cache
        Cache::supportsTags() ? Cache::tags('users')->flush() : Cache::flush();
        
        return response()->json([
            'message' => 'User created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserRequest $request, string $id)
    {
        // check authorization
        if(!auth()->user()->can('view-user')) {
            return response()->json([
                'message' => "You don't have permission to view a user"
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'data' => $user
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        // check authorization
        if(!auth()->user()->can('edit-user')) {
            return response()->json([
                'message' => "You don't have permission to edit a user"
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $data = $request->validated();
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        
        // flush the cache
        Cache::supportsTags() ? Cache::tags('users')->flush() : Cache::flush();
        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // check authorization
        if(!auth()->user()->can('delete-user')) {
            return response()->json([
                'message' => "You don't have permission to delete a user"
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();
        
        // flush the cache
        Cache::supportsTags() ? Cache::tags('users')->flush() : Cache::flush();

        return response()->json([
            'message' => 'User deleted successfully',
        ], 200);
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore($id)
    {
        if(!auth()->user()->can('edit-user')) {
            return response()->json([
                'message' => "You don't have permission to restore a user"
            ], 403);
        }

        $user = User::withTrashed()->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if (!$user->trashed()) {
            return response()->json([
                'message' => 'User is not deleted'
            ], 400);
        }

        $user->restore();

        // flush the cache
        Cache::supportsTags() ? Cache::tags('users')->flush() : Cache::flush();

        return response()->json([
            'message' => 'User restored successfully'
        ], 200);
    }

    /**
     * Permanently delete a user.
     */
    public function forceDelete($id)
    {
        if(!auth()->user()->can('delete-user')) {
            return response()->json([
                'message' => "You don't have permission to permanently delete a user"
            ], 403);
        }

        $user = User::withTrashed()->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->forceDelete();

        // flush the cache
        Cache::supportsTags() ? Cache::tags('users')->flush() : Cache::flush();

        return response()->json([
            'message' => 'User permanently deleted'
        ], 200);
    }
}
