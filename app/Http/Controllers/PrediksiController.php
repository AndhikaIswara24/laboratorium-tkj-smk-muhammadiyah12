<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\NaiveBayesDataset;
use Illuminate\Support\Facades\DB;

class PrediksiController extends Controller
{
    public function index()
    {
        // placeholder for Naive Bayes prediction UI
        return view('prediksi.index');
    }

    /**
     * Halaman manajemen dataset Naive Bayes.
     */
    public function datasetIndex()
    {
        $rows = NaiveBayesDataset::with('asset')->paginate(15);
        $totalDataset = NaiveBayesDataset::count();
        $totalAssets = Asset::count();

        // Cari tahu aset mana yang datanya tidak lengkap (kurang di salah satu dari 4 tabel)
        $assets = Asset::withCount(['kondisiFisik', 'pemeliharaan', 'efisiensi', 'variabelEksternal'])->get();
        $incompleteCount = 0;
        foreach ($assets as $asset) {
            if ($asset->kondisi_fisik_count == 0 || $asset->pemeliharaan_count == 0 || 
                $asset->efisiensi_count == 0 || $asset->variabel_eksternal_count == 0) {
                $incompleteCount++;
            }
        }

        return view('prediksi.dataset', compact('rows', 'totalDataset', 'totalAssets', 'incompleteCount'));
    }

    /**
     * Menggabungkan 4 tabel historis menjadi 1 flat dataset Naive Bayes.
     */
    public function generateDataset()
    {
        // Ambil data observasi terbaru per aset dari masing-masing tabel (menggunakan ID tertinggi)
        $kondisi = DB::table('t_kondisi_fisik')
            ->whereIn('id_kondisi', function($query) {
                $query->selectRaw('MAX(id_kondisi)')->from('t_kondisi_fisik')->groupBy('id_aset');
            })->get()->keyBy('id_aset');

        $pemeliharaan = DB::table('t_pemeliharaan')
            ->whereIn('id_pm', function($query) {
                $query->selectRaw('MAX(id_pm)')->from('t_pemeliharaan')->groupBy('id_aset');
            })->get()->keyBy('id_aset');

        $efisiensi = DB::table('t_efisiensi')
            ->whereIn('id_efisiensi', function($query) {
                $query->selectRaw('MAX(id_efisiensi)')->from('t_efisiensi')->groupBy('id_aset');
            })->get()->keyBy('id_aset');

        $variabel = DB::table('t_variabel_eksternal')
            ->whereIn('id_eksternal', function($query) {
                $query->selectRaw('MAX(id_eksternal)')->from('t_variabel_eksternal')->groupBy('id_aset');
            })->get()->keyBy('id_aset');

        $assets = Asset::all();

        // Truncate/bersihkan tabel dataset Naive Bayes terlebih dahulu
        NaiveBayesDataset::truncate();

        $inserted = 0;

        foreach ($assets as $asset) {
            $k = $kondisi->get($asset->id_aset);
            $p = $pemeliharaan->get($asset->id_aset);
            $e = $efisiensi->get($asset->id_aset);
            $v = $variabel->get($asset->id_aset);

            // Hanya masukkan aset yang memiliki data lengkap di keempat tabel historis
            if ($k && $p && $e && $v) {
                NaiveBayesDataset::create([
                    'id_aset' => $asset->id_aset,
                    'kondisi_brg' => $k->kondisi_brg,
                    'usia_pakai' => $k->usia_pakai,
                    'frq_kerusakan' => $k->frq_kerusakan,
                    'jenis_pm' => $p->jenis_pm,
                    'interval_pm' => $p->interval_bulan,
                    'efi_out' => $e->efi_out,
                    'downtime' => $e->downtime,
                    'lingkungan' => $v->lingkungan,
                    'daya_listrik' => $v->daya_listrik,
                    'sparepart' => $v->sparepart,
                    'kelas_label' => $k->kelas_label,
                    'tgl_input' => now(),
                ]);
                $inserted++;
            }
        }

        return redirect()->route('prediksi.dataset')
            ->with('success', "Berhasil menggabungkan data! Sebanyak {$inserted} data aset berhasil digabungkan menjadi dataset Naive Bayes.");
    }
}
