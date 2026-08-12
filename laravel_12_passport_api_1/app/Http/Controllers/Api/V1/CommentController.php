<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCommentRequest;
use App\Http\Requests\Api\V1\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\Api\V1\CommentResource;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        $this->authorize('viewAny', Comment::class);
        $perPage = config('custom.pagination.per_page');
        
        return CommentResource::collection(
            $post->comments()->with('user')->paginate($perPage)
        );
    }

    public function store(StoreCommentRequest $request, Post $post)
    {
        $comment = $post->comments()->create($request->validated());
        return response()->json($comment, 201);
    }

    public function show(Post $post, Comment $comment)
    {
        $this->authorize('view', $comment);
        
        return new CommentResource($comment->load('user'));
    }

    public function update(UpdateCommentRequest $request, Post $post, Comment $comment)
    {
        $comment->update($request->validated());
        return response()->json($comment);
    }

    public function destroy(Post $post, Comment $comment)
    {
        $this->authorize('delete', $comment);
        
        $comment->delete();
        return response()->json(null, 204);
    }
}