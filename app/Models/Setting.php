<?php

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'json',
        ];
    }

    /**
     * Get the current value of every game setting, using the configured default
     * for any setting that has not been saved.
     *
     * @return array<string, int|float>
     */
    public static function gameValues(): array
    {
        $saved = static::query()->pluck('value', 'key');

        return collect(config('game.settings'))
            ->map(fn (array $definition, string $key): int|float => static::normalizeGameValue(
                $definition,
                $saved->get($key, $definition['default']),
            ))
            ->all();
    }

    /**
     * Save game settings, ignoring any key that is not a defined setting.
     *
     * @param  array<string, int|float|string>  $values
     */
    public static function saveGameValues(array $values): void
    {
        $definitions = config('game.settings');

        foreach (array_intersect_key($values, $definitions) as $key => $value) {
            static::query()->updateOrCreate(
                ['key' => $key],
                ['value' => static::normalizeGameValue($definitions[$key], $value)],
            );
        }
    }

    /**
     * Remove every saved game setting so all of them use their defaults again.
     */
    public static function resetGameValues(): void
    {
        static::query()->whereIn('key', array_keys(config('game.settings')))->delete();
    }

    /**
     * @param  array{type: string}  $definition
     */
    private static function normalizeGameValue(array $definition, mixed $value): int|float
    {
        return $definition['type'] === 'integer' ? (int) $value : round((float) $value, 2);
    }
}
