<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_naive_bayes_dataset', function (Blueprint $table) {
            $table->increments('id_dataset');
            $table->unsignedInteger('id_aset');
            $table->enum('kondisi_brg', ['B','RR','RB'])->nullable();
            $table->integer('usia_pakai')->nullable();
            $table->integer('frq_kerusakan')->nullable();
            $table->enum('jenis_pm', ['Preventif','Korektif','Tidak Ada'])->nullable();
            $table->integer('interval_pm')->nullable();
            $table->enum('efi_out', ['Tinggi','Sedang','Rendah'])->nullable();
            $table->float('downtime')->nullable();
            $table->enum('lingkungan', ['Baik','Cukup','Buruk'])->nullable();
            $table->enum('daya_listrik', ['Stabil','Tidak Stabil','Sering Padam'])->nullable();
            $table->enum('sparepart', ['Tersedia','Terbatas','Tidak Ada'])->nullable();
            $table->enum('kelas_label', ['Layak','Perlu Servis','Tidak Layak'])->nullable();
            $table->timestamp('tgl_input')->useCurrent();

            $table->foreign('id_aset')->references('id_aset')->on('t_aset')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_naive_bayes_dataset');
    }
};
