<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Every account in this application is an admin (accounts are only created
     * with the admin:user command), so being signed in is the permission.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return collect(config('game.settings'))
            ->map(fn (array $definition): array => [
                'required',
                $definition['type'] === 'integer' ? 'integer' : 'numeric',
                'between:'.$definition['min'].','.$definition['max'],
            ])
            ->all();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return collect(config('game.settings'))
            ->map(fn (array $definition): string => Str::lower($definition['label']))
            ->all();
    }
}
