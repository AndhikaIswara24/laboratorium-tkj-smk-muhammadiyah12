<x-guest-layout>
    <div class="text-center mb-6">
        <a href="/" class="inline-flex items-center justify-center p-2.5 rounded-full bg-white shadow-md border border-slate-100 ring-4 ring-emerald-500/10 transition-all duration-300 transform hover:scale-105 hover:shadow-xl hover:ring-emerald-500/20">
            <img src="{{ asset('img/logo-smk.svg') }}" alt="Logo SMK Muhammadiyah 12 Jakarta" class="h-16 w-16 object-contain">
        </a>
        <h1 class="mt-3 text-lg font-extrabold tracking-tight text-slate-900 leading-snug uppercase">
            VERIFIKASI EMAIL
        </h1>
    </div>

    <div class="mb-4 text-xs font-medium text-slate-600 bg-slate-50 p-3.5 rounded-xl border border-slate-200 leading-relaxed">
        Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan ke email Anda.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
