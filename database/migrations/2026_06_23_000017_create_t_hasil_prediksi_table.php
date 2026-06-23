<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_hasil_prediksi', function (Blueprint $table) {
            $table->increments('id_prediksi');
            $table->unsignedInteger('id_dataset');
            $table->unsignedInteger('id_aset');
            $table->dateTime('tgl_prediksi')->useCurrent();
            $table->enum('hasil_prediksi', ['Layak','Perlu Servis','Tidak Layak'])->nullable();
            $table->float('prob_layak')->nullable();
            $table->float('prob_servis')->nullable();
            $table->float('prob_tidak_layak')->nullable();

            $table->foreign('id_dataset')->references('id_dataset')->on('t_naive_bayes_dataset')->onDelete('cascade');
            $table->foreign('id_aset')->references('id_aset')->on('t_aset')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_hasil_prediksi');
    }
};
