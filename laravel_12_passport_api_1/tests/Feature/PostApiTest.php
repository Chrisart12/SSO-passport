<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
    }

    public function test_user_can_create_post()
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/v1/posts', [
            'title' => 'Test Post',
            'content' => 'This is a test post content.',
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Post created successfully.']);

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'content' => 'This is a test post content.',
        ]);
    }

    public function test_user_can_view_post()
    {
        $post = Post::factory()->create();

        $response = $this->getJson('/api/v1/posts/' . $post->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $post->id,
                     'title' => $post->title,
                     'content' => $post->content,
                 ]);
    }

    public function test_user_can_update_post()
    {
        $this->actingAs($this->user);
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson('/api/v1/posts/' . $post->id, [
            'title' => 'Updated Title',
            'content' => 'Updated content.',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Post updated successfully.']);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'content' => 'Updated content.',
        ]);
    }

    public function test_user_can_delete_post()
    {
        $this->actingAs($this->user);
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson('/api/v1/posts/' . $post->id);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Post deleted successfully.']);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_guest_cannot_create_post()
    {
        $response = $this->postJson('/api/v1/posts', [
            'title' => 'Test Post',
            'content' => 'This is a test post content.',
        ]);

        $response->assertStatus(401);
    }
}