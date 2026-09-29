<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class GameSettingController extends Controller
{
    /**
     * Return the current game settings so a running kiosk can pick up changes
     * without reloading the page.
     */
    public function __invoke(): JsonResponse
    {
        return response()->json(Setting::gameValues());
    }
}
