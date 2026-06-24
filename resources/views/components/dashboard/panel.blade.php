@props([
    'title',
    'subtitle' => null,
    'icon' => null,
])

<section {{ $attributes->merge(['class' => 'rounded-lg border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900']) }}>
    <div class="mb-5 flex items-start justify-between gap-4">
        <div>
            <h2 class="flex items-center gap-2 text-base font-semibold text-slate-950 dark:text-white">
                @if($icon)
                    <i class="fa-solid {{ $icon }} text-blue-500"></i>
                @endif
                {{ $title }}
            </h2>
            @if($subtitle)
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
            @endif
        </div>
        {{ $actions ?? '' }}
    </div>
    {{ $slot }}
</section>
