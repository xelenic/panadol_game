<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => 'game_seconds',
            'value' => fake()->numberBetween(10, 300),
        ];
    }

    /**
     * A saved value for the given game setting.
     */
    public function gameSetting(string $key, int|float $value): static
    {
        return $this->state(fn (): array => [
            'key' => $key,
            'value' => $value,
        ]);
    }
}
