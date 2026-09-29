<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, user-scalable=no">
        <meta name="theme-color" content="#e8175d">

        <title>Panadol for Children – Memory Match</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fredoka:500,600,700&display=swap" rel="stylesheet">

        <script>
            // Fit the 540×960 design canvas to the screen (2× on a 1080×1920 kiosk).
            (function () {
                function fit() {
                    document.documentElement.style.setProperty('--scale', Math.min(innerWidth / 540, innerHeight / 960));
                }
                fit();
                addEventListener('resize', fit);
            })();
        </script>

        @vite(['resources/css/game.css', 'resources/js/game.js'])
    </head>
    <body>
        <main class="stage" id="stage">
            <div class="bg" aria-hidden="true">
                <div class="bg-clouds"></div>
                <div class="bg-bushes"></div>
            </div>

            <button class="btn-sound" data-action="toggle-sound" aria-label="Turn sound off" aria-pressed="true">
                <svg class="sound-on" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 9h4l5-4v14l-5-4H4z" fill="currentColor" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    <path d="M16 9a4 4 0 0 1 0 6M18.5 6.5a7.5 7.5 0 0 1 0 11" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                </svg>
                <svg class="sound-off" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 9h4l5-4v14l-5-4H4z" fill="currentColor" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    <path d="M16.5 9.5l5 5M21.5 9.5l-5 5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                </svg>
            </button>

            {{-- 01 · Start --}}
            <section class="screen screen-start is-active" data-screen="start">
                <div class="floaties" id="floaties" aria-hidden="true"></div>
                <span class="logo-wrap">
                    <img class="logo" src="{{ asset('images/game/logo.png') }}" alt="Panadol for children">
                </span>
                <h1 class="title" aria-label="Memory Match">
                    @foreach (['Memory', 'Match'] as $w => $word)
                        <span class="word" aria-hidden="true">
                            @foreach (mb_str_split($word) as $i => $letter)
                                <span class="ch" style="--i: {{ $w * 6 + $i }}">{{ $letter }}</span>
                            @endforeach
                        </span>
                    @endforeach
                    <span class="title-cards" aria-hidden="true"><i></i><i></i></span>
                </h1>
                <p class="tagline">Find the matching pairs<br>and win!</p>
                <img class="hero" src="{{ asset('images/game/family.png') }}" alt="">
                <button class="btn btn-primary btn-start btn-float" data-action="how-to">Start <x-game.chevron /></button>
            </section>

            {{-- 02 · How to play --}}
            <section class="screen screen-howto" data-screen="howto">
                <x-game.timer class="timer-center" />
                <div class="panel">
                    <h2 class="panel-head">How to Play</h2>
                    <ol class="steps">
                        <li><b>1</b><span>Find the matching pairs of cards.</span></li>
                        <li><b>2</b><span>Remember the images and tap to match.</span></li>
                        <li><b>3</b><span>Complete all pairs before the time runs out!</span></li>
                    </ol>
                    <div class="howto-cards" aria-hidden="true">
                        <span class="dash dash-l"></span>
                        <div class="mini-card mini-face"><img src="{{ asset('images/game/card-bottle.png') }}" alt=""></div>
                        <div class="mini-card mini-icon" data-icon="flame"></div>
                        <span class="dash dash-r"></span>
                    </div>
                </div>
                <button class="btn btn-primary" data-action="play">Let’s Go <x-game.chevron /></button>
            </section>

            {{-- 03–06 · Game board --}}
            <section class="screen screen-game" data-screen="game">
                <x-game.timer />
                <div class="banner" aria-hidden="true"><span>Find the Matching Pairs!</span></div>
                <div class="board" id="board" role="grid" aria-label="Memory cards"></div>
                <div class="hud">
                    <x-game.score />
                    <button class="btn-pause" data-action="pause" aria-label="Pause">
                        <svg viewBox="0 0 24 24"><rect x="6" y="5" width="4" height="14" rx="2"/><rect x="14" y="5" width="4" height="14" rx="2"/></svg>
                    </button>
                </div>

                {{-- 04 · Match found --}}
                <div class="overlay overlay-match" id="match-overlay" aria-live="polite">
                    <div class="rays"></div>
                    <div class="match-pair"></div>
                    <div class="ribbon"><span>Match Found!</span></div>
                </div>

                <div class="overlay overlay-pause" id="pause-overlay">
                    <div class="panel panel-small">
                        <h2 class="panel-head">Paused</h2>
                        <div class="pause-actions">
                            <button class="btn btn-primary" data-action="resume">Resume <x-game.chevron /></button>
                            <button class="btn btn-ghost" data-action="restart">Restart</button>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 07 · Win --}}
            <section class="screen screen-win" data-screen="win">
                <div class="confetti" id="confetti" aria-hidden="true"></div>
                <x-game.timer class="timer-center timer-big" />
                <div class="result-badge">
                    <div class="stars" aria-hidden="true"><x-game.star /><x-game.star /><x-game.star /></div>
                    <h2>Great Job!</h2>
                    <p>You found all the pairs!</p>
                </div>
                <x-game.score class="score-center" />
                <img class="hero hero-win" src="{{ asset('images/game/family-happy.png') }}" alt="">
                <button class="btn btn-primary btn-float" data-action="finish">Continue <x-game.chevron /></button>
            </section>

            {{-- 08 · Lose --}}
            <section class="screen screen-lose" data-screen="lose">
                <x-game.timer />
                <div class="result-badge result-lose">
                    <img class="sad" src="{{ asset('images/game/sad-boy.png') }}" alt="">
                    <h2>Try Again!</h2>
                </div>
                <p class="tagline" id="lose-text">You found some pairs.<br>Can you do better?</p>
                <x-game.score class="score-center" />
                <button class="btn btn-primary" data-action="replay">Play Again
                    <svg class="icon" viewBox="0 0 24 24"><path d="M20 12a8 8 0 1 1-2.34-5.66" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/><path d="M20 3v5h-5" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </section>

            {{-- 09 · Brand end card --}}
            <section class="screen screen-end" data-screen="end">
                <img class="end-art" src="{{ asset('images/game/brand-end.jpg') }}" alt="Right dose. Brighter days.">
                <button class="btn btn-primary btn-float" data-action="replay">Play Again
                    <svg class="icon" viewBox="0 0 24 24"><path d="M20 12a8 8 0 1 1-2.34-5.66" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/><path d="M20 3v5h-5" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </section>
        </main>

        <script>
            window.GAME_CONFIG = {
                // Editable in the admin panel (config/game.php lists them all).
                settings: @json($settings),
                settingsUrl: @json(route('game.settings')),
                sounds: {
                    music: @json(asset('sound/background.mp3')),
                    effects: {
                        flip: @json(asset('sound/coin.mp3')),
                        match: @json(asset('sound/mached.mp3')),
                        miss: @json(asset('sound/notmached.mp3')),
                    },
                },
                logo: @json(asset('images/game/logo.png')),
                faces: [
                    { id: 'bottle', image: @json(asset('images/game/card-bottle.png')) },
                    { id: 'box', image: @json(asset('images/game/card-box.png')) },
                    { id: 'cup', image: @json(asset('images/game/card-cup-full.png')) },
                    { id: 'flame', icon: 'flame' },
                    { id: 'thermometer', icon: 'thermometer' },
                    { id: 'heart', icon: 'heart' },
                    { id: 'sun', icon: 'sun' },
                    { id: 'drop', icon: 'drop' },
                ],
            };
        </script>
    </body>
</html>
