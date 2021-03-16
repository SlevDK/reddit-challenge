<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Board;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BoardControllerTest extends TestCase
{
    public function test_that_board_list_returns_correct()
    {
        $board1 = Board::factory()->create();
        $board2 = Board::factory()->create();

        $this->getJson(route('boards-list'))
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'id' => $board1->id,
                'name' => $board1->name,
                'slug' => $board1->slug,
            ])
            ->assertJsonFragment([
                'id' => $board2->id,
                'name' => $board2->name,
                'slug' => $board2->slug,
            ]);
    }

    public function test_that_concrete_board_info_returns_correct()
    {
        $board = Board::factory()->create();

        $this->getJson(route('board-info', ['board' => $board->slug]))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $board->id,
                'name' => $board->name,
                'slug' => $board->slug,
            ]);
    }

    public function test_that_unauthorized_user_cant_store_the_board()
    {
        $name = 'some name';

        $this->putJson(route('board-store'), ['name' => $name])
            ->assertStatus(401);
    }

    public function test_that_board_can_be_created()
    {
        $user = User::factory()->create();
        $name = 'My board';

        $this->assertDatabaseCount('boards', 0);

        $this->actingAs($user)->putJson(route('board-store'), [
            'name' => $name,
        ])
            ->assertOk()
            ->assertJsonFragment([
                'name' => $name,
            ]);

        $this->assertDatabaseCount('boards', 1);
        $this->assertDatabaseHas('boards', ['name' => $name]);
    }

    public function test_that_user_becomes_moderator_in_created_board()
    {
        $user = User::factory()->create();
        $name = 'My board';

        $this->actingAs($user)->putJson(route('board-store'), [
            'name' => $name,
        ])
            ->assertOk();

        $board = Board::where('name', $name)->first();

        $this->assertTrue($board->isModerator($user));
    }

    public function test_that_board_can_be_updated()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create();
        $board->updateRole($user, Board::ROLE_MODERATOR);

        $this->actingAs($user)->postJson(route('board-update', ['board' => $board->slug]), [
            'name' => 'Updated board name',
        ])
            ->assertOk()
            ->assertJsonFragment([
                'id' => $board->id,
                'name' => 'Updated board name',
                'slug' => $board->slug,
            ]);
    }

    public function test_that_unauthorized_user_cant_update_board()
    {
        $board = Board::factory()->create();

        $this->postJson(route('board-update', ['board' => $board->slug]), [
            'name' => 'Updated board name',
        ])
            ->assertStatus(401);
    }

    public function test_that_non_moderator_cant_update_board()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create();
        $board->updateRole($user, Board::ROLE_MEMBER);

        $this->actingAs($user)->postJson(route('board-update', ['board' => $board->slug]), [
            'name' => 'Updated board name',
        ])
            ->assertStatus(403);

        $board->updateRole($user, Board::ROLE_BANNED);

        $this->actingAs($user)->postJson(route('board-update', ['board' => $board->slug]), [
            'name' => 'Updated board name',
        ])
            ->assertStatus(403);
    }

    public function test_that_board_can_be_soft_deleted()
    {
        $user = User::factory()->create();
        $board = Board::factory()->create();
        $board->updateRole($user, Board::ROLE_MODERATOR);

        $this->assertNull($board->deleted_at);

        $this->actingAs($user)->deleteJson(route('board-destroy', ['board' => $board->slug]))
            ->assertNoContent();

        $board->refresh();
        $this->assertNotNull($board->deleted_at);
    }

    public function test_that_board_users_returns_correct()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $board = Board::factory()->create();
        $board->updateRole($user1, Board::ROLE_MODERATOR);
        $board->updateRole($user2, Board::ROLE_MEMBER);

        $this->getJson(route('board-users', ['board' => $board->slug]))
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'id' => $user1->id,
                'name' => $user1->name,
                'role' => Board::ROLE_MODERATOR,
            ])
            ->assertJsonFragment([
                'id' => $user2->id,
                'name' => $user2->name,
                'role' => Board::ROLE_MEMBER,
            ]);
    }

    public function test_that_user_role_can_be_updated()
    {
        $owner = User::factory()->create();
        $board = Board::factory()->create([
            'owner_id' => $owner,
        ]);
        $board->updateRole($owner, Board::ROLE_MODERATOR);

        $user = User::factory()->create();
        $this->assertNull($board->getUserRole($user));

        $this->actingAs($owner)->postJson(route('board-user-role', [
            'board' => $board->slug,
            'user' => $user->id,
        ]), [
            'role' => Board::ROLE_MEMBER,
        ])
            ->assertOk()
            ->assertJsonFragment([
                'id' => $user->id,
                'role' => Board::ROLE_MEMBER,
            ]);

        $role = $board->getUserRole($user);
        $this->assertNotNull($role);
        $this->assertEquals(Board::ROLE_MEMBER, $role);
    }
}
