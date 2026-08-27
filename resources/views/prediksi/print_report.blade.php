<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Prediksi Kelayakan Aset Resmi</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }

        /* Kop Surat */
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            text-align: center;
        }
        .kop-logo {
            width: 85px;
            height: 85px;
            object-fit: contain;
        }
        .kop-text h2 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .kop-text h1 {
            font-size: 16pt;
            font-weight: bold;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .kop-text p {
            font-size: 10pt;
            margin: 0;
            font-style: italic;
        }

        /* Judul Dokumen */
        .judul-laporan {
            text-align: center;
            margin-bottom: 25px;
        }
        .judul-laporan h3 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .judul-laporan p {
            font-size: 11pt;
            margin: 0;
        }

        /* Tabel Data */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px 10px;
            font-size: 10pt;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }

        /* Tanda Tangan */
        .signature-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 250px;
        }
        .signature-space {
            height: 75px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Tombol Aksi */
        .no-print-bar {
            background-color: #f1f5f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e2e8f0;
        }
        .btn {
            background-color: #3b82f6;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            font-family: sans-serif;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-secondary {
            background-color: #64748b;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    {{-- Control Panel Bar (Hidden when Printing) --}}
    <div class="no-print-bar">
        <span style="font-family: sans-serif; font-size: 14px; font-weight: 600; color: #334155;">
            <i class="fa-solid fa-file-pdf"></i> Pratinjau Cetak Laporan Kelayakan Aset Resmi
        </span>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn">
                Cetak Laporan / Simpan PDF
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                Tutup Halaman
            </button>
        </div>
    </div>

    {{-- Kop Surat Sekolah --}}
    <div class="kop-surat">
        <img src="{{ asset('img/logo-smk.svg') }}" alt="Logo SMK Muhammadiyah 12" class="kop-logo">
        <div class="kop-text">
            <h1>SMK Muhammadiyah 12 Jakarta</h1>
            <h2>Program Keahlian: Teknik Komputer dan Jaringan (TKJ)</h2>
            <p>Alamat: Jl. H. Murtadho No. 2A, Tugu Utara, Koja, Jakarta Utara - Telpon 085284270188</p>
        </div>
    </div>

    {{-- Judul Laporan --}}
    <div class="judul-laporan">
        <h3>Laporan Hasil Prediksi Kelayakan Aset Laboratorium</h3>
        <p>Klasifikasi Metode Naive Bayes & Rekomendasi Tindakan</p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</p>
    </div>

    {{-- Tabel Data Laporan --}}
    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Kode Aset</th>
                <th>Nama Aset</th>
                <th style="width: 12%">Lokasi</th>
                <th style="width: 15%">Tanggal Prediksi</th>
                <th style="width: 12%">Status Kelayakan</th>
                <th style="width: 10%">Probabilitas</th>
                <th style="width: 25%">Rekomendasi Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                @php
                    $topProb = max($row->prob_layak, $row->prob_servis, $row->prob_tidak_layak) * 100;
                    
                    $recom = match ($row->hasil_prediksi) {
                        'Layak' => 'Teruskan Penggunaan (Continue Use)',
                        'Perlu Servis' => 'Jadwalkan Pemeliharaan (Schedule Maintenance)',
                        'Tidak Layak' => 'Ganti / Hapus Aset (Replace/Dispose)',
                        default => '-'
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $row->asset->kode_brg ?? '-' }}</strong></td>
                    <td>{{ $row->asset->nama_brg ?? '-' }}</td>
                    <td class="text-center">{{ $row->asset->lokasi ?? '-' }}</td>
                    <td class="text-center">{{ $row->tgl_prediksi ? $row->tgl_prediksi->format('d-m-Y H:i') : '-' }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ $row->hasil_prediksi }}</td>
                    <td class="text-center font-semibold">{{ number_format($topProb, 2) }}%</td>
                    <td style="font-style: italic;">{{ $recom }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; font-style: italic; color: #666;">
                        Tidak ada data prediksi kelayakan yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tanda Tangan Laporan Resmi --}}
    <div class="signature-container">
        <div class="signature-box">
            <p>Jakarta, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</p>
            <p>Mengetahui,</p>
            <p style="margin-top: -10px;">Kepala Laboratorium TKJ</p>
            <div class="signature-space"></div>
            <p class="signature-name">Alvian Fiqra Ramadhan, S.Kom.</p>
            <p style="margin-top: -10px; font-size: 10pt; color: #444;">NIP. 19880512 201503 1 002</p>
        </div>
    </div>

</body>
</html>
