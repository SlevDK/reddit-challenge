<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function test_that_user_can_be_logged_in()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->assertGuest();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(RouteServiceProvider::HOME);

        $this->assertAuthenticatedAs($user);
    }

    public function test_that_user_can_be_logged_out()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(RouteServiceProvider::HOME);

        $this->assertGuest();
    }

    public function test_that_user_can_get_self_profile_info()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $this->actingAs($user)->getJson(route('user-info'))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

}
