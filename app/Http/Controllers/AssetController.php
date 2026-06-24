<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

class AssetController extends Controller
{
    private $asalUsulOptions = ['Pembelian', 'Hibah', 'Dropping Dinas', 'Dana BOS'];

    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter_asal = $request->input('asal_usul');

        $query = Asset::query();

        if ($search) {
            $query->where('kode_brg', 'like', "%{$search}%")
                  ->orWhere('nama_brg', 'like', "%{$search}%")
                  ->orWhere('merk_tipe', 'like', "%{$search}%");
        }

        if ($filter_asal) {
            $query->where('asal_usul', $filter_asal);
        }

        $items = $query->latest('created_at')->paginate(15);

        return view('assets.index', compact('items', 'search', 'filter_asal'));
    }

    public function create()
    {
        $asalUsul = $this->asalUsulOptions;
        return view('assets.create', compact('asalUsul'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_brg' => 'required|unique:t_aset|max:20',
            'nama_brg' => 'required|max:100',
            'merk_tipe' => 'nullable|max:80',
            'spesifikasi' => 'nullable|string',
            'lokasi' => 'nullable|max:60',
            'thn_perolehan' => 'nullable|digits:4',
            'harga_perolehan' => 'nullable|numeric|min:0',
            'asal_usul' => 'required|in:Pembelian,Hibah,Dropping Dinas,Dana BOS',
        ]);

        Asset::create($validated);

        return redirect()->route('assets.index')
                        ->with('success', 'Aset berhasil ditambahkan!');
    }

    public function edit(Asset $asset)
    {
        $asalUsul = $this->asalUsulOptions;
        return view('assets.edit', compact('asset', 'asalUsul'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'kode_brg' => 'required|max:20|unique:t_aset,kode_brg,' . $asset->id_aset . ',id_aset',
            'nama_brg' => 'required|max:100',
            'merk_tipe' => 'nullable|max:80',
            'spesifikasi' => 'nullable|string',
            'lokasi' => 'nullable|max:60',
            'thn_perolehan' => 'nullable|digits:4',
            'harga_perolehan' => 'nullable|numeric|min:0',
            'asal_usul' => 'required|in:Pembelian,Hibah,Dropping Dinas,Dana BOS',
        ]);

        $asset->update($validated);

        return redirect()->route('assets.index')
                        ->with('success', 'Aset berhasil diperbarui!');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();

        return redirect()->route('assets.index')
                        ->with('success', 'Aset berhasil dihapus!');
    }
}

