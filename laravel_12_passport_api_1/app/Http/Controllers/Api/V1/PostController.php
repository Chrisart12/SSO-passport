<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePostRequest;
use App\Http\Requests\Api\V1\UpdatePostRequest;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        
        $this->authorize('viewAny', Post::class);

        $perPage = config('custom.pagination.per_page');

        return PostResource::collection(
            Post::with('user', 'comments')->orderBy('created_at', 'desc')->paginate($perPage)
        );
    }

    public function store(StorePostRequest $request)
    {
        $this->authorize('create', Post::class);

        $post = $request->user()->posts()->create($request->validated());
        return new PostResource($post->load('user', 'comments'));
    }

    public function show(Post $post)
    {
        $this->authorize('view', $post);

        return new PostResource($post->load('user', 'comments'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->validated());
        return new PostResource($post->load('user', 'comments'));
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();
        return response()->json(null, 204);
    }
}
