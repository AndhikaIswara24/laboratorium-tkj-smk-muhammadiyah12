<x-guest-layout>
    <div class="text-center mb-6">
        <a href="/" class="inline-flex items-center justify-center p-2.5 rounded-full bg-white shadow-md border border-slate-100 ring-4 ring-emerald-500/10 transition-all duration-300 transform hover:scale-105 hover:shadow-xl hover:ring-emerald-500/20">
            <img src="{{ asset('img/logo-smk.svg') }}" alt="Logo SMK Muhammadiyah 12 Jakarta" class="h-16 w-16 object-contain">
        </a>
        <h1 class="mt-3 text-lg font-extrabold tracking-tight text-slate-900 leading-snug uppercase">
            SETEL ULANG PASSWORD
        </h1>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
