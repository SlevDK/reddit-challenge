<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Thread::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(10),
            'status' => Thread::THREAD_STATUS_OPENED,
            'board_id' => Board::factory(),
            'author_id' => User::factory(),
        ];
    }
}
