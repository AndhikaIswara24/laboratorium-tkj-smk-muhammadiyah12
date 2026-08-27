<x-guest-layout>
    <div class="text-center mb-6">
        <!-- Logo SMK Muhammadiyah 12 -->
        <a href="/" class="inline-flex items-center justify-center p-3 rounded-2xl bg-white shadow-md border border-slate-100 ring-4 ring-emerald-500/10 transition-all duration-300 transform hover:scale-105 hover:shadow-xl hover:ring-emerald-500/20">
            <img src="{{ asset('img/logo-smk.svg') }}" alt="Logo SMK Muhammadiyah 12 Jakarta" class="h-24 w-24 object-contain">
        </a>

        <!-- Title & Subtitle Matching Wireframe -->
        <h1 class="mt-4 text-xl font-extrabold tracking-tight text-slate-900 leading-snug uppercase">
            BUAT AKUN BARU
        </h1>
        <p class="mt-2 text-xs font-semibold text-emerald-700 bg-emerald-50 inline-block px-3 py-1 rounded-full border border-emerald-200/80">
            Sistem Inventaris Lab TKJ - SMK Muhammadiyah 12
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
        @csrf

        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-sm font-bold text-slate-800 mb-1">
                Nama Lengkap
            </label>
            <input 
                id="name" 
                type="text" 
                name="name" 
                value="{{ old('name') }}" 
                placeholder="Masukkan nama lengkap"
                required 
                autofocus 
                autocomplete="name"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-rose-600" />
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-bold text-slate-800 mb-1">
                Email
            </label>
            <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                placeholder="nama@email.com"
                required 
                autocomplete="username"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-600" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-bold text-slate-800 mb-1">
                Password
            </label>
            <input 
                id="password" 
                type="password" 
                name="password" 
                placeholder="Minimal 8 karakter"
                required 
                autocomplete="new-password"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-600" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-bold text-slate-800 mb-1">
                Confirm Password
            </label>
            <input 
                id="password_confirmation" 
                type="password" 
                name="password_confirmation" 
                placeholder="Ulangi password"
                required 
                autocomplete="new-password"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-rose-600" />
        </div>

        <!-- Submit Button -->
        <div class="pt-3">
            <button 
                type="submit" 
                class="w-full py-3 px-4 rounded-xl bg-[#111827] hover:bg-slate-900 text-white font-extrabold text-sm tracking-wider uppercase transition shadow-lg hover:shadow-slate-900/30 active:scale-[0.99]"
            >
                REGISTER
            </button>
        </div>

        <!-- Login Link -->
        <div class="text-center pt-3 border-t border-slate-100">
            <p class="text-xs text-slate-500 font-medium">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="font-bold text-slate-900 hover:underline">
                    Log in
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
