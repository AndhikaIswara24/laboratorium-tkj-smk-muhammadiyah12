<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('t_variabel_eksternal')) {
            Schema::create('t_variabel_eksternal', function (Blueprint $table) {
                $table->id('id_eksternal');
                $table->unsignedBigInteger('id_aset')->index();
                $table->string('lingkungan', 80)->nullable();
                $table->string('daya_listrik', 80)->nullable();
                $table->string('sparepart', 80)->nullable();
                $table->timestamp('tgl_input')->nullable();
                $table->timestamps();

                $table->foreign('id_aset')->references('id_aset')->on('t_aset')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('t_variabel_eksternal');
    }
};
