<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('t_aset')) {
            Schema::create('t_aset', function (Blueprint $table) {
                $table->id('id_aset');
                $table->string('kode_brg', 50)->unique();
                $table->string('nama_brg', 200);
                $table->string('merk_tipe', 120)->nullable();
                $table->text('spesifikasi')->nullable();
                $table->string('lokasi', 100)->nullable();
                $table->string('thn_perolehan', 4)->nullable();
                $table->decimal('harga_perolehan', 15, 2)->nullable();
                $table->string('asal_usul', 60)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('t_aset');
    }
};
