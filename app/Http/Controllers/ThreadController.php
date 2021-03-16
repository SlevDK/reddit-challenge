<?php

namespace App\Http\Controllers;

use App\Http\Requests\Thread\ThreadCreateRequest;
use App\Http\Requests\Thread\ThreadListRequest;
use App\Http\Requests\Thread\ThreadTagRequest;
use App\Http\Resources\Thread\ThreadCommentsResource;
use App\Http\Resources\Thread\ThreadResourceCollection;
use App\Models\Board;
use App\Models\Tag;
use App\Models\Thread;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ThreadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('store', 'update', 'close');
    }

    /**
     * Get threads list for provided board.
     *
     * @param ThreadListRequest $request
     * @param Board $board
     * @return JsonResponse
     */
    public function index(ThreadListRequest $request, Board $board)
    {
        $limit = (int) $request->input('limit', 20);
        $offset = (int) $request->input('offset', 0);
        $order = $request->input('order', 'DESC');

        $threads = $board->threads()->skip($offset)->take($limit)
            ->orderBy('created_at', $order)->get();
        $threads->load('author');

        return response()->json(new ThreadResourceCollection($threads));
    }

    /**
     * Get thread.
     *
     * @param Board $board
     * @param Thread $thread
     * @return JsonResponse
     */
    public function show(Board $board, Thread $thread)
    {
        if ($thread->board_id != $board->id) {
            throw new ModelNotFoundException();
        }
        $thread->load('comments', 'comments.author');

        return response()->json(new ThreadCommentsResource($thread));
    }

    /**
     * Create new thread.
     *
     * @param ThreadCreateRequest $request
     * @param Board $board
     * @return mixed
     * @throws AuthorizationException
     */
    public function store(ThreadCreateRequest $request, Board $board)
    {
        $this->authorize('create-thread', $board);

        $thread = Thread::make($request->only('title', 'description'), $board);

        return response()->json(new ThreadCommentsResource($thread));
    }

    /**
     * Update existing thread.
     *
     * @param ThreadCreateRequest $request
     * @param Board $board
     * @param Thread $thread
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ThreadCreateRequest $request, Board $board, Thread $thread)
    {
        if ($thread->board_id != $board->id) {
            throw new ModelNotFoundException();
        }

        $this->authorize('update-thread', $board);

        $thread->update($request->only('title', 'description'));
        $thread->load('comments', 'comments.author', 'tags');

        return response()->json(new ThreadCommentsResource($thread));
    }

    public function addTag(ThreadTagRequest $request, Board $board, Thread $thread)
    {
        if ($thread->board_id != $board->id) {
            throw new ModelNotFoundException();
        }

        $this->authorize('update-thread', $board);

        $tag = Tag::getOrCreate($request->input('tag'));
        $thread->addTag($tag);

        $thread->load('comments', 'comments.author', 'tags');

        return response()->json(new ThreadCommentsResource($thread));
    }

    /**
     * Mark current thread as closed.
     *
     * @param Board $board
     * @param Thread $thread
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function close(Board $board, Thread $thread)
    {
        if (Gate::denies('manage-board', $board) && ! $thread->isAuthor(auth()->user())) {
            throw new AuthorizationException('Have no permissions to close this thread');
        }

        $thread->closeThread();
        $thread->load('comments', 'comments.author');

        return response()->json(new ThreadCommentsResource($thread));
    }
}
