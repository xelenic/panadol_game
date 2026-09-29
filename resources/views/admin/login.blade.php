<x-admin.layout title="Sign in">
    <div class="mx-auto mt-6 max-w-sm sm:mt-16">
        <div class="mb-8 flex flex-col items-center gap-3 text-center">
            <img src="{{ asset('images/game/logo.png') }}" alt="Panadol for children" class="h-20 w-auto">
            <h1 class="text-2xl font-semibold text-slate-900">Memory Match admin</h1>
            <p class="text-sm text-slate-500">Sign in to change the game settings.</p>
        </div>

        <form method="POST" action="{{ route('admin.login.store') }}"
              class="flex flex-col gap-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pink-100 sm:p-8">
            @csrf

            <div class="flex flex-col gap-1.5">
                <label for="email" class="text-sm font-medium text-slate-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       @class([
                           'rounded-lg border px-3 py-2.5 text-slate-900 outline-none focus:ring-2 focus:ring-pink-500/40',
                           'border-red-400' => $errors->has('email'),
                           'border-slate-300 focus:border-pink-500' => ! $errors->has('email'),
                       ])>
                @error('email')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="password" class="text-sm font-medium text-slate-700">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                       class="rounded-lg border border-slate-300 px-3 py-2.5 text-slate-900 outline-none focus:border-pink-500 focus:ring-2 focus:ring-pink-500/40">
                @error('password')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1" class="size-4 rounded accent-pink-600">
                Keep me signed in
            </label>

            <button type="submit"
                    class="cursor-pointer rounded-lg bg-pink-600 px-4 py-2.5 font-semibold text-white shadow-sm hover:bg-pink-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pink-600">
                Sign in
            </button>
        </form>
    </div>
</x-admin.layout>
