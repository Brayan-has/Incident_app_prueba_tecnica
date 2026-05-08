<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Concerns\Traits\PaginationTrait;

class UserController extends Controller
{
    use PaginationTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::paginate(10);

        if ($users->isEmpty()) {
            return response()->json([
                'message' => 'No users found'
            ], 404);
        }

        $pagination = $this->paginate($users);

        return response()->json([
            $pagination
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::create($request->all());

        return response()->json([
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
