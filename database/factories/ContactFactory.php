<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement([1, 2, 3]),
            'email' => fake()->unique()->safeEmail(),
            'tel' => fake()->numerify('###########'),
            'address' => fake()->address(),
            'building' => fake()->secondaryAddress(),
            'detail' => fake()->realText(100),
        ];
    }
}