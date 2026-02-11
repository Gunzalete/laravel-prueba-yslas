<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostIndexRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function index(PostIndexRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $perPage = $filters['per_page'] ?? 15;

        $query = Post::query()->withCount('comments');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['published_from'])) {
            $query->whereDate('published_at', '>=', $filters['published_from']);
        }

        if (!empty($filters['published_to'])) {
            $query->whereDate('published_at', '<=', $filters['published_to']);
        }

        $includeComments = false;

        if (array_key_exists('include_comments', $filters)) {
            $includeComments = filter_var($filters['include_comments'], FILTER_VALIDATE_BOOLEAN);
        }

        if ($includeComments) {
            $query->with('comments');
        }

        $posts = $query->latest()->paginate($perPage)->appends($filters);

        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request, PostService $service): JsonResponse
    {
        $post = $service->create($request->validated());

        return (new PostResource($post))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdatePostRequest $request, Post $post, PostService $service): PostResource
    {
        $post = $service->update($post, $request->validated());

        return new PostResource($post->load('comments'));
    }

    public function show(Post $post): PostResource
    {
        return new PostResource($post->load('comments'));
    }
}
