const config = window.GAME_CONFIG;

const ICONS = {
    flame: '<svg viewBox="0 0 24 24"><path fill="#fff" d="M12 2c1 3.5 5 6 5 11a5 5 0 0 1-10 0c0-2.2 1-3.7 2-4.8.2 1.6 1 2.6 2 3 0-3.2-.5-6.2 1-9.2z"/><path fill="#f5357a" d="M12 12c.7 1.4 2.2 2.3 2.2 4.2a2.2 2.2 0 0 1-4.4 0c0-1.3.9-2.3 2.2-4.2z"/></svg>',
    thermometer: '<svg viewBox="0 0 24 24"><path fill="#fff" d="M10 4a2 2 0 1 1 4 0v9.3a4.5 4.5 0 1 1-4 0z"/><circle cx="12" cy="17" r="2.4" fill="#f5357a"/><rect x="11.1" y="7" width="1.8" height="9" rx=".9" fill="#f5357a"/></svg>',
    heart: '<svg viewBox="0 0 24 24"><path fill="#fff" d="M12 21s-8-5.2-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 5.8-8 11-8 11z"/></svg>',
    sun: '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="5" fill="#fff"/><g stroke="#fff" stroke-width="2.2" stroke-linecap="round"><path d="M12 2v2.5M12 19.5V22M2 12h2.5M19.5 12H22M4.9 4.9l1.8 1.8M17.3 17.3l1.8 1.8M4.9 19.1l1.8-1.8M17.3 6.7l1.8-1.8"/></g></svg>',
    drop: '<svg viewBox="0 0 24 24"><path fill="#fff" d="M12 2.5S5.5 10 5.5 14.5a6.5 6.5 0 0 0 13 0C18.5 10 12 2.5 12 2.5z"/><path fill="none" stroke="#f5357a" stroke-width="1.8" stroke-linecap="round" d="M9 15a3 3 0 0 0 3 3"/></svg>',
};

const $ = (selector, root = document) => root.querySelector(selector);
const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];

const board = $('#board');
const matchOverlay = $('#match-overlay');
const pauseOverlay = $('#pause-overlay');

const state = {
    timeLeft: config.settings.game_seconds,
    score: 0,
    pairsFound: 0,
    open: [],
    locked: false,
    paused: false,
    timerId: null,
};

// ───────── Sound ─────────

const sound = (() => {
    const music = new Audio(config.sounds.music);
    music.loop = true;
    music.volume = config.settings.music_volume / 100;
    music.preload = 'auto';

    // Web Audio for effects: no delay and overlapping plays on quick taps.
    const AudioCtx = window.AudioContext || window.webkitAudioContext;
    const ctx = AudioCtx ? new AudioCtx() : null;
    const buffers = {};

    if (ctx) {
        Object.entries(config.sounds.effects).forEach(([name, url]) => {
            fetch(url)
                .then((response) => response.arrayBuffer())
                .then((data) => ctx.decodeAudioData(data))
                .then((buffer) => (buffers[name] = buffer))
                .catch(() => {});
        });
    }

    let musicWanted = true;
    let muted = false;

    try {
        muted = localStorage.getItem('memory-match:muted') === '1';
    } catch {}

    music.muted = muted;

    function playMusic() {
        if (musicWanted) {
            music.play().catch(() => {});
        }
    }

    // Browsers block audio until the first touch (unless the kiosk runs Chrome with
    // --autoplay-policy=no-user-gesture-required), so retry on the first interaction.
    playMusic();
    const unlock = () => {
        ctx?.resume();
        playMusic();
    };
    document.addEventListener('pointerdown', unlock, { passive: true });
    document.addEventListener('keydown', unlock);

    return {
        get muted() {
            return muted;
        },
        setMuted(value) {
            muted = value;
            music.muted = value;

            try {
                localStorage.setItem('memory-match:muted', value ? '1' : '0');
            } catch {}
        },
        play(name) {
            if (muted) {
                return;
            }

            if (!ctx || !buffers[name]) {
                const fallback = new Audio(config.sounds.effects[name]);
                fallback.volume = config.settings.effects_volume / 100;
                fallback.play().catch(() => {});
                return;
            }

            const source = ctx.createBufferSource();
            const gain = ctx.createGain();
            gain.gain.value = config.settings.effects_volume / 100;
            source.buffer = buffers[name];
            source.connect(gain).connect(ctx.destination);
            source.start();
        },
        pauseMusic() {
            musicWanted = false;
            music.pause();
        },
        resumeMusic() {
            musicWanted = true;
            playMusic();
        },
        applyVolume() {
            music.volume = config.settings.music_volume / 100;
        },
    };
})();

// Start screen ambience: hearts drifting up and sparkles twinkling around the title.
function buildFloaties() {
    const rand = (min, max) => min + Math.random() * (max - min);
    const colors = ['#ffffff', '#ff8fb1', '#ffc2d4', '#ff5c93'];

    const hearts = Array.from({ length: 14 }, () => {
        const style = [
            `left:${rand(0, 95)}%`,
            `--size:${rand(16, 40)}px`,
            `--color:${colors[Math.floor(Math.random() * colors.length)]}`,
            `--alpha:${rand(0.5, 0.9)}`,
            `--drift:${rand(-50, 50)}px`,
            `--dur:${rand(7, 13)}s`,
            `--delay:${-rand(0, 13)}s`,
        ].join(';');

        return `<i class="heart" style="${style}"></i>`;
    });

    const sparkles = Array.from({ length: 10 }, () => {
        const style = [
            `left:${rand(4, 92)}%`,
            `top:${rand(4, 45)}%`,
            `--size:${rand(12, 26)}px`,
            `--dur:${rand(1.8, 3.2)}s`,
            `--delay:${-rand(0, 3)}s`,
        ].join(';');

        return `<i class="sparkle" style="${style}"></i>`;
    });

    $('#floaties').innerHTML = hearts.join('') + sparkles.join('');
}

buildFloaties();

// Paint the icon placeholder on the how-to screen.
$$('.mini-icon[data-icon]').forEach((el) => (el.innerHTML = ICONS[el.dataset.icon]));

function faceMarkup(face) {
    return face.image ? `<img src="${face.image}" alt="" draggable="false">` : ICONS[face.icon];
}

// Screens that move on by themselves after an admin-set number of seconds (0 = wait for a tap).
const AUTO_ADVANCE = {
    win: { setting: 'result_screen_seconds', next: 'end' },
    lose: { setting: 'result_screen_seconds', next: 'start' },
    end: { setting: 'end_screen_seconds', next: 'start' },
};

let autoAdvanceId = null;

function show(name) {
    clearTimeout(autoAdvanceId);
    $$('.screen').forEach((screen) => screen.classList.toggle('is-active', screen.dataset.screen === name));

    const auto = AUTO_ADVANCE[name];
    const seconds = auto ? config.settings[auto.setting] : 0;

    if (seconds > 0) {
        autoAdvanceId = setTimeout(() => show(auto.next), seconds * 1000);
    }

    // Pick up changes saved in the admin panel before the next round starts.
    if (name === 'start' || name === 'howto') {
        refreshSettings();
    }
}

function refreshSettings() {
    fetch(config.settingsUrl, { headers: { Accept: 'application/json' }, cache: 'no-store' })
        .then((response) => (response.ok ? response.json() : null))
        .then((settings) => settings && applySettings(settings))
        .catch(() => {});
}

function applySettings(settings) {
    Object.assign(config.settings, settings);
    sound.applyVolume();

    // A round already running keeps its clock; otherwise show the new game time.
    if (!state.timerId) {
        state.timeLeft = config.settings.game_seconds;
        renderHud();
    }

    resetIdle();
}

function formatTime(seconds) {
    const remaining = Math.max(0, seconds);

    return `${String(Math.floor(remaining / 60)).padStart(2, '0')}:${String(remaining % 60).padStart(2, '0')}`;
}

function renderHud() {
    $$('[data-timer]').forEach((el) => (el.textContent = formatTime(state.timeLeft)));
    $$('[data-score]').forEach((el) => (el.textContent = state.score));
    $$('.timer').forEach((el) => el.classList.toggle('is-low', state.timeLeft <= 10 && state.timeLeft > 0));
}

function shuffle(items) {
    for (let i = items.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [items[i], items[j]] = [items[j], items[i]];
    }

    return items;
}

function buildBoard() {
    const deck = shuffle([...config.faces, ...config.faces]);

    board.innerHTML = deck
        .map(
            (face) => `
            <button class="card" data-id="${face.id}" aria-label="Hidden card">
                <span class="card-back"><img src="${config.logo}" alt="" draggable="false"></span>
                <span class="card-face"${face.icon ? ` data-icon="${face.icon}"` : ''}>${faceMarkup(face)}</span>
            </button>`,
        )
        .join('');
}

function startTimer() {
    stopTimer();
    state.timerId = setInterval(() => {
        if (state.paused) {
            return;
        }

        state.timeLeft--;
        renderHud();

        if (state.timeLeft <= 0) {
            finish(false);
        }
    }, 1000);
}

function stopTimer() {
    clearInterval(state.timerId);
    state.timerId = null;
}

function startGame() {
    Object.assign(state, {
        timeLeft: config.settings.game_seconds,
        score: 0,
        pairsFound: 0,
        open: [],
        locked: false,
        paused: false,
    });

    matchOverlay.classList.remove('is-open');
    pauseOverlay.classList.remove('is-open');
    sound.resumeMusic();
    buildBoard();
    renderHud();
    show('game');
    startTimer();
}

function flip(card) {
    if (state.locked || state.paused || card.classList.contains('is-flipped') || card.classList.contains('is-matched')) {
        return;
    }

    card.classList.add('is-flipped');
    sound.play('flip');
    state.open.push(card);

    if (state.open.length < 2) {
        return;
    }

    state.locked = true;
    const [a, b] = state.open;

    if (a.dataset.id === b.dataset.id) {
        setTimeout(() => onMatch(a, b), 450);
    } else {
        setTimeout(() => {
            a.classList.add('is-wrong');
            b.classList.add('is-wrong');
            sound.play('miss');
        }, 450);
        setTimeout(() => {
            a.classList.remove('is-flipped', 'is-wrong');
            b.classList.remove('is-flipped', 'is-wrong');
            state.open = [];
            state.locked = false;
        }, 1000);
    }
}

function onMatch(a, b) {
    a.classList.replace('is-flipped', 'is-matched');
    b.classList.replace('is-flipped', 'is-matched');
    a.setAttribute('aria-label', 'Matched card');
    b.setAttribute('aria-label', 'Matched card');

    sound.play('match');
    state.pairsFound++;
    state.score += config.settings.points_per_pair;
    state.open = [];
    renderHud();

    const score = $('.screen-game .score');
    score.classList.remove('bump');
    void score.offsetWidth;
    score.classList.add('bump');

    // "Match Found!" celebration — the clock stops while it's on screen.
    const face = config.faces.find((f) => f.id === a.dataset.id);
    const tile = `<div${face.icon ? ` data-icon="${face.icon}"` : ''}>${faceMarkup(face)}</div>`;
    $('.match-pair', matchOverlay).innerHTML = tile + tile;
    matchOverlay.classList.add('is-open');
    state.paused = true;

    setTimeout(() => {
        matchOverlay.classList.remove('is-open');
        state.paused = false;
        state.locked = false;

        if (state.pairsFound === config.faces.length) {
            finish(true);
        }
    }, config.settings.match_popup_seconds * 1000);
}

function finish(won) {
    stopTimer();
    state.locked = true;
    renderHud();

    if (won) {
        launchConfetti();
        show('win');
        return;
    }

    $('#lose-text').innerHTML = state.pairsFound
        ? 'You found some pairs.<br>Can you do better?'
        : 'Time’s up!<br>Can you do better?';
    show('lose');
}

function launchConfetti() {
    const colors = ['#ff4d8d', '#ffd93b', '#3bc9ff', '#7ed957', '#b86bff', '#ff8a3b'];
    $('#confetti').innerHTML = Array.from({ length: 60 }, () => {
        const style = [
            `left:${Math.random() * 100}%`,
            `background:${colors[Math.floor(Math.random() * colors.length)]}`,
            `animation-duration:${2.5 + Math.random() * 3}s`,
            `animation-delay:${-Math.random() * 5}s`,
            `transform:rotate(${Math.random() * 360}deg)`,
        ].join(';');

        return `<i style="${style}"></i>`;
    }).join('');
}

function renderSoundButton() {
    const button = $('.btn-sound');
    button.classList.toggle('is-muted', sound.muted);
    button.setAttribute('aria-pressed', String(!sound.muted));
    button.setAttribute('aria-label', sound.muted ? 'Turn sound on' : 'Turn sound off');
}

function setPaused(paused) {
    state.paused = paused;
    pauseOverlay.classList.toggle('is-open', paused);
    paused ? sound.pauseMusic() : sound.resumeMusic();
}

board.addEventListener('click', (event) => {
    const card = event.target.closest('.card');

    if (card) {
        flip(card);
    }
});

const actions = {
    'how-to': () => show('howto'),
    play: startGame,
    replay: startGame,
    pause: () => setPaused(true),
    resume: () => setPaused(false),
    restart: startGame,
    'toggle-sound': () => {
        sound.setMuted(!sound.muted);
        renderSoundButton();
    },
    finish: () => show('end'),
};

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-action]');

    if (button) {
        actions[button.dataset.action]?.();
    }
});

// Auto-pause when the player switches away from the tab.
document.addEventListener('visibilitychange', () => {
    if (document.hidden && state.timerId && !state.locked) {
        setPaused(true);
    }
});

// ───────── Kiosk behaviour ─────────

// No long-press menus, image drags or pinch-zoom on the touchscreen.
document.addEventListener('contextmenu', (event) => event.preventDefault());
document.addEventListener('dragstart', (event) => event.preventDefault());
document.addEventListener('gesturestart', (event) => event.preventDefault());

// Return to the attract (start) screen when the kiosk is left alone,
// except while a round is actively running.
let idleId = null;

function resetIdle() {
    clearTimeout(idleId);
    idleId = setTimeout(() => {
        const playing = $('[data-screen="game"]').classList.contains('is-active') && !state.paused && state.timerId;

        if (playing) {
            resetIdle();
            return;
        }

        stopTimer();
        pauseOverlay.classList.remove('is-open');
        sound.resumeMusic();
        state.paused = false;
        show('start');
    }, config.settings.idle_reset_seconds * 1000);
}

['pointerdown', 'keydown'].forEach((type) => document.addEventListener(type, resetIdle, { passive: true }));
resetIdle();

renderSoundButton();
renderHud();
