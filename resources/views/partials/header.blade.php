<header
    class="fixed inset-x-0 top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950/85">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            @auth
                <button id="sidebarToggle" type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
                    aria-label="Toggle sidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
            @endauth

            <a href="{{ Route::has('dashboard') ? route('dashboard') : url('/') }}" class="group flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-900 p-1.5 ring-2 ring-emerald-500/10 transition-all duration-200 group-hover:scale-105 group-hover:border-emerald-500/30">
                    <img src="{{ asset('img/logo-smk.svg') }}" alt="Logo SMK Muhammadiyah 12" class="h-full w-full object-contain">
                </span>
                <span class="hidden leading-tight sm:block">
                    <span class="block text-sm font-extrabold text-slate-950 dark:text-white tracking-tight group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Sistem Management Inventaris Laboratorium TKJ</span>
                    <span class="block text-xs font-medium text-slate-500 dark:text-slate-400">SMK Muhammadiyah 12 Jakarta</span>
                </span>
            </a>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            @auth
                <button id="themeToggle" type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                    aria-label="Toggle dark mode">
                    <i class="fa-solid fa-moon dark:hidden"></i>
                    <i class="fa-solid fa-sun hidden dark:inline"></i>
                </button>

                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                        class="relative inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                        aria-label="Notification">
                        <i class="fa-regular fa-bell"></i>
                        <span
                            class="absolute right-2 top-2 h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-white dark:ring-slate-950"></span>
                    </button>
                    <div x-cloak x-show="open" @click.outside="open = false" x-transition
                        class="absolute right-0 mt-3 w-80 rounded-lg border border-slate-200 bg-white p-3 shadow-xl dark:border-slate-800 dark:bg-slate-900">
                        <p class="px-2 pb-2 text-sm font-semibold text-slate-900 dark:text-white">Notifikasi</p>
                        <div class="space-y-2">
                            <div
                                class="rounded-lg bg-amber-50 p-3 text-sm text-amber-800 dark:bg-amber-500/10 dark:text-amber-200">
                                12 aset masuk jadwal maintenance minggu ini.</div>
                            <div
                                class="rounded-lg bg-blue-50 p-3 text-sm text-blue-800 dark:bg-blue-500/10 dark:text-blue-200">
                                Model Naive Bayes selesai dilatih ulang.</div>
                        </div>
                    </div>
                </div>

                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                        class="flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-2 py-1.5 transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-900 dark:hover:bg-slate-800">
                        <span class="hidden text-right sm:block">
                            <span
                                class="block text-sm font-semibold text-slate-900 dark:text-white">{{ auth()->user()->name }}</span>
                            <span
                                class="block text-xs capitalize text-slate-500 dark:text-slate-400">{{ auth()->user()->role }}</span>
                        </span>
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-slate-800 to-blue-600 text-sm font-bold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <i class="fa-solid fa-chevron-down hidden text-xs text-slate-400 sm:inline"></i>
                    </button>
                    <div x-cloak x-show="open" @click.outside="open = false" x-transition
                        class="absolute right-0 mt-3 w-56 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-2 px-4 py-3 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">
                            <i class="fa-regular fa-user w-4"></i> Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center gap-2 px-4 py-3 text-left text-sm text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-500/10">
                                <i class="fa-solid fa-arrow-right-from-bracket w-4"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
                    href="{{ route('login') }}">Login</a>
            @endauth
        </div>
    </div>
</header>