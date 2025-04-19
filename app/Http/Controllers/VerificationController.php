<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Verification;
use Carbon\Carbon;

class VerificationController extends Controller
{
    /**
     * Send verification code to user email.
     */
    public function sendVerificationCode(Request $request)
    {
        if ($request->has('email')) {
            $user = User::where('email', $request->email)->first();
        } else {
            $user = $request->user();
        }
        if ($user->is_verified) {
            return response()->json([
                'message' => 'Your account is already verified.',
                'registration_status' => $user->registration_status
            ], 409);
        }

        // Generate a random 6-digit verification code
        $code = rand(100000, 999999);

        // Save the verification code in the database

        Verification::updateOrCreate(
            ['user_id' => $user->id],
            ['code' => $code, 'expires_at' => Carbon::now()->addMinutes(10)]
        );

        // Send email (setup Mail later)
        Mail::raw("Your verification code is: $code", function ($message) use ($user) {
            $message->to($user->email)->subject('Email Verification Code');
        });

        return response()->json(['message' => 'Verification code sent.'], 200);
    }

    /**
     * Verify the user by checking the code.
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'code' => 'required|integer',
        ]);

        $user = $request->user(); // Get authenticated user from token
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }
        if ($user->is_verified) {
            return response()->json([
                'message' => 'Your account is already verified.',
                'registration_status' => $user->registration_status
            ], 409);
        }

        // Find the verification record
        $verification = Verification::where('user_id', $user->id)
            ->where('code', $request->code)
            ->first();
        if (!$verification || $verification->isExpired()) { // Fix here
            return response()->json([
                'message' => 'Invalid or expired code.',
                "errors" => [
                    "code" => ["The provided verification code is invalid or has expired."]
                ]
            ], 400);
        }

        // Mark email as verified
        $user->is_verified = true;
        $user->registration_status = 'verified'; // Update registration status
        $user->save();

        // Delete verification code after successful verification
        $verification->delete();

        return response()->json(['message' => 'Email verified successfully.'], 200);
    }


}
