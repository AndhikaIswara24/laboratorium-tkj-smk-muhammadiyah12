<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_aset', function (Blueprint $table) {
            $table->increments('id_aset');
            $table->string('kode_brg', 20)->unique();
            $table->string('nama_brg', 100);
            $table->string('merk_tipe', 80)->nullable();
            $table->text('spesifikasi')->nullable();
            $table->string('lokasi', 60)->nullable();
            $table->string('thn_perolehan', 4)->nullable();
            $table->decimal('harga_perolehan', 15, 2)->nullable();
            $table->enum('asal_usul', ['Pembelian','Hibah','Dropping Dinas','Dana BOS'])->default('Pembelian');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_aset');
    }
};
