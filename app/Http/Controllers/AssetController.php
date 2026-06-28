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

        // Fetch distribution of Asal Usul for Chart.js
        $distribAsal = [
            'Pembelian' => Asset::where('asal_usul', 'Pembelian')->count(),
            'Hibah' => Asset::where('asal_usul', 'Hibah')->count(),
            'Dropping Dinas' => Asset::where('asal_usul', 'Dropping Dinas')->count(),
            'Dana BOS' => Asset::where('asal_usul', 'Dana BOS')->count(),
        ];

        return view('assets.index', compact('items', 'search', 'filter_asal', 'distribAsal'));
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

    /**
     * Import assets from uploaded CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        // Native CSV reading
        if (($handle = fopen($path, 'r')) !== FALSE) {
            // Read header
            $header = fgetcsv($handle, 1000, ',');
            if (!$header) {
                return redirect()->route('assets.index')->with('error', 'File CSV kosong.');
            }

            // Normalize header names (lowercase, remove spaces)
            $header = array_map(function($h) {
                return strtolower(trim(str_replace([' ', '_'], '', $h)));
            }, $header);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                // Skip empty rows
                if (empty($data) || count($data) < 2) continue;

                // Map header to values
                $row = array_combine(array_intersect_key($header, $data), array_intersect_key($data, $header));
                
                // Fallback map if header mapping fails
                $kode_brg = $row['kodebarang'] ?? $row['kodebrg'] ?? $data[0] ?? null;
                $nama_brg = $row['namabarang'] ?? $row['namabrg'] ?? $data[1] ?? null;
                $merk_tipe = $row['merktipe'] ?? $data[2] ?? null;
                $spesifikasi = $row['spesifikasi'] ?? $data[3] ?? null;
                $lokasi = $row['lokasi'] ?? $data[4] ?? null;
                $thn_perolehan = $row['tahunperolehan'] ?? $row['thnperolehan'] ?? $data[5] ?? null;
                $harga_perolehan = $row['hargaperolehan'] ?? $data[6] ?? null;
                $asal_usul = $row['asalusul'] ?? $data[7] ?? 'Pembelian';

                if (!$kode_brg || !$nama_brg) {
                    $errorCount++;
                    $errors[] = "Baris dengan data kosong dilewati.";
                    continue;
                }

                // Clean and normalize input
                $asal_usul = trim($asal_usul);
                $asal_usul = in_array($asal_usul, ['Pembelian', 'Hibah', 'Dropping Dinas', 'Dana BOS']) ? $asal_usul : 'Pembelian';
                $harga_perolehan = is_numeric($harga_perolehan) ? $harga_perolehan : null;
                $thn_perolehan = is_numeric($thn_perolehan) && strlen($thn_perolehan) == 4 ? $thn_perolehan : null;

                try {
                    Asset::updateOrCreate(
                        ['kode_brg' => trim($kode_brg)],
                        [
                            'nama_brg' => trim($nama_brg),
                            'merk_tipe' => $merk_tipe ? trim($merk_tipe) : null,
                            'spesifikasi' => $spesifikasi ? trim($spesifikasi) : null,
                            'lokasi' => $lokasi ? trim($lokasi) : null,
                            'thn_perolehan' => $thn_perolehan,
                            'harga_perolehan' => $harga_perolehan,
                            'asal_usul' => $asal_usul
                        ]
                    );
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Gagal menyimpan kode " . $kode_brg . ": " . $e->getMessage();
                }
            }
            fclose($handle);

            $msg = "Berhasil mengimpor {$successCount} aset.";
            if ($errorCount > 0) {
                $msg .= " Gagal {$errorCount} baris. Detail: " . implode(', ', array_slice($errors, 0, 3));
            }

            return redirect()->route('assets.index')->with('success', $msg);
        }

        return redirect()->route('assets.index')->with('error', 'Gagal membaca file CSV.');
    }
}

