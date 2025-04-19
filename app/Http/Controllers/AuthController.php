<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        // Create the user
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        // Generate token using Laravel Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // Send verification code after registration
        app(VerificationController::class)->sendVerificationCode(new Request(['email' => $user->email]));

        return response()->json([
            'message' => 'User registered successfully. A verification code has been sent to your email.',
            'user' => [
                'id' => $user->id,  // Include the user's id
                'token' => $token,  // Send the generated token
                'registration_status' => $user->registration_status, // Include the registration status
            ],
        ], 201);

    }

    /**
     * Login the user and return a token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        // Generate token using Laravel Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        if ($user->registration_status !== 'completed') {
            return response()->json([
                'message' => 'Your registration is not complete.',
                'user' => [
                    'id' => $user->id,
                    'token' => $token,
                    'registration_status' => $user->registration_status, // Provide the current status
                ],
            ], 403); // Keep 403 to indicate the user is not fully registered
        }

        return response()->json([
            'message' => 'Login successful.',
            'user' => [
                'id' => $user->id,
                'token' => $token,
                'registration_status' => $user->registration_status, // Provide the current status
            ],
        ], 200);
    }



    /**
     * Logout the user.
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout successful.'
        ], 200);
    }
}
