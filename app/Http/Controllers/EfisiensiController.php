<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Efisiensi;
use App\Models\Asset;

class EfisiensiController extends Controller
{
    /**
     * Daftar semua data efisiensi dengan search & filter.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterPenggunaan = $request->input('penggunaan');
        $filterPerform = $request->input('perform');
        $filterEfiOut = $request->input('efi_out');

        $query = Efisiensi::with('asset');

        if ($search) {
            $query->whereHas('asset', function ($q) use ($search) {
                $q->where('kode_brg', 'like', "%{$search}%")
                  ->orWhere('nama_brg', 'like', "%{$search}%");
            });
        }

        if ($filterPenggunaan) {
            $query->where('penggunaan', $filterPenggunaan);
        }

        if ($filterPerform) {
            $query->where('perform', $filterPerform);
        }

        if ($filterEfiOut) {
            $query->where('efi_out', $filterEfiOut);
        }

        $rows = $query->orderBy('tgl_observasi', 'desc')->paginate(15);
        $assets = Asset::orderBy('nama_brg')->get();

        return view('efisiensi.index', compact('rows', 'assets', 'search', 'filterPenggunaan', 'filterPerform', 'filterEfiOut'));
    }

    /**
     * Form tambah data efisiensi.
     */
    public function create()
    {
        $assets = Asset::orderBy('nama_brg')->get();
        return view('efisiensi.create', compact('assets'));
    }

    /**
     * Simpan data efisiensi baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_aset' => 'required|exists:t_aset,id_aset',
            'tgl_observasi' => 'required|date',
            'jam_ops' => 'required|numeric|min:0',
            'penggunaan' => 'required|in:Tinggi,Sedang,Tidak Pakai',
            'jml_user' => 'required|integer|min:0',
            'downtime' => 'required|numeric|min:0',
            'perform' => 'required|in:Normal,Lambat,Mati',
            'umur_ekonomis' => 'required|integer|min:0',
            'efi_out' => 'required|in:Tinggi,Sedang,Rendah',
        ]);

        Efisiensi::create($validated);

        return redirect()->route('efisiensi.index')
                        ->with('success', 'Data efisiensi berhasil ditambahkan!');
    }

    /**
     * Form edit data efisiensi.
     */
    public function edit($id)
    {
        $efisiensi = Efisiensi::with('asset')->findOrFail($id);
        $assets = Asset::orderBy('nama_brg')->get();
        return view('efisiensi.edit', compact('efisiensi', 'assets'));
    }

    /**
     * Update data efisiensi.
     */
    public function update(Request $request, $id)
    {
        $efisiensi = Efisiensi::findOrFail($id);

        $validated = $request->validate([
            'id_aset' => 'required|exists:t_aset,id_aset',
            'tgl_observasi' => 'required|date',
            'jam_ops' => 'required|numeric|min:0',
            'penggunaan' => 'required|in:Tinggi,Sedang,Tidak Pakai',
            'jml_user' => 'required|integer|min:0',
            'downtime' => 'required|numeric|min:0',
            'perform' => 'required|in:Normal,Lambat,Mati',
            'umur_ekonomis' => 'required|integer|min:0',
            'efi_out' => 'required|in:Tinggi,Sedang,Rendah',
        ]);

        $efisiensi->update($validated);

        return redirect()->route('efisiensi.index')
                        ->with('success', 'Data efisiensi berhasil diperbarui!');
    }

    /**
     * Hapus data efisiensi.
     */
    public function destroy($id)
    {
        $efisiensi = Efisiensi::findOrFail($id);
        $efisiensi->delete();

        return redirect()->route('efisiensi.index')
                        ->with('success', 'Data efisiensi berhasil dihapus!');
    }

    /**
     * Riwayat efisiensi per aset.
     */
    public function history($idAset)
    {
        $asset = Asset::findOrFail($idAset);
        $rows = Efisiensi::where('id_aset', $idAset)
                    ->orderBy('tgl_observasi', 'desc')
                    ->paginate(20);

        return view('efisiensi.history', compact('asset', 'rows'));
    }
}
