<?php

namespace App\Services;

use App\Exceptions\BusinessRuleViolation;
use App\Models\Comment;
use App\Models\Post;

class CommentService
{
    public function create(Post $post, array $data): Comment
    {
        if ($post->status !== 'published') {
            throw new BusinessRuleViolation(
                'No se puede comentar un post no publicado.',
                ['post' => ['No se puede comentar un post no publicado.']]
            );
        }

        $maxComments = (int) config('posts.max_comments_per_post', 10);
        $currentCount = $post->comments()->count();

        if ($currentCount >= $maxComments) {
            throw new BusinessRuleViolation(
                'Se alcanzó el límite de comentarios para este post.',
                ['post' => ["Máximo {$maxComments} comentarios por post."]]
            );
        }

        return $post->comments()->create($data);
    }
}
