<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_pemeliharaan', function (Blueprint $table) {
            $table->increments('id_pm');
            $table->unsignedInteger('id_aset');
            $table->date('tgl_pm')->nullable();
            $table->enum('jenis_pm', ['Preventif','Korektif','Tidak Ada'])->default('Preventif');
            $table->integer('interval_bulan')->nullable();
            $table->string('pelaksana', 60)->nullable();
            $table->decimal('biaya_servis', 12, 2)->default(0);
            $table->enum('kon_after', ['B','RR','RB'])->nullable();
            $table->text('ket_pm')->nullable();

            $table->foreign('id_aset')->references('id_aset')->on('t_aset')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_pemeliharaan');
    }
};
