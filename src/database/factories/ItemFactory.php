<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word(),
            'brand' => $this->faker->company(),
            'condition' => $this->faker->randomElement(['良好', 'やや傷や汚れあり', '目立った傷や汚れなし', '状態が悪い']),
            'description' => $this->faker->realText(),
            'price' => $this->faker->numberBetween(300, 10000),
            'img_url' => 'items/dummy.jpg',
            'status' => 'available',
        ];
    }
}
