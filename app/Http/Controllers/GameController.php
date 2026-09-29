<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\View\View;

class GameController extends Controller
{
    /**
     * Show the Memory Match game with the current admin settings.
     */
    public function __invoke(): View
    {
        return view('game', [
            'settings' => Setting::gameValues(),
        ]);
    }
}
