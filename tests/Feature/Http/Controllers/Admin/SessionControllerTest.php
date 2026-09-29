<?php

use App\Models\User;

it('shows the sign-in form to guests', function () {
    $response = $this->get(route('admin.login'));

    $response->assertSee('Sign in to change the game settings.');
});

it('redirects guests from the settings page to the sign-in form', function () {
    $response = $this->get(route('admin.settings.edit'));

    $response->assertRedirect(route('admin.login'));
});

it('redirects signed-in admins away from the sign-in form', function () {
    $response = $this->actingAs(User::factory()->create())->get(route('admin.login'));

    $response->assertRedirect(route('admin.settings.edit'));
});

it('signs an admin in and sends them to the settings page', function () {
    $user = User::factory()->create();

    $response = $this->post(route('admin.login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.settings.edit'));
    $this->assertAuthenticatedAs($user);
});

it('rejects a wrong password', function () {
    $user = User::factory()->create();

    $response = $this->from(route('admin.login'))->post(route('admin.login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response
        ->assertRedirect(route('admin.login'))
        ->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);
    $this->assertGuest();
});

it('requires an email and a password', function () {
    $response = $this->post(route('admin.login.store'), []);

    $response->assertSessionHasErrors([
        'email' => 'The email field is required.',
        'password' => 'The password field is required.',
    ]);
    $this->assertGuest();
});

it('blocks sign-in after five failed attempts in a minute', function () {
    $user = User::factory()->create();

    foreach (range(1, 5) as $attempt) {
        $this->post(route('admin.login.store'), ['email' => $user->email, 'password' => 'wrong-password']);
    }

    $response = $this->post(route('admin.login.store'), ['email' => $user->email, 'password' => 'password']);

    $response->assertSessionHasErrors(['email' => 'Too many sign-in attempts. Please wait a minute and try again.']);
    $this->assertGuest();
});

it('signs the admin out', function () {
    $response = $this->actingAs(User::factory()->create())->post(route('admin.logout'));

    $response->assertRedirect(route('admin.login'));
    $this->assertGuest();
});
