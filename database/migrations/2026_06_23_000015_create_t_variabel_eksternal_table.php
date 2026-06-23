<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_variabel_eksternal', function (Blueprint $table) {
            $table->increments('id_eksternal');
            $table->unsignedInteger('id_aset');
            $table->date('tgl_observasi');
            $table->enum('lingkungan', ['Baik','Cukup','Buruk'])->nullable();
            $table->enum('daya_listrik', ['Stabil','Tidak Stabil','Sering Padam'])->nullable();
            $table->enum('sparepart', ['Tersedia','Terbatas','Tidak Ada'])->nullable();
            $table->enum('anggaran', ['Mendukung','Terbatas','Tidak Ada'])->nullable();
            $table->enum('ext_effect', ['Rendah','Sedang','Tinggi'])->nullable();

            $table->foreign('id_aset')->references('id_aset')->on('t_aset')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_variabel_eksternal');
    }
};
