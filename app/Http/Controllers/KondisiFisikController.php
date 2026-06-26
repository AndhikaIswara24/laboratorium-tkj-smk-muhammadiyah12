<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KondisiFisik;
use App\Models\Asset;

class KondisiFisikController extends Controller
{
    /**
     * Daftar semua data kondisi fisik dengan filter dan search.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterKondisi = $request->input('kondisi_brg');
        $filterLabel = $request->input('kelas_label');

        $query = KondisiFisik::with('asset');

        if ($search) {
            $query->whereHas('asset', function ($q) use ($search) {
                $q->where('kode_brg', 'like', "%{$search}%")
                  ->orWhere('nama_brg', 'like', "%{$search}%");
            });
        }

        if ($filterKondisi) {
            $query->where('kondisi_brg', $filterKondisi);
        }

        if ($filterLabel) {
            $query->where('kelas_label', $filterLabel);
        }

        $rows = $query->latest('tgl_observasi')->paginate(15);
        $assets = Asset::orderBy('nama_brg')->get();

        return view('kondisi-fisik.index', compact('rows', 'assets', 'search', 'filterKondisi', 'filterLabel'));
    }

    /**
     * Form tambah data kondisi fisik.
     */
    public function create()
    {
        $assets = Asset::orderBy('nama_brg')->get();
        return view('kondisi-fisik.create', compact('assets'));
    }

    /**
     * Simpan data kondisi fisik baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_aset' => 'required|exists:t_aset,id_aset',
            'tgl_observasi' => 'required|date',
            'kondisi_brg' => 'required|in:B,RR,RB',
            'ket_teknis' => 'required|in:Normal,Lemah,Lambat,Mati Total',
            'frq_kerusakan' => 'required|integer|min:0',
            'kelas_label' => 'required|in:Layak,Perlu Servis,Tidak Layak',
        ]);

        // Hitung usia_pakai otomatis dari tahun sekarang - thn_perolehan
        $asset = Asset::findOrFail($validated['id_aset']);
        $validated['usia_pakai'] = $asset->thn_perolehan
            ? (int) date('Y') - (int) $asset->thn_perolehan
            : 0;

        KondisiFisik::create($validated);

        return redirect()->route('kondisi.index')
                        ->with('success', 'Data kondisi fisik berhasil ditambahkan!');
    }

    /**
     * Form edit data kondisi fisik.
     */
    public function edit($id)
    {
        $kondisi = KondisiFisik::with('asset')->findOrFail($id);
        $assets = Asset::orderBy('nama_brg')->get();
        return view('kondisi-fisik.edit', compact('kondisi', 'assets'));
    }

    /**
     * Update data kondisi fisik.
     */
    public function update(Request $request, $id)
    {
        $kondisi = KondisiFisik::findOrFail($id);

        $validated = $request->validate([
            'id_aset' => 'required|exists:t_aset,id_aset',
            'tgl_observasi' => 'required|date',
            'kondisi_brg' => 'required|in:B,RR,RB',
            'ket_teknis' => 'required|in:Normal,Lemah,Lambat,Mati Total',
            'frq_kerusakan' => 'required|integer|min:0',
            'kelas_label' => 'required|in:Layak,Perlu Servis,Tidak Layak',
        ]);

        // Hitung usia_pakai otomatis
        $asset = Asset::findOrFail($validated['id_aset']);
        $validated['usia_pakai'] = $asset->thn_perolehan
            ? (int) date('Y') - (int) $asset->thn_perolehan
            : 0;

        $kondisi->update($validated);

        return redirect()->route('kondisi.index')
                        ->with('success', 'Data kondisi fisik berhasil diperbarui!');
    }

    /**
     * Hapus data kondisi fisik.
     */
    public function destroy($id)
    {
        $kondisi = KondisiFisik::findOrFail($id);
        $kondisi->delete();

        return redirect()->route('kondisi.index')
                        ->with('success', 'Data kondisi fisik berhasil dihapus!');
    }

    /**
     * Riwayat kondisi fisik per aset.
     */
    public function history($idAset)
    {
        $asset = Asset::findOrFail($idAset);
        $rows = KondisiFisik::where('id_aset', $idAset)
                    ->latest('tgl_observasi')
                    ->paginate(20);

        return view('kondisi-fisik.history', compact('asset', 'rows'));
    }

    /**
     * API: Ambil data aset untuk perhitungan usia_pakai otomatis (AJAX).
     */
    public function getAssetData($id)
    {
        $asset = Asset::findOrFail($id);
        $usiaPakai = $asset->thn_perolehan
            ? (int) date('Y') - (int) $asset->thn_perolehan
            : 0;

        return response()->json([
            'id_aset' => $asset->id_aset,
            'kode_brg' => $asset->kode_brg,
            'nama_brg' => $asset->nama_brg,
            'thn_perolehan' => $asset->thn_perolehan,
            'usia_pakai' => $usiaPakai,
        ]);
    }
}
