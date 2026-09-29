<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin-Editable Game Settings
    |--------------------------------------------------------------------------
    |
    | Every setting the admin panel can change, in display order and grouped
    | into sections. Saved values live in the "settings" table; any setting
    | that has not been saved there uses the default defined below.
    |
    */

    'settings' => [

        'game_seconds' => [
            'group' => 'Game',
            'label' => 'Game time',
            'help' => 'How long players have to find all the pairs.',
            'type' => 'integer',
            'unit' => 'sec',
            'min' => 10,
            'max' => 300,
            'step' => 1,
            'default' => 45,
        ],

        'points_per_pair' => [
            'group' => 'Game',
            'label' => 'Points per pair',
            'help' => 'Score added for each matching pair found.',
            'type' => 'integer',
            'unit' => 'pts',
            'min' => 1,
            'max' => 100,
            'step' => 1,
            'default' => 2,
        ],

        'match_popup_seconds' => [
            'group' => 'Game',
            'label' => 'Match pop-up time',
            'help' => 'How long the “Match Found!” pop-up stays on screen. The clock pauses while it shows.',
            'type' => 'decimal',
            'unit' => 'sec',
            'min' => 0.5,
            'max' => 5,
            'step' => 0.1,
            'default' => 1.2,
        ],

        'idle_reset_seconds' => [
            'group' => 'Timeouts',
            'label' => 'Idle reset',
            'help' => 'Go back to the start screen after nobody touches the screen for this long. Never interrupts a running round.',
            'type' => 'integer',
            'unit' => 'sec',
            'min' => 15,
            'max' => 600,
            'step' => 1,
            'default' => 60,
        ],

        'result_screen_seconds' => [
            'group' => 'Timeouts',
            'label' => 'Result screen',
            'help' => 'Leave “Great Job!” or “Try Again!” automatically after this long. 0 waits for a tap.',
            'type' => 'integer',
            'unit' => 'sec',
            'min' => 0,
            'max' => 120,
            'step' => 1,
            'default' => 0,
        ],

        'end_screen_seconds' => [
            'group' => 'Timeouts',
            'label' => 'End screen',
            'help' => 'Return from the “Right dose. Brighter days.” screen to the start screen after this long. 0 waits for a tap.',
            'type' => 'integer',
            'unit' => 'sec',
            'min' => 0,
            'max' => 120,
            'step' => 1,
            'default' => 0,
        ],

        'music_volume' => [
            'group' => 'Sound',
            'label' => 'Music volume',
            'help' => 'Background music loudness.',
            'type' => 'integer',
            'unit' => '%',
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'default' => 40,
        ],

        'effects_volume' => [
            'group' => 'Sound',
            'label' => 'Effects volume',
            'help' => 'Loudness of the card flip, match and no-match sounds.',
            'type' => 'integer',
            'unit' => '%',
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'default' => 80,
        ],

    ],

];
