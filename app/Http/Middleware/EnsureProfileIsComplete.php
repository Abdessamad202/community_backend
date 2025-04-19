<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->registration_status !== "completed" || !$user->profile) {
            return response()->json(['message' => 'Please complete your profile.'], 403);
        }
        return $next($request);
    }
}
