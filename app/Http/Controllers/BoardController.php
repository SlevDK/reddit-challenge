<?php

namespace App\Http\Controllers;

use App\Http\Requests\Board\BoardCreateRequest;
use App\Http\Requests\Board\BoardListRequest;
use App\Http\Requests\Board\BoardUpdateRequest;
use App\Http\Requests\Board\SetRoleRequest;
use App\Http\Resources\Board\BoardResource;
use App\Http\Resources\Board\BoardResourceCollection;
use App\Http\Resources\Board\BoardUsersCollection;
use App\Models\Board;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class BoardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index', 'show', 'users');
    }

    /**
     * Return boards list.
     *
     * @param BoardListRequest $request
     * @return JsonResponse
     */
    public function index(BoardListRequest $request)
    {
        $limit = (int) $request->input('limit', 20);
        $offset = (int) $request->input('offset', 0);
        $order = $request->input('order', 'DESC');

        $boards = Board::skip($offset)->take($limit)->orderBy('created_at', $order)->get();

        return response()->json(new BoardResourceCollection($boards));
    }

    /**
     * Return concrete board info.
     *
     * @param Board $board
     * @return JsonResponse
     */
    public function show(Board $board)
    {
        $board->load('threads');

        return response()->json(new BoardResource($board));
    }

    /**
     * Create new board.
     *
     * @param BoardCreateRequest $request
     * @return JsonResponse
     */
    public function store(BoardCreateRequest $request)
    {
        $board = Board::make($request->only('name'));

        return response()->json(new BoardResource($board));
    }

    /**
     * Update board.
     *
     * @param BoardUpdateRequest $request
     * @param Board $board
     * @throws AuthorizationException
     */
    public function update(BoardUpdateRequest $request, Board $board)
    {
        $this->authorize('update-board', $board);

        $board->update($request->only('name'));

        return response()->json(new BoardResource($board));
    }

    /**
     * Delete board.
     *
     * @param Board $board
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Board $board)
    {
        $this->authorize('destroy-board', $board);

        $board->delete();

        return response()->json('', 204);
    }

    /**
     * Get users that joined current board.
     *
     * @param Board $board
     * @return JsonResponse
     */
    public function users(Board $board)
    {
        $users = $board->users;

        return response()->json(new BoardUsersCollection($users));
    }

    /**
     * Update provided user role in board.
     *
     * @param SetRoleRequest $request
     * @param Board $board
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function setRole(SetRoleRequest $request, Board $board, User $user)
    {
        $this->authorize('manage-board', $board);

        $board->updateRole($user, $request->input('role'));

        return response()->json(new BoardUsersCollection($board->users));
    }
}
