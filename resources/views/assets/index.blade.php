@extends('layouts.app')
@section('title','Data Aset')
@section('content')

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">Data Aset (Master Aset)</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('assets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Aset
            </a>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search & Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('assets.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Cari Aset</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Kode / Nama / Merk..." value="{{ old('search', $search ?? '') }}">
                    <small class="form-text text-muted">Cari berdasarkan kode barang, nama, atau merk</small>
                </div>
                <div class="col-md-4">
                    <label for="asal_usul" class="form-label">Filter Asal Usul</label>
                    <select class="form-select" id="asal_usul" name="asal_usul">
                        <option value="">-- Semua --</option>
                        <option value="Pembelian" @selected(($filter_asal ?? '') === 'Pembelian')>Pembelian</option>
                        <option value="Hibah" @selected(($filter_asal ?? '') === 'Hibah')>Hibah</option>
                        <option value="Dropping Dinas" @selected(($filter_asal ?? '') === 'Dropping Dinas')>Dropping Dinas</option>
                        <option value="Dana BOS" @selected(($filter_asal ?? '') === 'Dana BOS')>Dana BOS</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-info flex-grow-1">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('assets.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="10%">Kode Barang</th>
                        <th width="15%">Nama Barang</th>
                        <th width="12%">Merk/Tipe</th>
                        <th width="12%">Lokasi</th>
                        <th width="10%">Tahun</th>
                        <th width="12%">Harga</th>
                        <th width="12%">Asal Usul</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($items->count())
                        @foreach($items as $item)
                            <tr>
                                <td>{{ ($items->currentPage()-1) * $items->perPage() + $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $item->kode_brg }}</span>
                                </td>
                                <td>
                                    <strong>{{ $item->nama_brg }}</strong>
                                    @if($item->spesifikasi)
                                        <br><small class="text-muted">{{ Str::limit($item->spesifikasi, 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $item->merk_tipe ?? '-' }}</td>
                                <td>{{ $item->lokasi ?? '-' }}</td>
                                <td>{{ $item->thn_perolehan ?? '-' }}</td>
                                <td>
                                    @if($item->harga_perolehan)
                                        <span class="badge bg-success">Rp {{ number_format($item->harga_perolehan, 0, ',', '.') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($item->asal_usul) {
                                            'Pembelian' => 'bg-info',
                                            'Hibah' => 'bg-warning',
                                            'Dropping Dinas' => 'bg-secondary',
                                            'Dana BOS' => 'bg-success',
                                            default => 'bg-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $item->asal_usul }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('kondisi.history', $item->id_aset) }}" 
                                           class="btn btn-sm btn-info text-white" title="Riwayat Kondisi Fisik">
                                            <i class="fas fa-stethoscope"></i>
                                        </a>
                                        <a href="{{ route('pemeliharaan.history', $item->id_aset) }}" 
                                           class="btn btn-sm btn-success text-white" title="Riwayat Pemeliharaan">
                                            <i class="fas fa-screwdriver-wrench"></i>
                                        </a>
                                        <a href="{{ route('efisiensi.history', $item->id_aset) }}" 
                                           class="btn btn-sm btn-primary text-white" title="Riwayat Efisiensi Output">
                                            <i class="fas fa-gauge-high"></i>
                                        </a>
                                        <a href="{{ route('assets.edit', $item->id_aset) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                data-id="{{ $item->id_aset }}" data-name="{{ $item->nama_brg }}"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-inbox" style="font-size: 2rem; color: #ccc;"></i>
                                <p class="text-muted mt-2">Tidak ada data aset ditemukan</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($items->count())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Menampilkan {{ $items->firstItem() }} - {{ $items->lastItem() }} dari {{ $items->total() }} data
                    </small>
                    {{ $items->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Konfirmasi Penghapusan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus aset <strong id="itemName"></strong>?
                <br><small class="text-muted mt-2 d-block">Tindakan ini tidak dapat dibatalkan.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
    const button = e.relatedTarget;
    const itemId = button.getAttribute('data-id');
    const itemName = button.getAttribute('data-name');
    
    document.getElementById('itemName').textContent = itemName;
    document.getElementById('deleteForm').action = `/assets/${itemId}`;
});
</script>

@endsection
