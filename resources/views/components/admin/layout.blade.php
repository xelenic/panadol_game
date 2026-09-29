@props(['title'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-pink-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">

        <title>{{ $title }} · Memory Match Admin</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full font-sans text-slate-800 antialiased">
        @auth
            <header class="border-b border-pink-100 bg-white">
                <div class="mx-auto flex max-w-4xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
                    <a href="{{ route('admin.settings.edit') }}" class="flex min-w-0 items-center gap-3">
                        <img src="{{ asset('images/game/logo.png') }}" alt="" class="h-9 w-auto">
                        <span class="hidden truncate font-semibold text-slate-900 sm:inline">Memory Match</span>
                        <span class="rounded-full bg-pink-100 px-2 py-0.5 text-xs font-medium text-pink-700">Admin</span>
                    </a>

                    <nav class="flex shrink-0 items-center gap-1 text-sm">
                        <a href="{{ route('game') }}" target="_blank" rel="noopener"
                           class="rounded-lg px-3 py-2 font-medium text-slate-600 hover:bg-pink-50 hover:text-pink-700">
                            Open game <span aria-hidden="true">↗</span>
                        </a>

                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit"
                                    class="cursor-pointer rounded-lg px-3 py-2 font-medium text-slate-600 hover:bg-pink-50 hover:text-pink-700">
                                Sign out
                            </button>
                        </form>
                    </nav>
                </div>
            </header>
        @endauth

        <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6 sm:py-10">
            {{ $slot }}
        </main>
    </body>
</html>
