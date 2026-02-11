<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_published_post(): void
    {
        $payload = [
            'title' => 'Hello World',
            'body' => str_repeat('Body ', 15),
            'status' => 'published',
        ];

        $response = $this->postJson('/api/posts', $payload);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.title', 'Hello World')
            ->assertJsonPath('data.status', 'published');

        $post = Post::first();

        $this->assertNotNull($post?->published_at);
    }

    public function test_can_filter_posts_by_status_and_paginate(): void
    {
        Post::factory()->count(2)->published()->create();
        Post::factory()->create();

        $response = $this->getJson('/api/posts?status=published&per_page=1');

        $response
            ->assertStatus(200)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'published');
    }
}
