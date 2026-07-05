<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('reset password link screen cannot be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(404);
});

test('reset password link cannot be requested', function () {
    $user = User::factory()->create();

    $response = $this->post('/forgot-password', ['email' => $user->email]);

    $response->assertStatus(404);
});

test('reset password screen cannot be rendered', function () {
    $response = $this->get('/reset-password/fake-token');

    $response->assertStatus(404);
});

test('password cannot be reset', function () {
    $response = $this->post('/reset-password', [
        'token' => 'fake-token',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(404);
});
