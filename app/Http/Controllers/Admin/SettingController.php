<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Show the game settings form.
     */
    public function edit(): View
    {
        return view('admin.settings', [
            'groups' => collect(config('game.settings'))->groupBy('group', preserveKeys: true),
            'values' => Setting::gameValues(),
        ]);
    }

    /**
     * Save the game settings.
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        Setting::saveGameValues($request->validated());

        return to_route('admin.settings.edit')
            ->with('status', 'Settings saved. The game uses them from the next round.');
    }

    /**
     * Put every game setting back to its default.
     */
    public function destroy(): RedirectResponse
    {
        Setting::resetGameValues();

        return to_route('admin.settings.edit')
            ->with('status', 'All settings are back to their defaults.');
    }
}
