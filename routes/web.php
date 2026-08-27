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
    Route::post('/assets/import', [AssetController::class, 'import'])->name('assets.import')->middleware('role:admin|teknisi');
    Route::delete('/assets/destroy-all', [AssetController::class, 'destroyAll'])->name('assets.destroyAll')->middleware('role:admin|teknisi');
    Route::resource('assets', AssetController::class)
        ->middleware('role:admin|teknisi')
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // Kondisi Fisik - teknisi and admin
    Route::middleware('role:admin|teknisi')->group(function () {
        Route::get('/kondisi-fisik', [KondisiFisikController::class, 'index'])->name('kondisi.index');
        Route::get('/kondisi-fisik/export/csv', [KondisiFisikController::class, 'exportCsv'])->name('kondisi.export.csv');
        Route::get('/kondisi-fisik/export/excel', [KondisiFisikController::class, 'exportExcel'])->name('kondisi.export.excel');
        Route::get('/kondisi-fisik/create', [KondisiFisikController::class, 'create'])->name('kondisi.create');
        Route::post('/kondisi-fisik', [KondisiFisikController::class, 'store'])->name('kondisi.store');
        Route::get('/kondisi-fisik/{id}/edit', [KondisiFisikController::class, 'edit'])->name('kondisi.edit');
        Route::put('/kondisi-fisik/{id}', [KondisiFisikController::class, 'update'])->name('kondisi.update');
        Route::delete('/kondisi-fisik/{id}', [KondisiFisikController::class, 'destroy'])->name('kondisi.destroy');
        Route::get('/kondisi-fisik/history/{idAset}', [KondisiFisikController::class, 'history'])->name('kondisi.history');
        Route::get('/kondisi-fisik/asset-data/{id}', [KondisiFisikController::class, 'getAssetData'])->name('kondisi.asset-data');
    });

    // Pemeliharaan - teknisi and admin
    Route::middleware('role:admin|teknisi')->group(function () {
        Route::get('/pemeliharaan', [PemeliharaanController::class, 'index'])->name('pemeliharaan.index');
        Route::get('/pemeliharaan/export/csv', [PemeliharaanController::class, 'exportCsv'])->name('pemeliharaan.export.csv');
        Route::get('/pemeliharaan/export/excel', [PemeliharaanController::class, 'exportExcel'])->name('pemeliharaan.export.excel');
        Route::get('/pemeliharaan/create', [PemeliharaanController::class, 'create'])->name('pemeliharaan.create');
        Route::post('/pemeliharaan', [PemeliharaanController::class, 'store'])->name('pemeliharaan.store');
        Route::get('/pemeliharaan/{id}/edit', [PemeliharaanController::class, 'edit'])->name('pemeliharaan.edit');
        Route::put('/pemeliharaan/{id}', [PemeliharaanController::class, 'update'])->name('pemeliharaan.update');
        Route::delete('/pemeliharaan/{id}', [PemeliharaanController::class, 'destroy'])->name('pemeliharaan.destroy');
        Route::get('/pemeliharaan/history/{idAset}', [PemeliharaanController::class, 'history'])->name('pemeliharaan.history');
    });

    // Efisiensi - admin and teknisi
    Route::middleware('role:admin|teknisi')->group(function () {
        Route::get('/efisiensi', [EfisiensiController::class, 'index'])->name('efisiensi.index');
        Route::get('/efisiensi/export/csv', [EfisiensiController::class, 'exportCsv'])->name('efisiensi.export.csv');
        Route::get('/efisiensi/export/excel', [EfisiensiController::class, 'exportExcel'])->name('efisiensi.export.excel');
        Route::get('/efisiensi/create', [EfisiensiController::class, 'create'])->name('efisiensi.create');
        Route::post('/efisiensi', [EfisiensiController::class, 'store'])->name('efisiensi.store');
        Route::get('/efisiensi/{id}/edit', [EfisiensiController::class, 'edit'])->name('efisiensi.edit');
        Route::put('/efisiensi/{id}', [EfisiensiController::class, 'update'])->name('efisiensi.update');
        Route::delete('/efisiensi/{id}', [EfisiensiController::class, 'destroy'])->name('efisiensi.destroy');
        Route::get('/efisiensi/history/{idAset}', [EfisiensiController::class, 'history'])->name('efisiensi.history');
    });

    // Variabel eksternal - admin and teknisi
    Route::middleware('role:admin|teknisi')->group(function () {
        Route::get('/variabel-eksternal', [VariabelEksternalController::class, 'index'])->name('variabel.index');
        Route::get('/variabel-eksternal/export/csv', [VariabelEksternalController::class, 'exportCsv'])->name('variabel.export.csv');
        Route::get('/variabel-eksternal/export/excel', [VariabelEksternalController::class, 'exportExcel'])->name('variabel.export.excel');
        Route::get('/variabel-eksternal/create', [VariabelEksternalController::class, 'create'])->name('variabel.create');
        Route::post('/variabel-eksternal', [VariabelEksternalController::class, 'store'])->name('variabel.store');
        Route::get('/variabel-eksternal/{id}/edit', [VariabelEksternalController::class, 'edit'])->name('variabel.edit');
        Route::put('/variabel-eksternal/{id}', [VariabelEksternalController::class, 'update'])->name('variabel.update');
        Route::delete('/variabel-eksternal/{id}', [VariabelEksternalController::class, 'destroy'])->name('variabel.destroy');
        Route::get('/variabel-eksternal/history/{idAset}', [VariabelEksternalController::class, 'history'])->name('variabel.history');
    });

    // Prediksi - admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/prediksi-naive-bayes', [PrediksiController::class, 'index'])->name('prediksi.index');
        Route::get('/prediksi-naive-bayes/dataset', [PrediksiController::class, 'datasetIndex'])->name('prediksi.dataset');
        Route::get('/prediksi-naive-bayes/training', [PrediksiController::class, 'trainingIndex'])->name('prediksi.training');
        Route::get('/prediksi-naive-bayes/prediksi-kelayakan', [PrediksiController::class, 'predictionPage'])->name('prediksi.kelayakan');
        Route::get('/prediksi-naive-bayes/asset-history/{id}', [PrediksiController::class, 'getAssetHistory'])->name('prediksi.asset_history');
        Route::get('/prediksi-naive-bayes/ringkasan', [PrediksiController::class, 'summaryIndex'])->name('prediksi.summary');
        Route::get('/prediksi-naive-bayes/evaluasi', [PrediksiController::class, 'evaluationIndex'])->name('prediksi.evaluasi');
        Route::get('/prediksi-naive-bayes/laporan-kelayakan', [PrediksiController::class, 'predictionReport'])->name('prediksi.laporan_kelayakan');
        Route::get('/prediksi-naive-bayes/dataset-items', [PrediksiController::class, 'getDatasetItems'])->name('prediksi.dataset_items');
        Route::post('/prediksi-naive-bayes/predict-dataset-item/{id}', [PrediksiController::class, 'predictDatasetItem'])->name('prediksi.predict_dataset_item');
        Route::post('/prediksi-naive-bayes/predict-all-optimized', [PrediksiController::class, 'predictAllOptimized'])->name('prediksi.predict_all_optimized');
        Route::post('/prediksi-naive-bayes/dataset/generate', [PrediksiController::class, 'generateDataset'])->name('prediksi.dataset.generate');
        Route::post('/prediksi-naive-bayes/train', [PrediksiController::class, 'trainModel'])->name('prediksi.train');
        Route::post('/prediksi-naive-bayes/predict', [PrediksiController::class, 'predict'])->name('prediksi.predict');
    });

    // Laporan - admin only
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index')->middleware('role:admin');
    Route::get('/laporan/generate', [LaporanController::class, 'generate'])->name('laporan.generate')->middleware('role:admin');

    // Admin user management
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::patch('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__ . '/auth.php';
