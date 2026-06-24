@props([
    'label',
    'value',
    'hint' => null,
    'icon' => 'fa-chart-line',
    'tone' => 'blue',
    'trend' => null,
])

@php
    $tones = [
        'blue' => ['icon' => 'from-blue-500 to-cyan-500', 'pill' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300'],
        'green' => ['icon' => 'from-emerald-500 to-teal-500', 'pill' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300'],
        'red' => ['icon' => 'from-rose-500 to-red-500', 'pill' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-300'],
        'amber' => ['icon' => 'from-amber-500 to-orange-500', 'pill' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-300'],
        'violet' => ['icon' => 'from-violet-500 to-indigo-500', 'pill' => 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-300'],
    ];
    $toneClass = $tones[$tone] ?? $tones['blue'];
@endphp

<div class="group rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-xl dark:border-slate-800 dark:bg-slate-900">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
            <p class="mt-2 text-3xl font-bold text-slate-950 dark:text-white">{{ $value }}</p>
        </div>
        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br {{ $toneClass['icon'] }} text-white shadow-lg shadow-slate-200/70 dark:shadow-none">
            <i class="fa-solid {{ $icon }}"></i>
        </div>
    </div>
    <div class="mt-4 flex items-center justify-between gap-3">
        @if($hint)
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $hint }}</p>
        @endif
        @if($trend)
            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $toneClass['pill'] }}">{{ $trend }}</span>
        @endif
    </div>
</div>
