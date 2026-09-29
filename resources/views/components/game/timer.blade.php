<div {{ $attributes->merge(['class' => 'timer']) }} role="timer">
    <span class="timer-clock" aria-hidden="true">
        <svg viewBox="0 0 48 48">
            <rect x="19" y="1" width="10" height="6" rx="2" fill="#e21d48"/>
            <rect x="33" y="5" width="7" height="5" rx="2" fill="#e21d48" transform="rotate(40 36 8)"/>
            <circle cx="24" cy="27" r="19" fill="#e21d48"/>
            <circle cx="24" cy="27" r="14" fill="#fff"/>
            <path d="M24 18v9l-6 4" fill="none" stroke="#e21d48" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </span>
    <span class="timer-value" data-timer>00:45</span>
</div>
