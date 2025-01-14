<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $title = fake()->sentence(1),
            'slug' => Str::slug($title),
            'desc' => $desc = fake()->paragraph(),
            'excerpt' => Str::excerpt($desc),
            'category_id' => Category::all()->random()->id,
            'user_id' => User::astrologer()->get()->random()->id,
            'created_at' => fake()->dateTimeBetween('-3 month', 'now'),
            'updated_at' => fake()->dateTimeBetween('-2 month', 'now'),
        ];
    }
}
