<?php

use App\Models\Post;
use App\Models\User;

test('public blog index page is displayed', function () {
    $response = $this->get('/blog');

    $response->assertOk();
});

test('public blog detail page displays published post', function () {
    $post = Post::create([
        'title' => 'Test Post Title',
        'slug' => 'test-post-title',
        'content' => '<p>This is a test post content.</p>',
        'is_published' => true,
        'published_at' => now()->subDay(),
    ]);

    $response = $this->get('/blog/'.$post->slug);

    $response->assertOk();
    $response->assertSee('Test Post Title');
    $response->assertSee('This is a test post content.');
});

test('public blog detail page returns 404 for draft post', function () {
    $post = Post::create([
        'title' => 'Draft Post Title',
        'slug' => 'draft-post-title',
        'content' => '<p>This is a draft content.</p>',
        'is_published' => false,
        'published_at' => null,
    ]);

    $response = $this->get('/blog/'.$post->slug);

    $response->assertStatus(404);
});

test('admin blog manager page requires authentication', function () {
    $response = $this->get('/admin/posts');

    $response->assertRedirect('/login');
});

test('authenticated user can view admin blog manager page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin/posts');

    $response->assertOk();
});
