<x-admin.layout title="Game settings">
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-semibold text-slate-900">Game settings</h1>
            <p class="text-sm text-slate-500">
                The kiosk picks up saved changes from the next round. No restart needed.
            </p>
        </div>

        @if (session('status'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                Some settings need fixing. Nothing was saved.
            </div>
        @endif

        <form id="settings-form" method="POST" action="{{ route('admin.settings.update') }}" class="flex flex-col gap-6">
            @csrf
            @method('PUT')

            @foreach ($groups as $group => $definitions)
                <section class="rounded-2xl bg-white shadow-sm ring-1 ring-pink-100">
                    <h2 class="border-b border-pink-100 px-5 py-4 font-semibold text-slate-900 sm:px-6">{{ $group }}</h2>

                    <div class="divide-y divide-slate-100">
                        @foreach ($definitions as $key => $definition)
                            <div class="grid gap-3 px-5 py-4 sm:grid-cols-[1fr_13rem] sm:items-start sm:gap-6 sm:px-6">
                                <div class="flex flex-col gap-1">
                                    <label for="{{ $key }}" class="font-medium text-slate-800">{{ $definition['label'] }}</label>
                                    <p class="text-sm text-slate-500">{{ $definition['help'] }}</p>
                                    <p class="text-xs text-slate-400">
                                        Default {{ $definition['default'] }} {{ $definition['unit'] }}
                                        · allowed {{ $definition['min'] }}–{{ $definition['max'] }}
                                    </p>
                                </div>

                                <div class="flex flex-col gap-1.5">
                                    <div @class([
                                        'flex items-center overflow-hidden rounded-lg border bg-white focus-within:ring-2 focus-within:ring-pink-500/40',
                                        'border-red-400' => $errors->has($key),
                                        'border-slate-300 focus-within:border-pink-500' => ! $errors->has($key),
                                    ])>
                                        <input id="{{ $key }}" name="{{ $key }}" type="number" inputmode="decimal" required
                                               min="{{ $definition['min'] }}" max="{{ $definition['max'] }}" step="{{ $definition['step'] }}"
                                               value="{{ old($key, $values[$key]) }}"
                                               @error($key) aria-invalid="true" aria-describedby="{{ $key }}-error" @enderror
                                               class="w-full min-w-0 px-3 py-2.5 text-right text-slate-900 tabular-nums outline-none">
                                        <span class="shrink-0 border-l border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-500">
                                            {{ $definition['unit'] }}
                                        </span>
                                    </div>
                                    @error($key)
                                        <p id="{{ $key }}-error" class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </form>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="POST" action="{{ route('admin.settings.destroy') }}"
                  onsubmit="return confirm('Reset every setting to its default?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full cursor-pointer rounded-lg px-4 py-2.5 text-sm font-medium text-slate-600 ring-1 ring-slate-300 hover:bg-white hover:text-slate-900 sm:w-auto">
                    Reset to defaults
                </button>
            </form>

            <button type="submit" form="settings-form"
                    class="cursor-pointer rounded-lg bg-pink-600 px-6 py-2.5 font-semibold text-white shadow-sm hover:bg-pink-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pink-600">
                Save settings
            </button>
        </div>
    </div>
</x-admin.layout>
