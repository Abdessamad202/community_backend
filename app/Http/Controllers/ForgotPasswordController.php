<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PasswordReset;

class ForgotPasswordController extends Controller
{
    /**
     * Step 1: Send Reset Code
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $code = random_int(100000, 999999); // Generate 6-digit code

        PasswordReset::updateOrCreate(
            ['email' => $request->email],
            [
                'code' => $code,
                'expires_at' => now()->addMinutes(10)
            ]
        );

        // Send code via email
        Mail::raw("Your password reset code is: $code", function ($message) use ($request) {
            $message->to($request->email)->subject('Password Reset Code');
        });

        return response()->json([
            'message' => 'Reset code sent to your email.',
            "user" => [
                "email" => $request->email
            ]
        ], 200);
    }

    /**
     * Step 2: Verify Reset Code & Authenticate User (Sanctum Login)
     */
    public function validateResetCode(Request $request)
    {
        $request->validate([
            'code' => 'required|integer',
        ]);

        $resetRequest = PasswordReset::where('code', $request->code)
            ->first();

        if (!$resetRequest || $resetRequest->isExpired()) {
            return response()->json(['message' => 'Invalid or expired reset code.'], 400);
        }

        $user = User::where('email', $resetRequest->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Log in the user using Sanctum
        $token = $user->createToken('reset-password')->plainTextToken;
        return response()->json([
            'message' => 'Code verified successfully. You are now authenticated.',
            'user' => [
                'id' => $user->id,
                'token' => $token
            ]
        ]);
    }


    /**
     * Step 3: Change Password (Sanctum Protected)
     */
    public function resetPassword(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Retrieve the user based on the email
        $user = User::where('email', $request->email)->first();

        // If user doesn't exist, return unauthorized response
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Update user's password
        $user->update(['password' => Hash::make($request->password)]);

        // Delete the password reset record after successful password change
        PasswordReset::where('email', $user->email)->delete();
        $token = $user->createToken('')->plainTextToken;
        // Return success message
        return response()->json([
            'message' => 'Password changed successfully.',
            "user" => [
                "id" => $user->id,
                "token" => $token
            ]
        ], 200);
    }

}
