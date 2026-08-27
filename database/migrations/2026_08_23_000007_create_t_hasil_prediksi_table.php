<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('t_hasil_prediksi')) {
            Schema::create('t_hasil_prediksi', function (Blueprint $table) {
                $table->id('id_prediksi');
                $table->unsignedBigInteger('id_dataset')->nullable()->index();
                $table->unsignedBigInteger('id_aset')->index();
                $table->timestamp('tgl_prediksi')->useCurrent();
                $table->string('hasil_prediksi', 80)->nullable();
                $table->decimal('prob_layak', 8, 6)->default(0.0);
                $table->decimal('prob_servis', 8, 6)->default(0.0);
                $table->decimal('prob_tidak_layak', 8, 6)->default(0.0);
                $table->timestamps();

                $table->foreign('id_dataset')->references('id_dataset')->on('t_naive_bayes_dataset')->onDelete('set null');
                $table->foreign('id_aset')->references('id_aset')->on('t_aset')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('t_hasil_prediksi');
    }
};
