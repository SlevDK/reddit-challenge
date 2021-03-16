<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ThreadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('me', [AuthController::class, 'me'])->name('user-info');

Route::get('/boards', [BoardController::class, 'index'])->name('boards-list');
Route::get('/boards/{board:slug}', [BoardController::class, 'show'])->name('board-info');
Route::put('/boards', [BoardController::class, 'store'])->name('board-store');
Route::post('/boards/{board:slug}', [BoardController::class, 'update'])->name('board-update');
Route::delete('/boards/{board:slug}', [BoardController::class, 'destroy'])->name('board-destroy');
Route::get('/boards/{board:slug}/users', [BoardController::class, 'users'])->name('board-users');
Route::post('/boards/{board:slug}/users/{user}/role', [BoardController::class, 'setROle'])->name('board-user-role');

Route::get('/{board:slug}/threads', [ThreadController::class, 'index'])->name('threads-list');
Route::get('/{board:slug}/threads/{thread:id}', [ThreadController::class, 'show'])->name('thread-info');
Route::put('/{board:slug}/threads', [ThreadController::class, 'store'])->name('thread-store');
Route::post('/{board:slug}/threads/{thread:id}', [ThreadController::class, 'update'])->name('thread-update');
Route::post('/{board:slug}/threads/{thread:id}/close', [ThreadController::class, 'close'])->name('thread-close');
Route::post('/{board:slug}/threads/{thread:id}/tag', [ThreadController::class, 'addTag'])->name('thread-tag-add');

Route::get('/threads/{thread:id}/comments', [CommentController::class, 'index'])->name('comments-list');
Route::put('/threads/{thread:id}/comments', [CommentController::class, 'store'])->name('comment-store');
