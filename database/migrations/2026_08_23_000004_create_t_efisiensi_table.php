<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('t_efisiensi')) {
            Schema::create('t_efisiensi', function (Blueprint $table) {
                $table->id('id_efisiensi');
                $table->unsignedBigInteger('id_aset')->index();
                $table->string('efi_out', 80)->nullable();
                $table->decimal('downtime', 8, 2)->nullable();
                $table->timestamp('tgl_input')->nullable();
                $table->timestamps();

                $table->foreign('id_aset')->references('id_aset')->on('t_aset')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('t_efisiensi');
    }
};
