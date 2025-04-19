<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();  // Get the authenticated user

        // If the user is not authenticated or email is not verified, or registration is not complete
        if (!$user || !$user->is_verified ) {
            return response()->json([
                'message' => 'Your email is not verified or registration is incomplete.',
                'registration_status' => $user?->registration_status,  // Safely return current registration status
            ], 403);  // Forbidden response if conditions are not met
        }

        return $next($request);  // Allow the request to proceed if the user is verified
    }
}
