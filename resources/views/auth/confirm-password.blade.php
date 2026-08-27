<x-guest-layout>
    <div class="text-center mb-6">
        <a href="/" class="inline-flex items-center justify-center p-2.5 rounded-full bg-white shadow-md border border-slate-100 ring-4 ring-emerald-500/10 transition-all duration-300 transform hover:scale-105 hover:shadow-xl hover:ring-emerald-500/20">
            <img src="{{ asset('img/logo-smk.svg') }}" alt="Logo SMK Muhammadiyah 12 Jakarta" class="h-16 w-16 object-contain">
        </a>
        <h1 class="mt-3 text-lg font-extrabold tracking-tight text-slate-900 leading-snug uppercase">
            KONFIRMASI PASSWORD
        </h1>
    </div>

    <div class="mb-4 text-xs font-medium text-slate-600 bg-slate-50 p-3.5 rounded-xl border border-slate-200 leading-relaxed">
        Ini adalah area terproteksi. Silakan konfirmasi password Anda sebelum melanjutkan.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
