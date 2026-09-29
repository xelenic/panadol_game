<?php

use App\Models\Setting;
use App\Models\User;

/**
 * A complete, valid settings form submission with every value changed from its default.
 *
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function changedSettings(array $overrides = []): array
{
    return [
        'game_seconds' => 90,
        'points_per_pair' => 5,
        'match_popup_seconds' => 2.5,
        'idle_reset_seconds' => 120,
        'result_screen_seconds' => 10,
        'end_screen_seconds' => 8,
        'music_volume' => 25,
        'effects_volume' => 60,
        ...$overrides,
    ];
}

describe('edit', function () {
    it('shows saved values and defaults for settings that were never saved', function () {
        Setting::factory()->gameSetting('game_seconds', 90)->create();

        $response = $this->actingAs(User::factory()->create())->get(route('admin.settings.edit'));

        $response
            ->assertSee('Game time')
            ->assertViewHas('values', fn (array $values): bool => $values['game_seconds'] === 90
                && $values['points_per_pair'] === 2);
    });
});

describe('update', function () {
    it('saves every setting', function () {
        $response = $this->actingAs(User::factory()->create())
            ->put(route('admin.settings.update'), changedSettings());

        $response
            ->assertRedirect(route('admin.settings.edit'))
            ->assertSessionHas('status', 'Settings saved. The game uses them from the next round.');
        expect(Setting::gameValues())->toBe(changedSettings());
    });

    it('rejects a value outside what the game allows', function (string $key, mixed $value, string $message) {
        $response = $this->actingAs(User::factory()->create())
            ->put(route('admin.settings.update'), changedSettings([$key => $value]));

        $response->assertSessionHasErrors([$key => $message]);
        $this->assertDatabaseCount('settings', 0);
    })->with([
        'game time under 10 seconds' => ['game_seconds', 5, 'The game time field must be between 10 and 300.'],
        'idle reset under 15 seconds' => ['idle_reset_seconds', 10, 'The idle reset field must be between 15 and 600.'],
        'music volume over 100%' => ['music_volume', 150, 'The music volume field must be between 0 and 100.'],
        'negative end screen time' => ['end_screen_seconds', -1, 'The end screen field must be between 0 and 120.'],
        'fractional points' => ['points_per_pair', 2.5, 'The points per pair field must be an integer.'],
        'text instead of a number' => ['match_popup_seconds', 'soon', 'The match pop-up time field must be a number.'],
    ]);

    it('requires every setting', function () {
        $response = $this->actingAs(User::factory()->create())->put(route('admin.settings.update'), []);

        $response->assertSessionHasErrors([
            'game_seconds' => 'The game time field is required.',
            'points_per_pair' => 'The points per pair field is required.',
            'match_popup_seconds' => 'The match pop-up time field is required.',
            'idle_reset_seconds' => 'The idle reset field is required.',
            'result_screen_seconds' => 'The result screen field is required.',
            'end_screen_seconds' => 'The end screen field is required.',
            'music_volume' => 'The music volume field is required.',
            'effects_volume' => 'The effects volume field is required.',
        ]);
        $this->assertDatabaseCount('settings', 0);
    });

    it('does not save keys that are not game settings', function () {
        $this->actingAs(User::factory()->create())
            ->put(route('admin.settings.update'), changedSettings(['is_admin' => 1]));

        $this->assertDatabaseMissing('settings', ['key' => 'is_admin']);
        $this->assertDatabaseCount('settings', 8);
    });

    it('does not let guests change settings', function () {
        $response = $this->put(route('admin.settings.update'), changedSettings());

        $response->assertRedirect(route('admin.login'));
        $this->assertDatabaseCount('settings', 0);
    });
});

describe('destroy', function () {
    it('puts every setting back to its default', function () {
        Setting::factory()->gameSetting('game_seconds', 90)->create();
        Setting::factory()->gameSetting('music_volume', 10)->create();

        $response = $this->actingAs(User::factory()->create())->delete(route('admin.settings.destroy'));

        $response
            ->assertRedirect(route('admin.settings.edit'))
            ->assertSessionHas('status', 'All settings are back to their defaults.');
        $this->assertDatabaseCount('settings', 0);
    });
});
