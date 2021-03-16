<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Board::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->sentence(2);
        $slug = Board::makeSlugFromName($name);

        return [
            'name' => $name,
            'slug' => $slug,
            'owner_id' => function () {
                return User::factory();
            },
        ];
    }
}
