<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $post;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->post = Post::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_can_create_comment()
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/v1/comments', [
            'post_id' => $this->post->id,
            'content' => $this->faker->sentence,
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['data' => ['id', 'post_id', 'content', 'user_id']]);

        $this->assertDatabaseHas('comments', [
            'post_id' => $this->post->id,
            'content' => $response->json('data.content'),
            'user_id' => $this->user->id,
        ]);
    }

    public function test_can_get_comments_for_post()
    {
        $this->actingAs($this->user);
        $comment = Comment::factory()->create(['post_id' => $this->post->id, 'user_id' => $this->user->id]);

        $response = $this->getJson('/api/v1/posts/' . $this->post->id . '/comments');

        $response->assertStatus(200)
                 ->assertJsonFragment(['content' => $comment->content]);
    }

    public function test_can_update_comment()
    {
        $this->actingAs($this->user);
        $comment = Comment::factory()->create(['post_id' => $this->post->id, 'user_id' => $this->user->id]);

        $response = $this->putJson('/api/v1/comments/' . $comment->id, [
            'content' => 'Updated content',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['content' => 'Updated content']);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated content',
        ]);
    }

    public function test_can_delete_comment()
    {
        $this->actingAs($this->user);
        $comment = Comment::factory()->create(['post_id' => $this->post->id, 'user_id' => $this->user->id]);

        $response = $this->deleteJson('/api/v1/comments/' . $comment->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}