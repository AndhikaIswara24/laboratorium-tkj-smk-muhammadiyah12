<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('t_naive_bayes_dataset')) {
            Schema::create('t_naive_bayes_dataset', function (Blueprint $table) {
                $table->id('id_dataset');
                $table->unsignedBigInteger('id_aset')->index();
                $table->string('kondisi_brg', 50)->nullable();
                $table->integer('usia_pakai')->nullable();
                $table->integer('frq_kerusakan')->nullable();
                $table->string('jenis_pm', 80)->nullable();
                $table->integer('interval_pm')->nullable();
                $table->string('efi_out', 80)->nullable();
                $table->decimal('downtime', 8, 2)->nullable();
                $table->string('lingkungan', 80)->nullable();
                $table->string('daya_listrik', 80)->nullable();
                $table->string('sparepart', 80)->nullable();
                $table->string('kelas_label', 80)->nullable();
                $table->timestamp('tgl_input')->useCurrent();
                $table->timestamps();

                $table->foreign('id_aset')->references('id_aset')->on('t_aset')->onDelete('cascade');
                $table->index(['id_aset', 'kelas_label']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('t_naive_bayes_dataset');
    }
};
