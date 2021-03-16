<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Board;
use App\Models\Comment;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use WithFaker;

    public function test_that_comments_list_returns_correct()
    {
        $thread = Thread::factory()->create();

        $comment1 = Comment::factory()->create([
            'thread_id' => $thread,
        ]);
        $comment2 = Comment::factory()->create([
            'thread_id' => $thread,
        ]);

        $this->getJson(route('comments-list', ['thread' => $thread->id]))
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'text' => $comment1->text,
            ])
            ->assertJsonFragment([
                'id' => $comment1->author->id,
                'name' => $comment1->author->name,
            ])
            ->assertJsonFragment([
                'text' => $comment2->text,
            ])
            ->assertJsonFragment([
                'id' => $comment2->author->id,
                'name' => $comment2->author->name,
            ]);
    }

    public function test_that_comment_can_be_created()
    {
        $thread = Thread::factory()->create();
        $board = $thread->board;
        $user = User::factory()->create();
        $board->updateRole($user, Board::ROLE_MEMBER);

        $this->assertDatabaseCount('comments', 0);
        $this->actingAs($user)->putJson(route('comment-store', ['thread' => $thread->id]), [
            'text' => 'This is my comment',
        ])
            ->assertOk()
            ->assertJsonFragment([
                'text' => 'This is my comment',
            ]);

        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', ['thread_id' => $thread->id, 'text' => 'This is my comment']);
    }

    public function test_that_member_can_create_comment()
    {
        $thread = Thread::factory()->create();
        $board = $thread->board;
        $user = User::factory()->create();
        $board->updateRole($user, Board::ROLE_MEMBER);

        $this->assertDatabaseCount('comments', 0);
        $this->actingAs($user)->putJson(route('comment-store', ['thread' => $thread->id]), [
            'text' => 'This is my comment',
        ])
            ->assertOk()
            ->assertJsonFragment([
                'text' => 'This is my comment',
            ]);

        $this->assertDatabaseCount('comments', 1);
    }

    public function test_that_moderator_can_create_comment()
    {
        $thread = Thread::factory()->create();
        $board = $thread->board;
        $user = User::factory()->create();
        $board->updateRole($user, Board::ROLE_MODERATOR);

        $this->assertDatabaseCount('comments', 0);
        $this->actingAs($user)->putJson(route('comment-store', ['thread' => $thread->id]), [
            'text' => 'This is my comment',
        ])
            ->assertOk()
            ->assertJsonFragment([
                'text' => 'This is my comment',
            ]);

        $this->assertDatabaseCount('comments', 1);
    }

    public function test_that_non_joined_user_can_create_comment()
    {
        $thread = Thread::factory()->create();
        $board = $thread->board;
        $user = User::factory()->create();

        $this->assertDatabaseCount('comments', 0);
        $this->actingAs($user)->putJson(route('comment-store', ['thread' => $thread->id]), [
            'text' => 'This is my comment',
        ])
            ->assertOk()
            ->assertJsonFragment([
                'text' => 'This is my comment',
            ]);

        $this->assertDatabaseCount('comments', 1);
    }

    public function test_that_banned_user_cant_create_comment()
    {
        $thread = Thread::factory()->create();
        $board = $thread->board;
        $user = User::factory()->create();
        $board->updateRole($user, Board::ROLE_BANNED);

        $this->assertDatabaseCount('comments', 0);
        $this->actingAs($user)->putJson(route('comment-store', ['thread' => $thread->id]), [
            'text' => 'This is my comment',
        ])
            ->assertStatus(403);

        $this->assertDatabaseCount('comments', 0);
    }
}
