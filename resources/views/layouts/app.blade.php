<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Inventaris Lab TKJ') - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head')
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100" x-data>
        <div class="min-h-screen">
            @include('partials.header')

            @auth
            <div class="flex pt-16">
                @include('partials.sidebar')

                <main id="dashboardMain" class="min-h-[calc(100vh-4rem)] flex-1 transition-all duration-300 lg:pl-72">
                    <div class="mx-auto max-w-[1600px] p-4 sm:p-6 lg:p-8">
                        @isset($header)
                            <div class="mb-6">
                                {{ $header }}
                            </div>
                        @endisset

                        @hasSection('content')
                            @yield('content')
                        @else
                            {{ $slot }}
                        @endif
                    </div>
                </main>
            </div>
            @else
            <main class="flex-1">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot }}
                @endif
            </main>
            @endauth
        </div>
        @stack('scripts')
    </body>
</html>
