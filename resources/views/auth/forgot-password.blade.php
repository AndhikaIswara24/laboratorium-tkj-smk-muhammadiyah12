<x-guest-layout>
    <div class="text-center mb-6">
        <a href="/" class="inline-flex items-center justify-center p-2.5 rounded-full bg-white shadow-md border border-slate-100 ring-4 ring-emerald-500/10 transition-all duration-300 transform hover:scale-105 hover:shadow-xl hover:ring-emerald-500/20">
            <img src="{{ asset('img/logo-smk.svg') }}" alt="Logo SMK Muhammadiyah 12 Jakarta" class="h-16 w-16 object-contain">
        </a>
        <h1 class="mt-3 text-lg font-extrabold tracking-tight text-slate-900 leading-snug uppercase">
            LUPA PASSWORD
        </h1>
    </div>

    <div class="mb-4 text-xs font-medium text-slate-600 bg-slate-50 p-3.5 rounded-xl border border-slate-200 leading-relaxed">
        Lupa password Anda? Masukkan alamat email Anda di bawah ini dan kami akan mengirimkan tautan untuk menyetel ulang password Anda.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
