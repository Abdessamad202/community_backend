<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\FriendRequest;
use App\Models\Message;
use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
use App\Models\Like;
use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 250 users and get them all
        // User::factory(250)->create();
        // $users = User::all();
        // foreach ($users as $user) {
        //     $profile = Profile::where('user_id', $user->id)->first();
        //     $profile->update(
        //         [
        //             // 'user_id' => $user->id,
        //              'picture' =>'http://backend_comunity_app.test'.$profile->picture // '/imgs/profiles/pic' . (($user->id % 4) + 1) . '.jpg', // Cycle through 4 profile images
        //             // 'created_at' => $user->created_at,  // Use user's created_at timestamp
        //             // 'updated_at' => $user->updated_at,  // Use user's updated_at timestamp
        //         ]
        //     );
        // }
        // Create profile for each user
        // foreach ($users as $user) {
        //     Profile::factory()->create([
        //         'user_id' => $user->id,
        //         'picture' => '/imgs/profiles/pic' . (($user->id % 4) + 1) . '.jpg', // Cycle through 4 profile images
        //     ]);
        // }

        // Create posts for each user
        // foreach ($users as $user) {
        //     // Create 4 posts for each user, each post will have a different image
        //     for ($i = 1; $i <= 4; $i++) {
        //         Post::factory()->create([
        //             'user_id' => $user->id,
        //             'image' => '/imgs/posts/pic' . $i . '.jpg', // Assign one of 4 images to each post
        //         ]);
        //     }
        // }

        // Get all the posts created
        // $posts = Post::all();

        // Add likes and comments to each post
        // foreach ($posts as $post) {
        //     // Assign 50 likes randomly from users
        //     $usersForLikes = $users->random(50);
        //     foreach ($usersForLikes as $user) {
        //         Like::factory()->create([
        //             'post_id' => $post->id,
        //             'user_id' => $user->id,
        //         ]);
        //     }

        //     // Assign 30 comments randomly from users
        //     $usersForComments = $users->random(30);
        //     foreach ($usersForComments as $user) {
        //         Comment::factory()->create([
        //             'post_id' => $post->id,
        //             'user_id' => $user->id,
        //             'content' => 'This is a comment by user ' . $user->id, // Sample comment content
        //         ]);
        //     }
        // }

        // $users = User::take(10)->get();

        // // $users->each(function (User $user) {
        // // $users2 = User::take(10)->latest("id")->get();
        // foreach ($users as $user) {
        //     $friendRequests = $user->friendRequestsReceived()->take(10)->get();
        //     foreach ($friendRequests as $friendRequest) {
        //         $friendRequest->update(["status" =>"accepted"]);
        //     }
        // }
        // $conversations = Conversation::all();

        // foreach ($conversations as $conversation) {
        //     // Get users involved in the conversation
        //     $user1 = $conversation->user1;  // Assuming user1() is a relationship method on Conversation
        //     $user2 = $conversation->user2;  // Assuming user2() is a relationship method on Conversation

        //     // Create 10 messages for the conversation
        //     for ($i = 0; $i < 10; $i++) {
        //         // Generate a random timestamp for each message
        //         $timestamp = Carbon::now()->subDays(rand(1, 30))
        //             ->subHours(rand(1, 23))
        //             ->subMinutes(rand(1, 50));

        //         // Create a message from user1 to user2
        //         Message::factory()->create([
        //             'sender_id' => $user1->id,
        //             'receiver_id' => $user2->id,
        //             'conversation_id' => $conversation->id,
        //             'created_at' => $timestamp,
        //             'updated_at' => $timestamp,
        //         ]);

        //         // Create a message from user2 to user1
        //         $timestamp = Carbon::now()->subDays(rand(1, 30))
        //             ->subHours(rand(1, 23))
        //             ->subMinutes(rand(1, 50));

        //         Message::factory()->create([
        //             'sender_id' => $user2->id,
        //             'receiver_id' => $user1->id,
        //             'conversation_id' => $conversation->id,
        //             'created_at' => $timestamp,
        //             'updated_at' => $timestamp,
        //         ]);
        //     }
        //     $lastMessage = $conversation->messages()->latest('created_at')->first();

        //     if ($lastMessage) {
        //         $conversation->update([
        //             'last_message_at' => $lastMessage->created_at,
        //         ]);
        //     }
        // }
        // $profiles = Profile::all();
        // $profiles->each(function (Profile $profile) {
        //     if ($profile->id %2 == 0) {
        //         $profile->picture = "http://backend_comunity_app.test/imgs/profiles/pic2.jpg";
        //     }else{
        //         $profile->picture = "http://backend_comunity_app.test/imgs/profiles/pic1.jpg";
        //     }
        //     $profile->save();
        // });
        // $posts = Post::all();
        // foreach ($posts as $post) {
        //     $post->image = "http://backend_comunity_app.test" . $post->image ;
        //     $post->save();
        // }
        // $rq = FriendRequest::all();
        // foreach($rq as $r){
        //     $r->delete();
        // }
        // $this->call([
        //     FriendRequestSeeder::class,
        // ]);
        // $user = User::find(1);
        // for ($i = 11; $i <= 20; $i++) {
        //     // $friend = User::find($i);
        //     $user->sentFriendRequests()->create([
        //         'receiver_id' => $i,
        //     ]);
        // }

        // $user->sentFriendRequests()->each(function (FriendRequest $friendRequest) {
        //     $friendRequest->acceptFriendRequest();
        // });
        // $conversations = Conversation::all();
        // foreach ($conversations as $conversation) {
        //     $conversation->participants()->each(function (User $participant) use ($conversation) {
        //         for ($i = 0; $i < 100; $i++) {
        //             $timestamp = Carbon::now()->subDays(100 - $i)
        //                 ->subHours(100 - $i)
        //                 ->subMinutes(100 - $i);
        //             $conversation->messages()->create(
        //                 Message::factory()->make([
        //                     'sender_id' => $participant->id,
        //                     "created_at" => $timestamp,
        //                     "updated_at" => $timestamp,
        //                 ])->toArray()
        //             );
        //         }
        //     });
        // }
        $user = User::find(1); // Get the user with ID 1
        $conversation = $user->conversations()
            ->where('conversation_id', 3) // Make sure the correct conversation is selected
            ->first(); // Get the first matched conversation
        /**
         * @var Message $message
         */
        $message = $conversation->messages()
            ->make([
                'content' => 'Hello, this is a test message.',
            ]);
        $message->sender()->associate($user);
        $message->save();
        // $user->messages()->associate($message);
    }
}
