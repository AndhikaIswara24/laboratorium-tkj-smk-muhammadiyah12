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
            $query->where(function ($q) use ($search) {
                $q->where('kode_brg', 'like', "%{$search}%")
                  ->orWhere('nama_brg', 'like', "%{$search}%")
                  ->orWhere('merk_tipe', 'like', "%{$search}%");
            });
        }

        if ($filter_asal) {
            $query->where('asal_usul', $filter_asal);
        }

        $items = $query->latest('created_at')->paginate(15)->withQueryString();

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
     * Delete all assets from the database.
     */
    public function destroyAll()
    {
        $count = Asset::count();

        if ($count === 0) {
            return redirect()->route('assets.index')
                            ->with('error', 'Tidak ada data aset untuk dihapus.');
        }

        try {
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
            Asset::truncate();
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            return redirect()->route('assets.index')
                            ->with('error', 'Gagal menghapus data aset: ' . $e->getMessage());
        }

        return redirect()->route('assets.index')
                        ->with('success', "Berhasil menghapus seluruh {$count} data aset.");
    }

    /**
     * Import assets from uploaded CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            // limit uploads to 10MB and only csv/txt mime types
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        // Native CSV reading
        if (($handle = fopen($path, 'r')) !== FALSE) {
            // Remove BOM if present (common in Excel-exported CSV)
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            // Read header (0 = unlimited line length)
            $header = fgetcsv($handle, 0, ',');
            if (!$header) {
                return redirect()->route('assets.index')->with('error', 'File CSV kosong.');
            }

            // Try semicolon delimiter if only 1 column detected
            if (count($header) <= 1) {
                rewind($handle);
                $bom = fread($handle, 3);
                if ($bom !== "\xEF\xBB\xBF") {
                    rewind($handle);
                }
                $header = fgetcsv($handle, 0, ';');
            }

            // Determine delimiter
            $delimiter = count($header) > 1 ? ',' : ';';
            if (str_contains(implode('', $header), ';')) {
                $delimiter = ';';
                rewind($handle);
                $bom = fread($handle, 3);
                if ($bom !== "\xEF\xBB\xBF") {
                    rewind($handle);
                }
                $header = fgetcsv($handle, 0, ';');
            }

            // Normalize header names (lowercase, remove spaces, underscores, BOM chars)
            $header = array_map(function($h) {
                $h = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h); // Remove non-printable chars
                return strtolower(trim(str_replace([' ', '_'], '', $h)));
            }, $header);

            $successCount = 0;
            $errorCount = 0;
            $skippedEmpty = 0;
            $errors = [];

            while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
                // Skip truly empty rows (all cells empty or row has < 2 cells)
                $nonEmpty = array_filter($data, function($v) { return trim($v) !== ''; });
                if (empty($nonEmpty) || count($data) < 2) {
                    $skippedEmpty++;
                    continue;
                }

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

                if (!trim($kode_brg) || !trim($nama_brg)) {
                    $errorCount++;
                    $errors[] = "Baris dengan kode/nama barang kosong dilewati.";
                    continue;
                }

                // Clean and normalize input
                $asal_usul = trim($asal_usul);
                $asal_usul = in_array($asal_usul, ['Pembelian', 'Hibah', 'Dropping Dinas', 'Dana BOS']) ? $asal_usul : 'Pembelian';

                // Parse harga_perolehan: handle Indonesian format (dots as thousands separator)
                // e.g., "90.000" → 90000, "1.500.000" → 1500000, "Rp 90.000" → 90000
                $harga_perolehan = $this->cleanNumericValue($harga_perolehan);

                $thn_perolehan = is_numeric(trim($thn_perolehan)) && strlen(trim($thn_perolehan)) == 4 ? trim($thn_perolehan) : null;

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
            if ($skippedEmpty > 0) {
                $msg .= " {$skippedEmpty} baris kosong dilewati.";
            }
            if ($errorCount > 0) {
                $msg .= " Gagal {$errorCount} baris. Detail: " . implode(', ', array_slice($errors, 0, 3));
            }

            return redirect()->route('assets.index')->with('success', $msg);
        }

        return redirect()->route('assets.index')->with('error', 'Gagal membaca file CSV.');
    }

    /**
     * Clean and parse a numeric value from CSV, handling Indonesian format.
     * Converts "90.000" → 90000, "1.500.000" → 1500000, "Rp 90.000" → 90000
     */
    private function cleanNumericValue($value)
    {
        if ($value === null || trim($value) === '' || trim($value) === '-') {
            return null;
        }

        $value = trim($value);

        // Remove currency prefix like "Rp", "Rp.", "IDR"
        $value = preg_replace('/^(Rp\.?\s*|IDR\s*)/i', '', $value);
        $value = trim($value);

        // Detect Indonesian format: dots as thousands separators
        // Pattern: digits separated by dots in groups of 3 (e.g., "1.500.000", "90.000")
        // But NOT a single decimal like "90.50"
        if (preg_match('/^\d{1,3}(\.\d{3})+$/', $value)) {
            // Indonesian format: remove dots (thousands separators)
            $value = str_replace('.', '', $value);
        } elseif (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $value)) {
            // Indonesian format with decimal comma: "1.500.000,50"
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (preg_match('/^\d{1,3}(,\d{3})+(\.\d+)?$/', $value)) {
            // English format with commas: "1,500,000.50"
            $value = str_replace(',', '', $value);
        }

        // Remove any remaining non-numeric chars except dot and minus
        $value = preg_replace('/[^0-9.\-]/', '', $value);

        return is_numeric($value) ? (float) $value : null;
    }
}

