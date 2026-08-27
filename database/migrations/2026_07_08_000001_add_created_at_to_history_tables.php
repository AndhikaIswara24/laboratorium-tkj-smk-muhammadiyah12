<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add created_at column to the 4 asset history tables so we can
     * enforce 24-hour visibility on history views.
     */
    public function up(): void
    {
        $tables = [
            't_kondisi_fisik',
            't_pemeliharaan',
            't_efisiensi',
            't_variabel_eksternal',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->timestamp('created_at')->nullable();
            });

            // Backfill existing rows with current timestamp
            DB::table($table)->whereNull('created_at')->update([
                'created_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        $tables = [
            't_kondisi_fisik',
            't_pemeliharaan',
            't_efisiensi',
            't_variabel_eksternal',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('created_at');
            });
        }
    }
};
