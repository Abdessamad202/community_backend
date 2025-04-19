<?php

use App\Broadcasting\ChatChannel;
use App\Http\Controllers\CustomBroadcastAuthController;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

// Route::post('/broadcasting/auth', [CustomBroadcastAuthController::class, 'authenticate'])->middleware('auth:sanctum');

// Broadcast::channel('chat.{conversation}', ChatChannel::class);
// Broadcast::channel('chat.{conversation}', function (User $user, Conversation $conversation) {
//     Log::info("Auth user: {$user->id}, Conversation: {$conversation->id}");
//     Log::info("Involves: " . ($conversation->involves($user) ? 'yes' : 'no'));
//     return $conversation->involves($user);
// });

