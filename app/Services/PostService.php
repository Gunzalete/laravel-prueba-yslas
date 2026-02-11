<?php

namespace App\Services;

use App\Exceptions\BusinessRuleViolation;
use App\Models\Post;

class PostService
{
    public function create(array $data): Post
    {
        $data['status'] = $data['status'] ?? 'draft';

        if ($data['status'] === 'published') {
            $this->ensureTitleUniqueForToday($data['title']);
            $data['published_at'] = $data['published_at'] ?? now();
        } else {
            $data['published_at'] = null;
        }

        return Post::create($data);
    }

    /**
     * Update a post while enforcing business rules.
     *
     * @throws \App\Exceptions\BusinessRuleViolation
     */
    public function update(Post $post, array $data): Post
    {
        $newStatus = $data['status'] ?? $post->status;

        // Business rule: a published post cannot be reverted to draft
        if ($post->status === 'published' && $newStatus === 'draft') {
            throw new BusinessRuleViolation(
                'Un post publicado no puede volver a borrador.',
                ['status' => ['Un post publicado no puede volver a borrador.']]
            );
        }

        if ($newStatus === 'published' && $post->status !== 'published') {
            $this->ensureTitleUniqueForToday($data['title'] ?? $post->title);
            $data['published_at'] = $data['published_at'] ?? now();
        }

        if ($newStatus !== 'published') {
            $data['published_at'] = null;
        }

        $post->fill($data);
        $post->save();

        return $post;
    }

    private function ensureTitleUniqueForToday(string $title): void
    {
        $exists = Post::query()
            // Only consider posts that are already published. Drafts should not
            // prevent publishing another post with the same title.
            ->where('status', 'published')
            ->whereDate('created_at', now()->toDateString())
            ->where('title', $title)
            ->exists();

        if ($exists) {
            throw new BusinessRuleViolation(
                'Ya existe un post con este título hoy.',
                ['title' => ['Ya existe un post con este título hoy.']]
            );
        }
    }
}
