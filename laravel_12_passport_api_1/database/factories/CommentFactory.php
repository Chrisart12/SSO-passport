<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 5),
            'post_id' => $this->faker->numberBetween(1, 30),
            'body' => $this->faker->text,
        ];
    }
}