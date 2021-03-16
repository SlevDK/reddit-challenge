<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\CommentCreateRequest;
use App\Http\Requests\Comment\CommentListRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\Comment\CommentResourceCollection;
use App\Models\Comment;
use App\Models\Thread;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('store');
    }

    /**
     * Get comment list.
     *
     * @param CommentListRequest $request
     * @param Thread $thread
     * @return JsonResponse
     */
    public function index(CommentListRequest $request, Thread $thread)
    {
        $limit = (int) $request->input('limit', 20);
        $offset = (int) $request->input('offset', 0);
        $order = $request->input('order', 'DESC');

        $comments = $thread->comments()->skip($offset)->take($limit)
            ->orderBy('created_at', $order)->get();

        return response()->json(new CommentResourceCollection($comments));
    }

    /**
     * Create new comment.
     *
     * @param CommentCreateRequest $request
     * @param Thread $thread
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(CommentCreateRequest $request, Thread $thread)
    {
        $this->authorize('create-comment', $thread);

        $comment = Comment::make($request->only('text'), $thread);

        return response()->json(new CommentResource($comment));
    }
}
