<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        if (!Hash::check($request["password"], $user["password"])) {
            return response()->json([
                'message' => 'Password is not correct'
            ], 401);
        }
        $token = $user->createToken('api_token', ['*'])->plainTextToken;
        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }   
    
    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'User logged out successfully'
        ], 200);
    }
}
