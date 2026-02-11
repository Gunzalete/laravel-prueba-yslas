<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_comment_on_draft_post(): void
    {
        $post = Post::factory()->create([
            'status' => 'draft',
            'published_at' => null,
        ]);

        $payload = [
            'author_name' => 'Ana Test',
            'body' => 'Nice post here',
        ];

        $response = $this->postJson("/api/posts/{$post->id}/comments", $payload);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', 'Cannot comment on an unpublished post.');
    }
}
