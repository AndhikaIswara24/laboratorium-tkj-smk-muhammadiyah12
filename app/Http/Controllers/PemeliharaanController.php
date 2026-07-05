<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemeliharaan;
use App\Models\Asset;
use App\Traits\CanExportData;

class PemeliharaanController extends Controller
{
    use CanExportData;
    /**
     * Daftar semua data pemeliharaan dengan search & filter.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterJenis = $request->input('jenis_pm');
        $filterKondisi = $request->input('kon_after');

        $query = Pemeliharaan::with('asset');

        if ($search) {
            $query->whereHas('asset', function ($q) use ($search) {
                $q->where('kode_brg', 'like', "%{$search}%")
                  ->orWhere('nama_brg', 'like', "%{$search}%");
            });
        }

        if ($filterJenis) {
            $query->where('jenis_pm', $filterJenis);
        }

        if ($filterKondisi) {
            $query->where('kon_after', $filterKondisi);
        }

        $rows = $query->orderBy('tgl_pm', 'desc')->paginate(15);
        $assets = Asset::orderBy('nama_brg')->get();

        return view('pemeliharaan.index', compact('rows', 'assets', 'search', 'filterJenis', 'filterKondisi'));
    }

    /**
     * Form tambah data pemeliharaan.
     */
    public function create()
    {
        $assets = Asset::whereDoesntHave('pemeliharaan')->orderBy('nama_brg')->get();
        return view('pemeliharaan.create', compact('assets'));
    }

    /**
     * Simpan data pemeliharaan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_aset' => 'required|exists:t_aset,id_aset|unique:t_pemeliharaan,id_aset',
            'tgl_pm' => 'required|date',
            'jenis_pm' => 'required|in:Preventif,Korektif,Tidak Ada',
            'interval_bulan' => 'required|integer|min:0',
            'pelaksana' => 'required|in:Teknisi Internal,Vendor Luar,Guru TKJ',
            'biaya_servis' => 'required|numeric|min:0',
            'kon_after' => 'required|in:B,RR,RB',
            'ket_pm' => 'nullable|string',
        ], [
            'id_aset.unique' => 'Aset ini sudah memiliki data pemeliharaan. Setiap aset hanya boleh diobservasi sekali.',
        ]);

        Pemeliharaan::create($validated);

        return redirect()->route('pemeliharaan.index')
                        ->with('success', 'Data pemeliharaan berhasil ditambahkan!');
    }

    /**
     * Form edit data pemeliharaan.
     */
    public function edit($id)
    {
        $pemeliharaan = Pemeliharaan::with('asset')->findOrFail($id);
        $assets = Asset::orderBy('nama_brg')->get();
        return view('pemeliharaan.edit', compact('pemeliharaan', 'assets'));
    }

    /**
     * Update data pemeliharaan.
     */
    public function update(Request $request, $id)
    {
        $pemeliharaan = Pemeliharaan::findOrFail($id);

        $validated = $request->validate([
            'id_aset' => 'required|exists:t_aset,id_aset|unique:t_pemeliharaan,id_aset,' . $id . ',id_pm',
            'tgl_pm' => 'required|date',
            'jenis_pm' => 'required|in:Preventif,Korektif,Tidak Ada',
            'interval_bulan' => 'required|integer|min:0',
            'pelaksana' => 'required|in:Teknisi Internal,Vendor Luar,Guru TKJ',
            'biaya_servis' => 'required|numeric|min:0',
            'kon_after' => 'required|in:B,RR,RB',
            'ket_pm' => 'nullable|string',
        ], [
            'id_aset.unique' => 'Aset ini sudah memiliki data pemeliharaan. Setiap aset hanya boleh diobservasi sekali.',
        ]);

        $pemeliharaan->update($validated);

        return redirect()->route('pemeliharaan.index')
                        ->with('success', 'Data pemeliharaan berhasil diperbarui!');
    }

    /**
     * Hapus data pemeliharaan.
     */
    public function destroy($id)
    {
        $pemeliharaan = Pemeliharaan::findOrFail($id);
        $pemeliharaan->delete();

        return redirect()->route('pemeliharaan.index')
                        ->with('success', 'Data pemeliharaan berhasil dihapus!');
    }

    /**
     * Riwayat pemeliharaan per aset.
     */
    public function history($idAset)
    {
        $asset = Asset::findOrFail($idAset);
        $rows = Pemeliharaan::where('id_aset', $idAset)
                    ->orderBy('tgl_pm', 'desc')
                    ->paginate(20);

        return view('pemeliharaan.history', compact('asset', 'rows'));
    }

    /**
     * Export data to CSV.
     */
    public function exportCsv()
    {
        $rows = Pemeliharaan::with('asset')->orderBy('tgl_pm', 'desc')->get();
        $headers = ['ID Pemeliharaan', 'Kode Aset', 'Nama Aset', 'Tanggal PM', 'Jenis PM', 'Interval (Bulan)', 'Pelaksana', 'Biaya Servis', 'Kondisi Sesudah', 'Keterangan'];
        
        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                $row->id_pm,
                $row->asset->kode_brg ?? '-',
                $row->asset->nama_brg ?? '-',
                $row->tgl_pm ? $row->tgl_pm->format('Y-m-d') : '-',
                $row->jenis_pm,
                $row->interval_bulan,
                $row->pelaksana,
                $row->biaya_servis,
                $row->kon_after,
                $row->ket_pm,
            ];
        }

        return $this->exportToCsv('riwayat-pemeliharaan-' . date('Y-m-d'), $headers, $data);
    }

    /**
     * Export data to Excel (XLS).
     */
    public function exportExcel()
    {
        $rows = Pemeliharaan::with('asset')->orderBy('tgl_pm', 'desc')->get();
        $headers = ['ID Pemeliharaan', 'Kode Aset', 'Nama Aset', 'Tanggal PM', 'Jenis PM', 'Interval (Bulan)', 'Pelaksana', 'Biaya Servis', 'Kondisi Sesudah', 'Keterangan'];
        
        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                $row->id_pm,
                $row->asset->kode_brg ?? '-',
                $row->asset->nama_brg ?? '-',
                $row->tgl_pm ? $row->tgl_pm->format('Y-m-d') : '-',
                $row->jenis_pm,
                $row->interval_bulan,
                $row->pelaksana,
                $row->biaya_servis,
                $row->kon_after,
                $row->ket_pm,
            ];
        }

        return $this->exportToExcel('riwayat-pemeliharaan-' . date('Y-m-d'), 'Riwayat Pemeliharaan Aset', $headers, $data);
    }
}
