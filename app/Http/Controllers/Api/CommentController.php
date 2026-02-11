<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function store(Post $post, StoreCommentRequest $request, CommentService $service): JsonResponse
    {
        $comment = $service->create($post, $request->validated());

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(201);
    }
}
