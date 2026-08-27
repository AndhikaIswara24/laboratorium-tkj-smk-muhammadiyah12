<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Asset;
use App\Models\NaiveBayesDataset;
use App\Models\HasilPrediksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Traits\CanExportData;

class PrediksiController extends Controller
{
    use CanExportData;
    /**
     * Helper to get Flask API URL from environment (use .env.production in production).
     */
    private static function flaskUrl()
    {
        return config('services.flask.url', 'http://127.0.0.1:5000');
    }

    /**
     * Helper to build HTTP client with API key header if provided.
     */
    private static function flaskHttp()
    {
        $apiKey = config('services.flask.key', '');
        $headers = [];
        if (!empty($apiKey)) {
            $headers['X-API-KEY'] = $apiKey;
        }
        // Return a pending request builder so callers can chain ->timeout(...)
        return Http::withHeaders($headers);
    }

    public function index()
    {
        // placeholder for Naive Bayes prediction UI
        return view('prediksi.index');
    }

    /**
     * Halaman Training Model Naive Bayes.
     */
    public function trainingIndex()
    {
        return view('prediksi.training');
    }

    /**
     * Halaman kalkulator/form prediksi kelayakan aset.
     */
    public function predictionPage()
    {
        $assets = Asset::orderBy('nama_brg', 'asc')->get();
        return view('prediksi.prediksi', compact('assets'));
    }

    /**
     * Mengambil data riwayat terbaru dari 4 tabel historis untuk aset tertentu.
     */
    public function getAssetHistory($id)
    {
        $kondisi = DB::table('t_kondisi_fisik')
            ->where('id_aset', $id)
            ->latest('id_kondisi')
            ->first();

        $pemeliharaan = DB::table('t_pemeliharaan')
            ->where('id_aset', $id)
            ->latest('id_pm')
            ->first();

        $efisiensi = DB::table('t_efisiensi')
            ->where('id_aset', $id)
            ->latest('id_efisiensi')
            ->first();

        $variabel = DB::table('t_variabel_eksternal')
            ->where('id_aset', $id)
            ->latest('id_eksternal')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'kondisi_brg' => $kondisi?->kondisi_brg ?? null,
                'usia_pakai' => $kondisi?->usia_pakai ?? null,
                'frq_kerusakan' => $kondisi?->frq_kerusakan ?? null,
                'jenis_pm' => $pemeliharaan?->jenis_pm ?? null,
                'interval_pm' => $pemeliharaan?->interval_bulan ?? null,
                'efi_out' => $efisiensi?->efi_out ?? null,
                'downtime' => $efisiensi?->downtime ?? null,
                'lingkungan' => $variabel?->lingkungan ?? null,
                'daya_listrik' => $variabel?->daya_listrik ?? null,
                'sparepart' => $variabel?->sparepart ?? null,
            ],
            'incomplete' => (!$kondisi || !$pemeliharaan || !$efisiensi || !$variabel)
        ]);
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

        // Bersihkan tabel (disable FK checks agar TRUNCATE tidak diblokir DB engine)
        Schema::disableForeignKeyConstraints();
        HasilPrediksi::truncate();
        NaiveBayesDataset::truncate();
        Schema::enableForeignKeyConstraints();

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

    /**
     * Melatih model Naive Bayes melalui Flask API.
     * Memanggil POST /train pada Flask API dan mengembalikan hasil training.
     */
    public function trainModel()
    {
        try {
            $response = self::flaskHttp()->timeout(60)->post(self::flaskUrl() . '/train');

            if ($response->successful()) {
                $data = $response->json();
                return response()->json($data);
            }

            return response()->json([
                'success' => false,
                'message' => $response->json('message') ?? 'Gagal melatih model. Flask API mengembalikan error.',
            ], $response->status());

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat terhubung ke Flask API. Pastikan Flask API berjalan di ' . self::flaskUrl(),
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Melakukan prediksi menggunakan model Naive Bayes melalui Flask API.
     * Menerima data fitur aset, memanggil POST /predict, dan menyimpan hasil ke t_hasil_prediksi.
     */
    public function predict(Request $request)
    {
        $validated = $request->validate([
            'id_aset' => 'required|integer|exists:t_aset,id_aset',
            'kondisi_brg' => 'required|in:B,RR,RB',
            'usia_pakai' => 'required|integer|min:0',
            'frq_kerusakan' => 'required|integer|min:0',
            'jenis_pm' => 'required|in:Preventif,Korektif,Tidak Ada',
            'interval_pm' => 'required|integer|min:0',
            'efi_out' => 'required|in:Tinggi,Sedang,Rendah',
            'downtime' => 'required|numeric|min:0',
            'lingkungan' => 'required|in:Baik,Cukup,Buruk',
            'daya_listrik' => 'required|in:Stabil,Tidak Stabil,Sering Padam',
            'sparepart' => 'required|in:Tersedia,Terbatas,Tidak Ada',
        ]);

        try {
            // Kirim 10 fitur ke Flask API untuk prediksi
            $features = collect($validated)->except('id_aset')->toArray();
            $response = self::flaskHttp()->timeout(30)->post(self::flaskUrl() . '/predict', $features);

            if ($response->successful()) {
                $data = $response->json();

                // Cari id_dataset terkait (jika ada)
                $dataset = NaiveBayesDataset::where('id_aset', $validated['id_aset'])->latest('id_dataset')->first();

                // Simpan hasil prediksi ke t_hasil_prediksi
                $hasil = HasilPrediksi::create([
                    'id_dataset' => $dataset?->id_dataset ?? 0,
                    'id_aset' => $validated['id_aset'],
                    'tgl_prediksi' => now(),
                    'hasil_prediksi' => $data['predicted_class'],
                    'prob_layak' => $data['probabilities']['Layak'] ?? 0,
                    'prob_servis' => $data['probabilities']['Perlu Servis'] ?? 0,
                    'prob_tidak_layak' => $data['probabilities']['Tidak Layak'] ?? 0,
                ]);

                return response()->json([
                    'success' => true,
                    'predicted_class' => $data['predicted_class'],
                    'probabilities' => $data['probabilities'],
                    'id_prediksi' => $hasil->id_prediksi,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $response->json('message') ?? 'Gagal melakukan prediksi.',
            ], $response->status());

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat terhubung ke Flask API. Pastikan Flask API berjalan di ' . self::flaskUrl(),
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Halaman Ringkasan Hasil Prediksi.
     */
    public function summaryIndex(Request $request)
    {
        $locations = Asset::select('lokasi')
            ->distinct()
            ->whereNotNull('lokasi')
            ->where('lokasi', '<>', '')
            ->orderBy('lokasi', 'asc')
            ->pluck('lokasi');

        // Subquery to get latest prediction ID per asset
        $latestPredictionIds = DB::table('t_hasil_prediksi')
            ->select(DB::raw('MAX(id_prediksi) as max_id'))
            ->groupBy('id_aset');

        $query = HasilPrediksi::with('asset')
            ->whereIn('id_prediksi', $latestPredictionIds);

        // Filters
        if ($request->filled('label')) {
            $query->where('hasil_prediksi', $request->label);
        }

        if ($request->filled('location')) {
            $location = $request->location;
            $query->whereHas('asset', function ($q) use ($location) {
                $q->where('lokasi', $location);
            });
        }

        $predictions = $query->latest('tgl_prediksi')->paginate(15)->withQueryString();

        // Calculate highlights metrics
        $totalPredicted = HasilPrediksi::whereIn('id_prediksi', $latestPredictionIds)->count();
        $needsService = HasilPrediksi::whereIn('id_prediksi', $latestPredictionIds)
            ->where('hasil_prediksi', 'Perlu Servis')
            ->count();
        $notEligible = HasilPrediksi::whereIn('id_prediksi', $latestPredictionIds)
            ->where('hasil_prediksi', 'Tidak Layak')
            ->count();

        return view('prediksi.summary', compact('predictions', 'locations', 'totalPredicted', 'needsService', 'notEligible'));
    }

    /**
     * Mendapatkan list item di flat dataset untuk keperluan prediksi massal.
     */
    public function getDatasetItems()
    {
        $items = NaiveBayesDataset::with('asset:id_aset,kode_brg,nama_brg')
            ->get(['id_dataset', 'id_aset']);
            
        return response()->json([
            'success' => true,
            'items' => $items->map(function ($item) {
                return [
                    'id_dataset' => $item->id_dataset,
                    'id_aset' => $item->id_aset,
                    'kode_brg' => $item->asset->kode_brg ?? '-',
                    'nama_brg' => $item->asset->nama_brg ?? '-',
                ];
            })
        ]);
    }

    /**
     * Memproses prediksi untuk satu baris dataset massal.
     */
    public function predictDatasetItem($id)
    {
        $row = NaiveBayesDataset::find($id);
        if (!$row) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        try {
            $features = [
                'kondisi_brg' => $row->kondisi_brg,
                'usia_pakai' => (int)$row->usia_pakai,
                'frq_kerusakan' => (int)$row->frq_kerusakan,
                'jenis_pm' => $row->jenis_pm,
                'interval_pm' => (int)$row->interval_pm,
                'efi_out' => $row->efi_out,
                'downtime' => (float)$row->downtime,
                'lingkungan' => $row->lingkungan,
                'daya_listrik' => $row->daya_listrik,
                'sparepart' => $row->sparepart,
            ];

            $response = self::flaskHttp()->timeout(15)->post(self::flaskUrl() . '/predict', $features);

            if ($response->successful()) {
                $data = $response->json();

                // Simpan/update hasil prediksi ke t_hasil_prediksi
                $hasil = HasilPrediksi::create([
                    'id_dataset' => $row->id_dataset,
                    'id_aset' => $row->id_aset,
                    'tgl_prediksi' => now(),
                    'hasil_prediksi' => $data['predicted_class'],
                    'prob_layak' => $data['probabilities']['Layak'] ?? 0,
                    'prob_servis' => $data['probabilities']['Perlu Servis'] ?? 0,
                    'prob_tidak_layak' => $data['probabilities']['Tidak Layak'] ?? 0,
                ]);

                return response()->json([
                    'success' => true,
                    'predicted_class' => $data['predicted_class'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Flask API error.'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memproses batch prediksi kelayakan untuk seluruh aset di dataset sekaligus (Optimized).
     */
    public function predictAllOptimized()
    {
        $rows = NaiveBayesDataset::all();
        if ($rows->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Dataset kosong. Silakan generate dataset terlebih dahulu.',
            ], 400);
        }

        // Map database records to payload for Flask predict_batch
        $payload = $rows->map(function ($row) {
            return [
                'id_dataset' => $row->id_dataset,
                'id_aset' => $row->id_aset,
                'kondisi_brg' => $row->kondisi_brg,
                'usia_pakai' => (int)$row->usia_pakai,
                'frq_kerusakan' => (int)$row->frq_kerusakan,
                'jenis_pm' => $row->jenis_pm,
                'interval_pm' => (int)$row->interval_pm,
                'efi_out' => $row->efi_out,
                'downtime' => (float)$row->downtime,
                'lingkungan' => $row->lingkungan,
                'daya_listrik' => $row->daya_listrik,
                'sparepart' => $row->sparepart,
            ];
        })->toArray();

        try {
            // Send one single request to Flask API /predict_batch
            $response = self::flaskHttp()->timeout(120)->post(self::flaskUrl() . '/predict_batch', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $results = $data['results'] ?? [];

                $insertData = [];
                $totals = [
                    'Layak' => 0,
                    'Perlu Servis' => 0,
                    'Tidak Layak' => 0,
                ];

                $tgl_prediksi = now();

                foreach ($results as $res) {
                    $insertData[] = [
                        'id_dataset' => $res['id_dataset'],
                        'id_aset' => $res['id_aset'],
                        'tgl_prediksi' => $tgl_prediksi,
                        'hasil_prediksi' => $res['predicted_class'],
                        'prob_layak' => $res['probabilities']['Layak'] ?? 0.0,
                        'prob_servis' => $res['probabilities']['Perlu Servis'] ?? 0.0,
                        'prob_tidak_layak' => $res['probabilities']['Tidak Layak'] ?? 0.0,
                    ];

                    $label = $res['predicted_class'];
                    if (isset($totals[$label])) {
                        $totals[$label]++;
                    }
                }

                // Bulk insert with a single INSERT statement
                if (!empty($insertData)) {
                    HasilPrediksi::insert($insertData);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Batch prediksi berhasil diselesaikan.',
                    'totals' => $totals,
                    'total_processed' => count($insertData)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $response->json('message') ?? 'Flask API mengembalikan kesalahan saat batch prediksi.'
            ], $response->status());

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat terhubung ke Flask API. Pastikan Flask API berjalan di ' . self::flaskUrl()
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Halaman Evaluasi Model Naive Bayes (Read-Only).
     */
    public function evaluationIndex()
    {
        // SELECT only: Join hasil prediksi dengan ground truth dari dataset
        $records = DB::table('t_hasil_prediksi')
            ->join('t_naive_bayes_dataset', 't_hasil_prediksi.id_dataset', '=', 't_naive_bayes_dataset.id_dataset')
            ->select('t_hasil_prediksi.hasil_prediksi', 't_naive_bayes_dataset.kelas_label')
            ->get();

        $classes = ['Layak', 'Perlu Servis', 'Tidak Layak'];

        // Inisialisasi confusion matrix
        $confusionMatrix = [];
        foreach ($classes as $actual) {
            foreach ($classes as $predicted) {
                $confusionMatrix[$actual][$predicted] = 0;
            }
        }

        $total = count($records);
        $correct = 0;

        $tp = ['Layak' => 0, 'Perlu Servis' => 0, 'Tidak Layak' => 0];
        $fp = ['Layak' => 0, 'Perlu Servis' => 0, 'Tidak Layak' => 0];
        $fn = ['Layak' => 0, 'Perlu Servis' => 0, 'Tidak Layak' => 0];
        $support = ['Layak' => 0, 'Perlu Servis' => 0, 'Tidak Layak' => 0];
        $predictionDistribution = ['Layak' => 0, 'Perlu Servis' => 0, 'Tidak Layak' => 0];

        foreach ($records as $r) {
            $act = $r->kelas_label;
            $pred = $r->hasil_prediksi;

            if (in_array($act, $classes) && in_array($pred, $classes)) {
                $confusionMatrix[$act][$pred]++;
                $predictionDistribution[$pred]++;
                $support[$act]++;

                if ($act === $pred) {
                    $correct++;
                    $tp[$act]++;
                } else {
                    $fp[$pred]++;
                    $fn[$act]++;
                }
            }
        }

        $accuracy = $total > 0 ? ($correct / $total) : 0;

        $metrics = [];
        foreach ($classes as $c) {
            $pVal = ($tp[$c] + $fp[$c]) > 0 ? ($tp[$c] / ($tp[$c] + $fp[$c])) : 0;
            $rVal = ($tp[$c] + $fn[$c]) > 0 ? ($tp[$c] / ($tp[$c] + $fn[$c])) : 0;
            $f1Val = ($pVal + $rVal) > 0 ? (2 * ($pVal * $rVal) / ($pVal + $rVal)) : 0;

            $metrics[$c] = [
                'precision' => $pVal,
                'recall' => $rVal,
                'f1' => $f1Val,
                'support' => $support[$c],
            ];
        }

        return view('prediksi.evaluation', compact(
            'records', 
            'confusionMatrix', 
            'metrics', 
            'accuracy', 
            'total', 
            'predictionDistribution',
            'classes'
        ));
    }

    /**
     * Halaman Laporan Hasil Prediksi Kelayakan Aset (Read-Only).
     */
    public function predictionReport(Request $request)
    {
        $locations = Asset::select('lokasi')
            ->distinct()
            ->whereNotNull('lokasi')
            ->where('lokasi', '<>', '')
            ->orderBy('lokasi', 'asc')
            ->pluck('lokasi');

        // Subquery to get latest prediction ID per asset
        $latestPredictionIds = DB::table('t_hasil_prediksi')
            ->select(DB::raw('MAX(id_prediksi) as max_id'))
            ->groupBy('id_aset');

        $query = HasilPrediksi::with('asset')
            ->whereIn('id_prediksi', $latestPredictionIds);

        // Filters
        if ($request->filled('label')) {
            $query->where('hasil_prediksi', $request->label);
        }

        if ($request->filled('location')) {
            $location = $request->location;
            $query->whereHas('asset', function ($q) use ($location) {
                $q->where('lokasi', $location);
            });
        }

        // Export handles
        $export = $request->input('export');

        if ($export === 'print') {
            // Get all results without pagination for printing
            $data = $query->latest('tgl_prediksi')->get();
            return view('prediksi.print_report', compact('data'));
        }

        if ($export === 'excel' || $export === 'csv') {
            $data = $query->latest('tgl_prediksi')->get();
            $filename = 'laporan-prediksi-kelayakan-' . date('Y-m-d');
            $title = 'Laporan Prediksi Kelayakan Aset Laboratorium';
            $headers = ['No', 'Kode Aset', 'Nama Aset', 'Lokasi', 'Tanggal Prediksi', 'Hasil Kelayakan', 'Probabilitas Terbesar', 'Rekomendasi Tindakan'];
            $rows = [];

            foreach ($data as $index => $row) {
                $topProb = max($row->prob_layak, $row->prob_servis, $row->prob_tidak_layak) * 100;
                
                $recom = match ($row->hasil_prediksi) {
                    'Layak' => 'Teruskan Penggunaan (Continue Use)',
                    'Perlu Servis' => 'Jadwalkan Pemeliharaan (Schedule Maintenance)',
                    'Tidak Layak' => 'Ganti / Hapus Aset (Replace/Dispose)',
                    default => '-'
                };

                $rows[] = [
                    $index + 1,
                    $row->asset->kode_brg ?? '-',
                    $row->asset->nama_brg ?? '-',
                    $row->asset->lokasi ?? '-',
                    $row->tgl_prediksi ? $row->tgl_prediksi->format('d-m-Y H:i') : '-',
                    $row->hasil_prediksi,
                    number_format($topProb, 2) . '%',
                    $recom
                ];
            }

            if ($export === 'excel') {
                return $this->exportToExcel($filename, $title, $headers, $rows);
            } else {
                return $this->exportToCsv($filename, $headers, $rows);
            }
        }

        // Standard Page Render with Pagination
        $predictions = $query->latest('tgl_prediksi')->paginate(15)->withQueryString();

        // Calculate highlights metrics based on current filters
        $totalPredicted = HasilPrediksi::whereIn('id_prediksi', $latestPredictionIds)->count();
        $needsService = HasilPrediksi::whereIn('id_prediksi', $latestPredictionIds)
            ->where('hasil_prediksi', 'Perlu Servis')
            ->count();
        $notEligible = HasilPrediksi::whereIn('id_prediksi', $latestPredictionIds)
            ->where('hasil_prediksi', 'Tidak Layak')
            ->count();

        return view('prediksi.report', compact(
            'predictions',
            'locations',
            'totalPredicted',
            'needsService',
            'notEligible'
        ));
    }
}

