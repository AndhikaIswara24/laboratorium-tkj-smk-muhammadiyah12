<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\KondisiFisik;
use App\Models\Pemeliharaan;
use App\Models\Efisiensi;
use App\Models\VariabelEksternal;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Fetch real database metrics
        $totalAssets = Asset::count();
        $countKondisi = KondisiFisik::count();
        $countPemeliharaan = Pemeliharaan::count();
        $countEfisiensi = Efisiensi::count();
        $countVariabel = VariabelEksternal::count();

        // Label distribution for Chart.js
        $distribKondisi = [
            'Layak' => KondisiFisik::where('kelas_label', 'Layak')->count(),
            'Perlu Servis' => KondisiFisik::where('kelas_label', 'Perlu Servis')->count(),
            'Tidak Layak' => KondisiFisik::where('kelas_label', 'Tidak Layak')->count(),
        ];

        // Kelengkapan data per aset
        $assets = Asset::withCount(['kondisiFisik', 'pemeliharaan', 'efisiensi', 'variabelEksternal'])->get();

        $incompleteAssets = [];
        foreach ($assets as $asset) {
            $missing = [];
            if ($asset->kondisi_fisik_count == 0) $missing[] = 'Kondisi Fisik';
            if ($asset->pemeliharaan_count == 0) $missing[] = 'Pemeliharaan';
            if ($asset->efisiensi_count == 0) $missing[] = 'Efisiensi';
            if ($asset->variabel_eksternal_count == 0) $missing[] = 'Variabel Eksternal';

            if (count($missing) > 0) {
                $incompleteAssets[] = [
                    'id_aset' => $asset->id_aset,
                    'kode_brg' => $asset->kode_brg,
                    'nama_brg' => $asset->nama_brg,
                    'missing' => $missing,
                    'score' => 4 - count($missing)
                ];
            }
        }

        if ($user->role === 'admin') {
            return view('dashboard.admin', compact(
                'totalAssets', 'countKondisi', 'countPemeliharaan', 'countEfisiensi', 'countVariabel', 
                'distribKondisi', 'incompleteAssets'
            ));
        }

        if ($user->role === 'teknisi') {
            return view('dashboard.teknisi', compact(
                'totalAssets', 'countKondisi', 'countPemeliharaan', 'countEfisiensi', 'countVariabel', 
                'distribKondisi', 'incompleteAssets'
            ));
        }

        return view('dashboard.user', compact(
            'totalAssets', 'countKondisi', 'countPemeliharaan', 'countEfisiensi', 'countVariabel', 
            'distribKondisi', 'incompleteAssets'
        ));
    }
}
