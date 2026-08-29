<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Inventaris Lab TKJ') }} - SMK Muhammadiyah 12 Jakarta</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased bg-slate-100 min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md my-auto">
            <div class="guest-card bg-white rounded-[32px] border-2 border-slate-900 shadow-2xl p-6 sm:p-8 transition-all">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

