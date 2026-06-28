@extends('layouts.app')
@section('title', 'Prediksi Naive Bayes')
@section('content')

<div class="space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
            <i class="fa-solid fa-brain mr-2 text-blue-600"></i>Prediksi Naive Bayes
        </h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Klasifikasi kelayakan aset laboratorium menggunakan algoritma Naive Bayes berdasarkan metrik data historis.
        </p>
    </div>

    {{-- Menu Grid --}}
    <div class="grid gap-6 md:grid-cols-2">
        {{-- Card 1: Dataset Flat --}}
        <div class="flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
            <div class="space-y-3">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <i class="fa-solid fa-database text-xl"></i>
                </span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Dataset Flat</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                    Lihat hasil penggabungan data dari 4 tabel historis (kondisi fisik, pemeliharaan, efisiensi, dan variabel eksternal) menjadi satu dataset flat, serta lakukan regenerasi dataset.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('prediksi.dataset') }}" class="btn-primary inline-flex items-center gap-1.5 py-2.5">
                    Kelola Dataset <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>

        {{-- Card 2: Proses Prediksi --}}
        <div class="flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
            <div class="space-y-3">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                    <i class="fa-solid fa-wand-magic-sparkles text-xl"></i>
                </span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Hasil Prediksi Kelayakan</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                    Lacak hasil kalkulasi klasifikasi kelayakan aset (Layak, Perlu Servis, atau Tidak Layak) beserta tingkat probabilitas masing-masing kelas.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="#" class="btn-secondary inline-flex items-center gap-1.5 py-2.5 cursor-not-allowed opacity-60" title="Akan datang">
                    Hasil Prediksi <i class="fa-solid fa-lock text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
