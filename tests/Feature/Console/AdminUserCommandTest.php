<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('creates an admin who can sign in with the given password', function () {
    $this->artisan('admin:user', ['email' => 'kiosk@example.com', '--password' => 'secret-pass'])
        ->expectsOutputToContain('Admin kiosk@example.com created.')
        ->assertSuccessful();

    $user = User::query()->where('email', 'kiosk@example.com')->sole();
    expect($user->name)->toBe('Admin');
    expect(Hash::check('secret-pass', $user->password))->toBeTrue();
});

it('asks for the password when none is given', function () {
    $this->artisan('admin:user', ['email' => 'kiosk@example.com'])
        ->expectsQuestion('Password (at least 8 characters)', 'prompted-pass')
        ->assertSuccessful();

    $user = User::query()->where('email', 'kiosk@example.com')->sole();
    expect(Hash::check('prompted-pass', $user->password))->toBeTrue();
});

it('resets the password of an existing admin and keeps their name', function () {
    $user = User::factory()->create(['email' => 'kiosk@example.com', 'name' => 'Kiosk Manager']);

    $this->artisan('admin:user', ['email' => 'kiosk@example.com', '--password' => 'new-secret-pass'])
        ->expectsOutput('Password updated for kiosk@example.com.')
        ->assertSuccessful();

    $user->refresh();
    expect($user->name)->toBe('Kiosk Manager');
    expect(Hash::check('new-secret-pass', $user->password))->toBeTrue();
});

it('refuses a password shorter than 8 characters', function () {
    $this->artisan('admin:user', ['email' => 'kiosk@example.com', '--password' => 'short'])
        ->expectsOutput('The password must be at least 8 characters.')
        ->assertFailed();

    $this->assertDatabaseCount('users', 0);
});
