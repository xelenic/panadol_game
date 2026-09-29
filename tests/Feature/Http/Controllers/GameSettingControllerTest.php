<?php

use App\Models\Setting;

it('returns the current settings with defaults for the ones never saved', function () {
    Setting::factory()->gameSetting('music_volume', 10)->create();

    $response = $this->getJson(route('game.settings'));

    $response->assertExactJson([
        'game_seconds' => 45,
        'points_per_pair' => 2,
        'match_popup_seconds' => 1.2,
        'idle_reset_seconds' => 60,
        'result_screen_seconds' => 0,
        'end_screen_seconds' => 0,
        'music_volume' => 10,
        'effects_volume' => 80,
    ]);
});
