<?php

namespace Database\Seeders;

use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FriendRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::pluck('id')->toArray();
        $requests = [];

        for ($i = 0; $i < 250; $i++) {
            $sender = fake()->randomElement($users);
            $receiver = fake()->randomElement(array_filter($users, fn($id) => $id !== $sender));

            // Avoid duplicates
            $exists = FriendRequest::where('sender_id', $sender)
                ->where('receiver_id', $receiver)
                ->exists();

            if ($exists) {
                $i--;
                continue;
            }

            $requests[] = [
                'sender_id' => $sender,
                'receiver_id' => $receiver,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        FriendRequest::insert($requests);
    }
}
