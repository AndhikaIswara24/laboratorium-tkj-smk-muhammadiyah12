<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_kondisi_fisik', function (Blueprint $table) {
            $table->increments('id_kondisi');
            $table->unsignedInteger('id_aset');
            $table->date('tgl_observasi');
            $table->enum('kondisi_brg', ['B','RR','RB']);
            $table->string('ket_teknis', 100)->nullable();
            $table->integer('usia_pakai')->nullable();
            $table->integer('frq_kerusakan')->default(0);
            $table->enum('kelas_label', ['Layak','Perlu Servis','Tidak Layak']);

            $table->foreign('id_aset')->references('id_aset')->on('t_aset')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_kondisi_fisik');
    }
};
