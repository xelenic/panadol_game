<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:user {email} {--name=Admin} {--password=}', function (string $email) {
    $password = $this->option('password') ?: $this->secret('Password (at least 8 characters)');

    if (mb_strlen((string) $password) < 8) {
        $this->error('The password must be at least 8 characters.');

        return 1;
    }

    $user = User::query()->firstOrNew(['email' => $email]);

    if (! $user->exists) {
        $user->name = $this->option('name');
    }

    $user->password = $password;
    $user->save();

    $this->info($user->wasRecentlyCreated
        ? "Admin {$email} created. Sign in at ".route('admin.login')
        : "Password updated for {$email}.");

    return 0;
})->purpose('Create an admin panel account, or reset an existing account\'s password');
