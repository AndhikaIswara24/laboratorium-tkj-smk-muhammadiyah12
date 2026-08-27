<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('t_pemeliharaan')) {
            Schema::create('t_pemeliharaan', function (Blueprint $table) {
                $table->id('id_pm');
                $table->unsignedBigInteger('id_aset')->index();
                $table->string('jenis_pm', 80)->nullable();
                $table->integer('interval_bulan')->nullable();
                $table->timestamp('tgl_pm')->nullable();
                $table->text('keterangan')->nullable();
                $table->timestamps();

                $table->foreign('id_aset')->references('id_aset')->on('t_aset')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('t_pemeliharaan');
    }
};
