<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_efisiensi', function (Blueprint $table) {
            $table->increments('id_efisiensi');
            $table->unsignedInteger('id_aset');
            $table->date('tgl_observasi');
            $table->float('jam_ops')->nullable();
            $table->enum('penggunaan', ['Tinggi','Sedang','Tidak Pakai'])->nullable();
            $table->integer('jml_user')->default(0);
            $table->float('downtime')->default(0);
            $table->enum('perform', ['Normal','Lambat','Mati'])->nullable();
            $table->integer('umur_ekonomis')->nullable();
            $table->enum('efi_out', ['Tinggi','Sedang','Rendah'])->nullable();

            $table->foreign('id_aset')->references('id_aset')->on('t_aset')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_efisiensi');
    }
};
