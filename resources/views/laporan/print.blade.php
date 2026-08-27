<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Resmi Inventaris Lab TKJ</title>
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
            <i class="fa-solid fa-file-pdf"></i> Pratinjau Cetak Laporan Resmi
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
        @php
            $title = match($tipe) {
                'kondisi' => 'Laporan Hasil Observasi Kondisi Fisik & Teknis Aset',
                'pemeliharaan' => 'Laporan Hasil Tindakan Pemeliharaan & Servis Aset',
                'efisiensi' => 'Laporan Hasil Pengukuran Efisiensi Penggunaan Aset',
                'variabel' => 'Laporan Pengaruh Variabel Eksternal & Lingkungan Aset',
            };
        @endphp
        <h3>{{ $title }}</h3>
        @if($startDate || $endDate)
            <p>Periode: 
                {{ $startDate ? \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM Y') : 'Awal' }}
                s.d
                {{ $endDate ? \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM Y') : 'Sekarang' }}
            </p>
        @else
            <p>Periode: Semua Data Riwayat</p>
        @endif
    </div>

    {{-- Tabel Data Laporan --}}
    <table>
        <thead>
            @if($tipe === 'kondisi')
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">Kode Aset</th>
                    <th>Nama Aset</th>
                    <th style="width: 12%">Tanggal Observasi</th>
                    <th style="width: 10%">Kondisi</th>
                    <th style="width: 15%">Keterangan Teknis</th>
                    <th style="width: 10%">Usia Pakai</th>
                    <th style="width: 10%">Frequensi Kerusakan</th>
                    <th style="width: 13%">Kelas Label</th>
                </tr>
            @elseif($tipe === 'pemeliharaan')
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">Kode Aset</th>
                    <th>Nama Aset</th>
                    <th style="width: 12%">Tanggal Pemeliharaan</th>
                    <th style="width: 12%">Jenis Pemeliharaan</th>
                    <th style="width: 10%">Interval</th>
                    <th style="width: 15%">Pelaksana</th>
                    <th style="width: 12%">Biaya Servis</th>
                    <th style="width: 10%">Kondisi Akhir</th>
                    <th style="width: 15%">Keterangan</th>
                </tr>
            @elseif($tipe === 'efisiensi')
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">Kode Aset</th>
                    <th>Nama Aset</th>
                    <th style="width: 12%">Tanggal Observasi</th>
                    <th style="width: 10%">Jam Operasional</th>
                    <th style="width: 12%">Tingkat Pakai</th>
                    <th style="width: 8%">User</th>
                    <th style="width: 10%">Downtime</th>
                    <th style="width: 10%">Performa</th>
                    <th style="width: 10%">Ekonomis</th>
                    <th style="width: 10%">Efisiensi Output</th>
                </tr>
            @elseif($tipe === 'variabel')
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 15%">Kode Aset</th>
                    <th>Nama Aset</th>
                    <th style="width: 12%">Tanggal Observasi</th>
                    <th style="width: 13%">Lingkungan</th>
                    <th style="width: 13%">Daya Listrik</th>
                    <th style="width: 13%">Sparepart</th>
                    <th style="width: 13%">Anggaran</th>
                    <th style="width: 13%">Efek Eksternal</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                @if($tipe === 'kondisi')
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><strong>{{ $row->asset->kode_brg ?? '-' }}</strong></td>
                        <td>{{ $row->asset->nama_brg ?? '-' }}</td>
                        <td class="text-center">{{ $row->tgl_observasi ? $row->tgl_observasi->locale('id')->isoFormat('DD-MM-YYYY') : '-' }}</td>
                        <td class="text-center">{{ $row->kondisi_brg }}</td>
                        <td>{{ $row->ket_teknis }}</td>
                        <td class="text-center">{{ $row->usia_pakai }} tahun</td>
                        <td class="text-center">{{ $row->frq_kerusakan }} kali</td>
                        <td class="text-center" style="font-weight: bold;">{{ $row->kelas_label }}</td>
                    </tr>
                @elseif($tipe === 'pemeliharaan')
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><strong>{{ $row->asset->kode_brg ?? '-' }}</strong></td>
                        <td>{{ $row->asset->nama_brg ?? '-' }}</td>
                        <td class="text-center">{{ $row->tgl_pm ? $row->tgl_pm->locale('id')->isoFormat('DD-MM-YYYY') : '-' }}</td>
                        <td class="text-center">{{ $row->jenis_pm }}</td>
                        <td class="text-center">{{ $row->interval_bulan }} bulan</td>
                        <td>{{ $row->pelaksana }}</td>
                        <td>Rp {{ number_format($row->biaya_servis, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $row->kon_after }}</td>
                        <td>{{ $row->ket_pm ?? '-' }}</td>
                    </tr>
                @elseif($tipe === 'efisiensi')
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><strong>{{ $row->asset->kode_brg ?? '-' }}</strong></td>
                        <td>{{ $row->asset->nama_brg ?? '-' }}</td>
                        <td class="text-center">{{ $row->tgl_observasi ? $row->tgl_observasi->locale('id')->isoFormat('DD-MM-YYYY') : '-' }}</td>
                        <td class="text-center">{{ $row->jam_ops }} jam</td>
                        <td class="text-center">{{ $row->penggunaan }}</td>
                        <td class="text-center">{{ $row->jml_user }} orang</td>
                        <td class="text-center">{{ $row->downtime }} jam</td>
                        <td class="text-center">{{ $row->perform }}</td>
                        <td class="text-center">{{ $row->umur_ekonomis }} tahun</td>
                        <td class="text-center" style="font-weight: bold;">{{ $row->efi_out }}</td>
                    </tr>
                @elseif($tipe === 'variabel')
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><strong>{{ $row->asset->kode_brg ?? '-' }}</strong></td>
                        <td>{{ $row->asset->nama_brg ?? '-' }}</td>
                        <td class="text-center">{{ $row->tgl_observasi ? $row->tgl_observasi->locale('id')->isoFormat('DD-MM-YYYY') : '-' }}</td>
                        <td>{{ $row->lingkungan }}</td>
                        <td>{{ $row->daya_listrik }}</td>
                        <td>{{ $row->sparepart }}</td>
                        <td>{{ $row->anggaran }}</td>
                        <td class="text-center" style="font-weight: bold;">{{ $row->ext_effect }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="15" class="text-center" style="padding: 20px; font-style: italic; color: #666;">
                        Tidak ada data historis yang ditemukan dalam periode observasi ini.
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
