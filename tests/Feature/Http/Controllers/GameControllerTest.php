<?php

use App\Models\Setting;

it('renders the game with saved settings and defaults for the rest', function () {
    Setting::factory()->gameSetting('game_seconds', 90)->create();

    $response = $this->get(route('game'));

    $response
        ->assertSee('"game_seconds":90', escape: false)
        ->assertSee('"points_per_pair":2', escape: false);
});
