<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\KondisiFisik;
use App\Models\Pemeliharaan;
use App\Models\Efisiensi;
use App\Models\VariabelEksternal;
use App\Traits\CanExportData;

class LaporanController extends Controller
{
    use CanExportData;

    public function index()
    {
        return view('laporan.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:kondisi,pemeliharaan,efisiensi,variabel',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'action' => 'required|in:print,excel,csv',
        ]);

        $tipe = $request->input('tipe');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $action = $request->input('action');

        // Fetch data based on type
        switch ($tipe) {
            case 'kondisi':
                $query = KondisiFisik::with('asset');
                $dateColumn = 'tgl_observasi';
                break;
            case 'pemeliharaan':
                $query = Pemeliharaan::with('asset');
                $dateColumn = 'tgl_pm';
                break;
            case 'efisiensi':
                $query = Efisiensi::with('asset');
                $dateColumn = 'tgl_observasi';
                break;
            case 'variabel':
                $query = VariabelEksternal::with('asset');
                $dateColumn = 'tgl_observasi';
                break;
        }

        if ($startDate) {
            $query->where($dateColumn, '>=', $startDate);
        }
        if ($endDate) {
            $query->where($dateColumn, '<=', $endDate);
        }

        $data = $query->orderBy($dateColumn, 'desc')->get();

        if ($action === 'print') {
            return view('laporan.print', compact('data', 'tipe', 'startDate', 'endDate'));
        }

        // Export Excel/CSV
        $filename = 'laporan-' . $tipe . '-' . date('Y-m-d');
        
        switch ($tipe) {
            case 'kondisi':
                $title = 'Laporan Kondisi Fisik & Teknis Aset';
                $headers = ['No', 'Kode Aset', 'Nama Aset', 'Tanggal Observasi', 'Kondisi Barang', 'Keterangan Teknis', 'Usia Pakai', 'Frekuensi Kerusakan', 'Kelas Label'];
                $rows = [];
                foreach ($data as $index => $row) {
                    $rows[] = [
                        $index + 1,
                        $row->asset->kode_brg ?? '-',
                        $row->asset->nama_brg ?? '-',
                        $row->tgl_observasi ? $row->tgl_observasi->format('d-m-Y') : '-',
                        $row->kondisi_brg,
                        $row->ket_teknis,
                        $row->usia_pakai . ' tahun',
                        $row->frq_kerusakan,
                        $row->kelas_label,
                    ];
                }
                break;
            case 'pemeliharaan':
                $title = 'Laporan Pemeliharaan & Servis Aset';
                $headers = ['No', 'Kode Aset', 'Nama Aset', 'Tanggal PM', 'Jenis PM', 'Interval (Bulan)', 'Pelaksana', 'Biaya Servis', 'Kondisi Sesudah', 'Keterangan'];
                $rows = [];
                foreach ($data as $index => $row) {
                    $rows[] = [
                        $index + 1,
                        $row->asset->kode_brg ?? '-',
                        $row->asset->nama_brg ?? '-',
                        $row->tgl_pm ? $row->tgl_pm->format('d-m-Y') : '-',
                        $row->jenis_pm,
                        $row->interval_bulan,
                        $row->pelaksana,
                        'Rp ' . number_format($row->biaya_servis, 0, ',', '.'),
                        $row->kon_after,
                        $row->ket_pm ?? '-',
                    ];
                }
                break;
            case 'efisiensi':
                $title = 'Laporan Efisiensi Penggunaan Aset';
                $headers = ['No', 'Kode Aset', 'Nama Aset', 'Tanggal Observasi', 'Jam Ops', 'Penggunaan', 'Jml User', 'Downtime', 'Performa', 'Umur Ekonomis', 'Efisiensi Output'];
                $rows = [];
                foreach ($data as $index => $row) {
                    $rows[] = [
                        $index + 1,
                        $row->asset->kode_brg ?? '-',
                        $row->asset->nama_brg ?? '-',
                        $row->tgl_observasi ? $row->tgl_observasi->format('d-m-Y') : '-',
                        $row->jam_ops . ' jam',
                        $row->penggunaan,
                        $row->jml_user . ' orang',
                        $row->downtime . ' jam',
                        $row->perform,
                        $row->umur_ekonomis . ' tahun',
                        $row->efi_out,
                    ];
                }
                break;
            case 'variabel':
                $title = 'Laporan Variabel Eksternal & Lingkungan Aset';
                $headers = ['No', 'Kode Aset', 'Nama Aset', 'Tanggal Observasi', 'Lingkungan', 'Daya Listrik', 'Sparepart', 'Anggaran', 'Efek Eksternal'];
                $rows = [];
                foreach ($data as $index => $row) {
                    $rows[] = [
                        $index + 1,
                        $row->asset->kode_brg ?? '-',
                        $row->asset->nama_brg ?? '-',
                        $row->tgl_observasi ? $row->tgl_observasi->format('d-m-Y') : '-',
                        $row->lingkungan,
                        $row->daya_listrik,
                        $row->sparepart,
                        $row->anggaran,
                        $row->ext_effect,
                    ];
                }
                break;
        }

        if ($action === 'excel') {
            return $this->exportToExcel($filename, $title, $headers, $rows);
        } else {
            return $this->exportToCsv($filename, $headers, $rows);
        }
    }
}
