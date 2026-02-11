<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Ensure test user exists (idempotent)
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ]
        );

        // Create some additional users only if there are few users
        if (User::count() < 6) {
            User::factory(count: 5)->create();
        }

        // Create posts and comments only if there are no posts yet
        if (Post::count() === 0) {
            // Create published posts with comments
            Post::factory()->count(5)->published()->create()->each(function (Post $post): void {
                $commentsCount = rand(0, 5);
                if ($commentsCount > 0) {
                    Comment::factory()->count($commentsCount)->create(['post_id' => $post->id]);
                }
            });

            // Create some draft posts (no comments)
            Post::factory()->count(5)->create();
        }
    }
}
