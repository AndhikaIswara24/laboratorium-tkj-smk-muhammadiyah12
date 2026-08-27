<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyPrediksiDb extends Command
{
    protected $signature = 'verify:prediksi-db';
    protected $description = 'Verify t_hasil_prediksi counts, distribution and FK integrity';

    public function handle()
    {
        $this->info('Verifying table: t_hasil_prediksi');

        $total = DB::table('t_hasil_prediksi')->count();
        $this->line('Total rows in t_hasil_prediksi: ' . $total);

        $this->line('Distribution by hasil_prediksi:');
        $dist = DB::table('t_hasil_prediksi')
            ->select('hasil_prediksi', DB::raw('COUNT(*) as cnt'))
            ->groupBy('hasil_prediksi')
            ->get();
        foreach ($dist as $d) {
            $this->line(" - {$d->hasil_prediksi}: {$d->cnt}");
        }

        $distinctDatasets = DB::table('t_hasil_prediksi')->whereNotNull('id_dataset')->distinct('id_dataset')->count('id_dataset');
        $this->line('Distinct id_dataset referenced: ' . $distinctDatasets);

        $distinctAssets = DB::table('t_hasil_prediksi')->distinct('id_aset')->count('id_aset');
        $this->line('Distinct id_aset referenced: ' . $distinctAssets);

        // FK integrity checks
        $missingAset = DB::table('t_hasil_prediksi as h')
            ->leftJoin('t_aset as a', 'h.id_aset', '=', 'a.id_aset')
            ->whereNull('a.id_aset')
            ->count();
        $this->line('t_hasil_prediksi rows with missing t_aset FK: ' . $missingAset);

        $missingDataset = DB::table('t_hasil_prediksi as h')
            ->leftJoin('t_naive_bayes_dataset as d', 'h.id_dataset', '=', 'd.id_dataset')
            ->whereNotNull('h.id_dataset')
            ->whereNull('d.id_dataset')
            ->count();
        $this->line('t_hasil_prediksi rows with missing t_naive_bayes_dataset FK: ' . $missingDataset);

        $this->line('Sample latest 5 rows:');
        $rows = DB::table('t_hasil_prediksi')->orderBy('tgl_prediksi', 'desc')->limit(5)->get();
        foreach ($rows as $r) {
            $this->line(" - id_prediksi={$r->id_prediksi}, id_aset={$r->id_aset}, id_dataset={$r->id_dataset}, hasil={$r->hasil_prediksi}, tgl={$r->tgl_prediksi}");
        }

        $this->info('Verification complete.');
        return 0;
    }
}
