<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(4),
            'subtitle' => fake()->sentence(6),
            'body' => '<p>'.fake()->paragraph().'</p>',
            'image' => null,
        ];
    }
}
