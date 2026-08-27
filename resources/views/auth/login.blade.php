<x-guest-layout>
    <div class="text-center mb-6">
        <!-- Logo SMK Muhammadiyah 12 -->
        <a href="/" class="inline-flex items-center justify-center p-3 rounded-2xl bg-white shadow-md border border-slate-100 ring-4 ring-emerald-500/10 transition-all duration-300 transform hover:scale-105 hover:shadow-xl hover:ring-emerald-500/20">
            <img src="{{ asset('img/logo-smk.svg') }}" alt="Logo SMK Muhammadiyah 12 Jakarta" class="h-28 w-28 object-contain">
        </a>

        <!-- Title & Subtitle Matching Wireframe -->
        <h1 class="mt-4 text-xl font-extrabold tracking-tight text-slate-900 leading-snug uppercase">
            SISTEM MANAGEMENT<br>INVENTARIS LABORATORIUM TKJ
        </h1>
        <p class="mt-2 text-xs font-semibold text-emerald-700 bg-emerald-50 inline-block px-3 py-1 rounded-full border border-emerald-200/80">
            SMK Muhammadiyah 12 Jakarta
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-center text-sm" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Username / Email -->
        <div>
            <label for="email" class="block text-sm font-bold text-slate-800 mb-1">
                Username / Email
            </label>
            <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                placeholder="admin@smkmuh12.sch.id"
                required 
                autofocus 
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
                placeholder="••••••••••••"
                required 
                autocomplete="current-password"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:border-slate-900 focus:ring-1 focus:ring-slate-900 transition"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-600" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between text-xs pt-1">
            <label for="remember_me" class="inline-flex items-center text-slate-600 font-medium cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-slate-900 shadow-sm focus:ring-slate-900" name="remember">
                <span class="ms-2">[X] Remember Me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="font-medium text-slate-600 hover:text-slate-950 transition underline" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button 
                type="submit" 
                class="w-full py-3 px-4 rounded-xl bg-[#111827] hover:bg-slate-900 text-white font-extrabold text-sm tracking-wider uppercase transition shadow-lg hover:shadow-slate-900/30 active:scale-[0.99]"
            >
                LOG IN
            </button>
        </div>

        <!-- Register Link -->
        <div class="text-center pt-3 border-t border-slate-100">
            <p class="text-xs text-slate-500 font-medium">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-bold text-slate-900 hover:underline">
                    Register
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
