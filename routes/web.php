<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\KondisiFisikController;
use App\Http\Controllers\PemeliharaanController;
use App\Http\Controllers\EfisiensiController;
use App\Http\Controllers\VariabelEksternalController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard redirect protected by auth; DashboardController will show role-specific view
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Asset management - teknisi and admin
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index')->middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin|teknisi');

    // Kondisi Fisik - teknisi and admin
    Route::get('/kondisi-fisik', [KondisiFisikController::class, 'index'])->name('kondisi.index')->middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin|teknisi');

    // Pemeliharaan - teknisi and admin
    Route::get('/pemeliharaan', [PemeliharaanController::class, 'index'])->name('pemeliharaan.index')->middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin|teknisi');

    // Efisiensi - admin and teknisi
    Route::get('/efisiensi', [EfisiensiController::class, 'index'])->name('efisiensi.index')->middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin|teknisi');

    // Variabel eksternal - admin
    Route::get('/variabel-eksternal', [VariabelEksternalController::class, 'index'])->name('variabel.index')->middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin');

    // Prediksi - admin
    Route::get('/prediksi-naive-bayes', [PrediksiController::class, 'index'])->name('prediksi.index')->middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin');

    // Laporan - admin only
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index')->middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin');

    // Admin user management
    Route::prefix('admin')->name('admin.')->middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin')->group(function () {
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::patch('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__.'/auth.php';
