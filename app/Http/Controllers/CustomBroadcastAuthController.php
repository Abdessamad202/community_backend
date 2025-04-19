<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

class CustomBroadcastAuthController extends Controller
{
    public function authenticate(Request $request)
    {
        // You can log or check the incoming request
        // Log::info('Custom Broadcasting Auth called', [
        //     'user' => Auth::user(),
        //     'channels' => $request->input('channel_name'),
        // ]);
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        // Let Laravel handle the actual channel authorization as usual
        return Broadcast::auth($request);
    }
}
