<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('t_kondisi_fisik')) {
            Schema::create('t_kondisi_fisik', function (Blueprint $table) {
                $table->id('id_kondisi');
                $table->unsignedBigInteger('id_aset')->index();
                $table->string('kondisi_brg', 10)->nullable();
                $table->integer('usia_pakai')->nullable();
                $table->integer('frq_kerusakan')->nullable();
                $table->string('kelas_label', 50)->nullable();
                $table->timestamp('tgl_input')->useCurrent();
                $table->timestamps();

                $table->foreign('id_aset')->references('id_aset')->on('t_aset')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('t_kondisi_fisik');
    }
};
