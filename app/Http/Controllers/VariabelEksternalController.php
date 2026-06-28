<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VariabelEksternal;
use App\Models\Asset;
use App\Traits\CanExportData;

class VariabelEksternalController extends Controller
{
    use CanExportData;
    /**
     * Daftar semua data variabel eksternal dengan filter dan search.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterLingkungan = $request->input('lingkungan');
        $filterDayaListrik = $request->input('daya_listrik');
        $filterSparepart = $request->input('sparepart');
        $filterAnggaran = $request->input('anggaran');
        $filterExtEffect = $request->input('ext_effect');

        $query = VariabelEksternal::with('asset');

        if ($search) {
            $query->whereHas('asset', function ($q) use ($search) {
                $q->where('kode_brg', 'like', "%{$search}%")
                  ->orWhere('nama_brg', 'like', "%{$search}%");
            });
        }

        if ($filterLingkungan) {
            $query->where('lingkungan', $filterLingkungan);
        }

        if ($filterDayaListrik) {
            $query->where('daya_listrik', $filterDayaListrik);
        }

        if ($filterSparepart) {
            $query->where('sparepart', $filterSparepart);
        }

        if ($filterAnggaran) {
            $query->where('anggaran', $filterAnggaran);
        }

        if ($filterExtEffect) {
            $query->where('ext_effect', $filterExtEffect);
        }

        $rows = $query->orderBy('tgl_observasi', 'desc')->paginate(15);
        $assets = Asset::orderBy('nama_brg')->get();

        return view('variabel-eksternal.index', compact(
            'rows', 'assets', 'search', 
            'filterLingkungan', 'filterDayaListrik', 
            'filterSparepart', 'filterAnggaran', 'filterExtEffect'
        ));
    }

    /**
     * Form tambah data variabel eksternal.
     */
    public function create()
    {
        $assets = Asset::orderBy('nama_brg')->get();
        return view('variabel-eksternal.create', compact('assets'));
    }

    /**
     * Simpan data variabel eksternal baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_aset' => 'required|exists:t_aset,id_aset',
            'tgl_observasi' => 'required|date',
            'lingkungan' => 'required|in:Baik,Cukup,Buruk',
            'daya_listrik' => 'required|in:Stabil,Tidak Stabil,Sering Padam',
            'sparepart' => 'required|in:Tersedia,Terbatas,Tidak Ada',
            'anggaran' => 'required|in:Mendukung,Terbatas,Tidak Ada',
            'ext_effect' => 'required|in:Rendah,Sedang,Tinggi',
        ]);

        VariabelEksternal::create($validated);

        return redirect()->route('variabel.index')
                        ->with('success', 'Data variabel eksternal berhasil ditambahkan!');
    }

    /**
     * Form edit data variabel eksternal.
     */
    public function edit($id)
    {
        $variabel = VariabelEksternal::with('asset')->findOrFail($id);
        $assets = Asset::orderBy('nama_brg')->get();
        return view('variabel-eksternal.edit', compact('variabel', 'assets'));
    }

    /**
     * Update data variabel eksternal.
     */
    public function update(Request $request, $id)
    {
        $variabel = VariabelEksternal::findOrFail($id);

        $validated = $request->validate([
            'id_aset' => 'required|exists:t_aset,id_aset',
            'tgl_observasi' => 'required|date',
            'lingkungan' => 'required|in:Baik,Cukup,Buruk',
            'daya_listrik' => 'required|in:Stabil,Tidak Stabil,Sering Padam',
            'sparepart' => 'required|in:Tersedia,Terbatas,Tidak Ada',
            'anggaran' => 'required|in:Mendukung,Terbatas,Tidak Ada',
            'ext_effect' => 'required|in:Rendah,Sedang,Tinggi',
        ]);

        $variabel->update($validated);

        return redirect()->route('variabel.index')
                        ->with('success', 'Data variabel eksternal berhasil diperbarui!');
    }

    /**
     * Hapus data variabel eksternal.
     */
    public function destroy($id)
    {
        $variabel = VariabelEksternal::findOrFail($id);
        $variabel->delete();

        return redirect()->route('variabel.index')
                        ->with('success', 'Data variabel eksternal berhasil dihapus!');
    }

    /**
     * Riwayat variabel eksternal per aset.
     */
    public function history($idAset)
    {
        $asset = Asset::findOrFail($idAset);
        $rows = VariabelEksternal::where('id_aset', $idAset)
                    ->orderBy('tgl_observasi', 'desc')
                    ->paginate(20);

        return view('variabel-eksternal.history', compact('asset', 'rows'));
    }

    /**
     * Export data to CSV.
     */
    public function exportCsv()
    {
        $rows = VariabelEksternal::with('asset')->orderBy('tgl_observasi', 'desc')->get();
        $headers = ['ID Eksternal', 'Kode Aset', 'Nama Aset', 'Tanggal Observasi', 'Lingkungan', 'Daya Listrik', 'Sparepart', 'Anggaran', 'Efek Eksternal'];
        
        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                $row->id_eksternal,
                $row->asset->kode_brg ?? '-',
                $row->asset->nama_brg ?? '-',
                $row->tgl_observasi ? $row->tgl_observasi->format('Y-m-d') : '-',
                $row->lingkungan,
                $row->daya_listrik,
                $row->sparepart,
                $row->anggaran,
                $row->ext_effect,
            ];
        }

        return $this->exportToCsv('riwayat-variabel-eksternal-' . date('Y-m-d'), $headers, $data);
    }

    /**
     * Export data to Excel (XLS).
     */
    public function exportExcel()
    {
        $rows = VariabelEksternal::with('asset')->orderBy('tgl_observasi', 'desc')->get();
        $headers = ['ID Eksternal', 'Kode Aset', 'Nama Aset', 'Tanggal Observasi', 'Lingkungan', 'Daya Listrik', 'Sparepart', 'Anggaran', 'Efek Eksternal'];
        
        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                $row->id_eksternal,
                $row->asset->kode_brg ?? '-',
                $row->asset->nama_brg ?? '-',
                $row->tgl_observasi ? $row->tgl_observasi->format('Y-m-d') : '-',
                $row->lingkungan,
                $row->daya_listrik,
                $row->sparepart,
                $row->anggaran,
                $row->ext_effect,
            ];
        }

        return $this->exportToExcel('riwayat-variabel-eksternal-' . date('Y-m-d'), 'Riwayat Variabel Eksternal Aset', $headers, $data);
    }
}
