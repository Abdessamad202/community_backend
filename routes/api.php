<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\FriendRequestController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SavedPostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomBroadcastAuthController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Broadcast;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/verify-email', [VerificationController::class, 'verifyEmail']);
    Route::post('/send-verification-code', [VerificationController::class, 'sendVerificationCode']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/complete-profile', [ProfileController::class, 'completeProfile']);
});

Route::middleware(['auth:sanctum', 'verified', 'profile.complete'])->group(function () {
    Route::post('/test', [ProfileController::class, 'test']);
});


Route::post('/send-reset-password-code', [ForgotPasswordController::class, 'sendResetCode']);
Route::post('/validate-reset-code', [ForgotPasswordController::class, 'validateResetCode']);  // Step 2
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);

// are worked

// Protected Routes: Require authentication
Route::middleware('auth:sanctum')->group(function () {

    // Public Routes: Viewing posts does not require authentication
    Route::get('/posts', [PostController::class, 'index']); // Get all posts with pagination
    // done

    Route::get('/users/{user}/posts', [PostController::class, 'show']); // Get posts by a specific user
    Route::post('posts/{post}', [PostController::class, 'update']); // Get posts by a specific user
    // done
    Route::get('/posts/{post}', [PostController::class, 'showSinglePost']); // Get a single post
    // done
    // CRUD operations for posts (excluding index and show, as they are public)

    Route::apiResource('posts', PostController::class)->only(['store', 'destroy']);
    // done
    Route::get('/posts/{post}/likers', [PostController::class, 'likersWithStatus']);

    // Likes - Toggle like/unlike
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle']);
    // done
    // Comments - Create, Update, Delete
    Route::apiResource('posts.comments', CommentController::class)->only(['store', 'update', 'destroy']);
    // done
});

//

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/friend-request/{receiver}', [FriendRequestController::class, 'send']);
    // done
    Route::patch('/friend-request/{sender}/{action}', [FriendRequestController::class, 'respond']);
    // done
    Route::delete('/friend-request/{receiver}', [FriendRequestController::class, 'cancel']);
    // done
    Route::get('/friend-requests/received', [FriendRequestController::class, 'received']);
    Route::get('/friend-requests/received/v2', [FriendRequestController::class, 'received2']);
    Route::get('/friend-requests/sent/v2', [FriendRequestController::class, 'sent2']);
    Route::get('/friend-requests/sent', [FriendRequestController::class, 'sent']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/friends', [FriendController::class, 'index']); // List friends
    Route::get('/friends/v2', [FriendController::class, 'index2']); // List friends
    Route::delete('/friends/{friend}', [FriendController::class, 'destroy']); // Remove friend
});

Route::middleware('auth:sanctum')->group(function () {

    // Get all conversations for the authenticated user
    Route::get('conversations', [ConversationController::class, 'index']);
    Route::get('conversations/{conversation}', [ConversationController::class, 'show']);
});
// api.php


// Routes for messages
Route::middleware('auth:sanctum')->group(function () {
    // Send a message to a conversation
    // Route::post('/conversations/{conversation}/messages', [MessageController::class, 'send'])
    //     ->name('messages.send');

    // Update an existing message
    Route::put('/messages/{message}', [MessageController::class, 'update'])
        ->name('messages.update');

    // Delete a message
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])
        ->name('messages.destroy');
    Route::get('/profiles/search', [ProfileController::class, 'search']);

});


Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'showAuthenticatedUserData']);
Route::middleware(['auth:sanctum'])->post('/profile', [ProfileController::class, 'update']);
Route::post('/posts/{post}/toggle-save', [PostController::class, 'toggleSave'])->middleware('auth:sanctum');
Route::get('/saved-posts', [SavedPostController::class, 'index'])->middleware('auth:sanctum');

Route::get('/profiles/{user}', [UserController::class, 'profile'])->middleware('auth:sanctum');
Route::patch('/change-password', [UserController::class, 'changePassword'])->middleware('auth:sanctum');
Route::post('/messages/{conversation}',[MessageController::class, 'send'])->middleware('auth:sanctum');
Route::post('/typing', [MessageController::class, 'typing'])->middleware('auth:sanctum');

// Route::post('/broadcasting/auth', [CustomBroadcastAuthController::class, 'authenticate'])
//      ->middleware('auth:sanctum');
// Broadcast::routes(['middleware' => ['auth:sanctum']]);