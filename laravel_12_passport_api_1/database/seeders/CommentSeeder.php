<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    public function run()
    {
        Comment::factory()
            ->count(70) // Adjust the number of comments to create as needed
            ->create();
    }
}