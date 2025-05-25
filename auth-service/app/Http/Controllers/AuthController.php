<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request) {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255', // Name is required, must be a string, and max 255 characters
            'email' => 'required|email|unique:users', // Email is required, must be valid and unique in the users table
            'password' => 'required|min:6', // Password is required and must be at least 6 characters
            'role_id' => 'required|exists:roles,id', // Role ID is required and must exist in the roles table
        ]);
    
        // Create a new user with the validated data
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password), // Hash the password before storing
        ]);
    
        // Generate a JWT token for the newly created user
        $token = JWTAuth::fromUser($user);
    
        // Return the user and token as a JSON response
        return response()->json(compact('user','token'));
    }
}
