<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Simple dashboard route for Inventaris Lab TKJ
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\KondisiFisikController;
use App\Http\Controllers\PemeliharaanController;
use App\Http\Controllers\EfisiensiController;
use App\Http\Controllers\VariabelEksternalController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\LaporanController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
Route::get('/kondisi-fisik', [KondisiFisikController::class, 'index'])->name('kondisi.index');
Route::get('/pemeliharaan', [PemeliharaanController::class, 'index'])->name('pemeliharaan.index');
Route::get('/efisiensi', [EfisiensiController::class, 'index'])->name('efisiensi.index');
Route::get('/variabel-eksternal', [VariabelEksternalController::class, 'index'])->name('variabel.index');
Route::get('/prediksi-naive-bayes', [PrediksiController::class, 'index'])->name('prediksi.index');
Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
