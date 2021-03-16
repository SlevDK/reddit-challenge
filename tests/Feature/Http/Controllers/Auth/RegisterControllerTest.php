<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function test_that_register_page_can_be_returned()
    {
        $this->get(route('register-page'))
            ->assertViewIs('register-page');
    }

    public function test_that_user_can_be_registered()
    {
        $email = $this->faker->safeEmail;
        $password = 'password';

        $this->assertDatabaseCount('users', 0);

        $this->post(route('register'), [
            'email' => $email,
            'password' => $password,
            'name' => $this->faker->sentence(2),
        ]);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['email' => $email]);
    }
}
