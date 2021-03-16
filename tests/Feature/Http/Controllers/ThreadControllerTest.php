<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Board;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ThreadControllerTest extends TestCase
{
    use WithFaker;

    public function test_that_thread_list_returns_correctly()
    {
        $board = Board::factory()->create();
        $thread1 = Thread::factory()->create([
            'board_id' => $board,
        ]);
        $thread2 = Thread::factory()->create([
            'board_id' => $board,
        ]);

        $this->getJson(route('threads-list', ['board' => $board->slug]))
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'title' => $thread1->title,
                'description' => $thread1->description,
            ])
            ->assertJsonFragment([
                'id' => $thread1->author->id,
                'name' => $thread1->author->name,
            ])
            ->assertJsonFragment([
                'title' => $thread2->title,
                'description' => $thread2->description,
            ])
            ->assertJsonFragment([
                'id' => $thread2->author->id,
                'name' => $thread2->author->name,
            ]);
    }

    public function test_that_thread_info_returns_correctly()
    {
        $thread = Thread::factory()->create();
        $board = $thread->board;

        $this->getJson(route('thread-info', [
            'board' => $board->slug,
            'thread' => $thread->id,
        ]))
            ->assertOk()
            ->assertJsonFragment([
                'title' => $thread->title,
                'description' => $thread->description,
                'status' => 'opened',
            ]);
    }

    public function test_that_unauthorized_user_cant_store_thread()
    {
        $board = Board::factory()->create();

        $this->putJson(route('thread-store', ['board' => $board->slug]))
            ->assertStatus(401);
    }

    public function test_that_thread_can_be_stored()
    {
        $board = Board::factory()->create();
        $user = $board->owner;
        $board->updateRole($user, Board::ROLE_MODERATOR);

        $title = $this->faker->sentence(2);
        $description = $this->faker->sentence(10);

        $this->assertDatabaseCount('threads', 0);
        $this->actingAs($user)->putJson(route('thread-store', ['board' => $board->slug]), [
            'title' => $title,
            'description' => $description,
        ])
            ->assertOk()
            ->assertJsonFragment([
                'title' => $title,
                'description' => $description,
            ]);

        $this->assertDatabaseCount('threads', 1);
    }

    public function test_that_member_can_have_only_one_opened_thread()
    {
        $board = Board::factory()->create();
        $user = User::factory()->create();
        $board->updateRole($user, Board::ROLE_MEMBER);

        $this->assertDatabaseCount('threads', 0);
        $this->actingAs($user)->putJson(route('thread-store', ['board' => $board->slug]), [
            'title' => $this->faker->sentence(2),
            'description' => $this->faker->sentence(10),
        ])
            ->assertOk();

        $this->actingAs($user)->putJson(route('thread-store', ['board' => $board->slug]), [
            'title' => $this->faker->sentence(2),
            'description' => $this->faker->sentence(10),
        ])
            ->assertStatus(403);
    }

    public function test_that_member_can_create_another_thread_if_prev_is_closed()
    {
        $board = Board::factory()->create();
        $user = User::factory()->create();
        $board->updateRole($user, Board::ROLE_MEMBER);

        $this->assertDatabaseCount('threads', 0);
        $this->actingAs($user)->putJson(route('thread-store', ['board' => $board->slug]), [
            'title' => $this->faker->sentence(2),
            'description' => $this->faker->sentence(10),
        ])
            ->assertOk();

        $user->threads->first()->closeThread();

        $this->actingAs($user)->putJson(route('thread-store', ['board' => $board->slug]), [
            'title' => $this->faker->sentence(2),
            'description' => $this->faker->sentence(10),
        ])
            ->assertOk();

        $user->refresh();
        $this->assertCount(2, $user->threads);
    }

    public function test_that_thread_can_be_updated()
    {
        $board = Board::factory()->create();
        $user = User::factory()->create();
        $board->updateRole($user, Board::ROLE_MODERATOR);

        $thread = Thread::factory()->create([
            'board_id' => $board->id,
        ]);

        $title = $this->faker->sentence(2);
        $description = $this->faker->sentence(10);
        $this->actingAs($user)->postJson(route('thread-update', [
            'board' => $board->slug,
            'thread' => $thread->id,
        ]), [
            'title' => $title,
            'description' => $description,
        ])
            ->assertOk()
            ->assertJsonFragment([
                'title' => $title,
                'description' => $description,
            ]);

        $thread->refresh();
        $this->assertEquals($title, $thread->title);
        $this->assertEquals($description, $thread->description);
    }

    public function test_that_thread_can_be_closed_by_author_with_member_role()
    {
        $board = Board::factory()->create();
        $user = User::factory()->create();
        $board->updateRole($user, Board::ROLE_MEMBER);

        $thread = Thread::factory()->create([
            'board_id' => $board,
            'author_id' => $user,
        ]);

        $this->assertDatabaseHas('threads', ['id' => $thread->id, 'status' => Thread::THREAD_STATUS_OPENED]);
        $this->actingAs($user)->postJson(route('thread-close', [
            'board' => $board->slug,
            'thread' => $thread->id,
        ]))
            ->assertOk();

        $this->assertDatabaseHas('threads', ['id' => $thread->id, 'status' => Thread::THREAD_STATUS_CLOSED]);
    }

    public function test_that_any_moderator_can_close_thread()
    {
        $board = Board::factory()->create();
        $moder = User::factory()->create();
        $board->updateRole($moder, Board::ROLE_MODERATOR);

        $thread = Thread::factory()->create([
            'board_id' => $board,
        ]);

        $this->assertDatabaseHas('threads', ['id' => $thread->id, 'status' => Thread::THREAD_STATUS_OPENED]);
        $this->actingAs($moder)->postJson(route('thread-close', [
            'board' => $board->slug,
            'thread' => $thread->id,
        ]))
            ->assertOk();

        $this->assertDatabaseHas('threads', ['id' => $thread->id, 'status' => Thread::THREAD_STATUS_CLOSED]);
    }

    public function test_that_member_cant_close_thread()
    {
        $board = Board::factory()->create();
        $member = User::factory()->create();
        $board->updateRole($member, Board::ROLE_MEMBER);

        $thread = Thread::factory()->create([
            'board_id' => $board,
        ]);

        $this->assertDatabaseHas('threads', ['id' => $thread->id, 'status' => Thread::THREAD_STATUS_OPENED]);
        $this->actingAs($member)->postJson(route('thread-close', [
            'board' => $board->slug,
            'thread' => $thread->id,
        ]))
            ->assertStatus(403);

        $this->assertDatabaseHas('threads', ['id' => $thread->id, 'status' => Thread::THREAD_STATUS_OPENED]);
    }

    public function test_that_stranger_cant_close_thread()
    {
        $board = Board::factory()->create();
        $member = User::factory()->create();

        $thread = Thread::factory()->create([
            'board_id' => $board,
        ]);

        $this->assertDatabaseHas('threads', ['id' => $thread->id, 'status' => Thread::THREAD_STATUS_OPENED]);
        $this->actingAs($member)->postJson(route('thread-close', [
            'board' => $board->slug,
            'thread' => $thread->id,
        ]))
            ->assertStatus(403);

        $this->assertDatabaseHas('threads', ['id' => $thread->id, 'status' => Thread::THREAD_STATUS_OPENED]);
    }

    public function test_that_tag_can_be_added()
    {
        $board = Board::factory()->create();
        $user = User::factory()->create();
        $board->updateRole($user, Board::ROLE_MODERATOR);

        $thread = Thread::factory()->create([
            'board_id' => $board->id,
        ]);

        $tag = $this->faker->word;
        $this->actingAs($user)->postJson(route('thread-tag-add', [
            'board' => $board->slug,
            'thread' => $thread->id,
        ]), [
            'tag' => $tag,
        ])
            ->assertOk()
            ->assertJsonFragment([
                'name' => $tag,
            ]);
    }
}
