<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Conversation;
use App\Models\FriendRequest;
use App\Models\Message;
use App\Models\Post;
use App\Policies\CommentPolicy;
use App\Policies\ConversationPolicy;
use App\Policies\FriendRequestPolicy;
use App\Policies\MessagePolicy;
use App\Policies\PostPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

/**
 * Bootstrap any application services.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(FriendRequest::class, FriendRequestPolicy::class);
        Gate::policy(Conversation::class, ConversationPolicy::class);
        Gate::policy(Message::class, ConversationPolicy::class);
    }
}
