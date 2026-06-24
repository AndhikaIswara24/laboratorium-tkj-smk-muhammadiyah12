@extends('layouts.app')
@section('title','Edit Aset')
@section('content')

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-edit"></i> Edit Data Aset
                    </h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-circle"></i> Terdapat Kesalahan!
                            </h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('assets.update', $asset->id_aset) }}" method="POST" enctype="multipart/form-data" id="formAset">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kode_brg" class="form-label">
                                        <i class="fas fa-barcode"></i> Kode Barang <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('kode_brg') is-invalid @enderror" 
                                           id="kode_brg" name="kode_brg" 
                                           placeholder="Misal: AST-001" maxlength="20"
                                           value="{{ old('kode_brg', $asset->kode_brg) }}" required>
                                    <small class="form-text text-muted">Kode unik untuk identifikasi barang</small>
                                    @error('kode_brg')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_brg" class="form-label">
                                        <i class="fas fa-cube"></i> Nama Barang <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('nama_brg') is-invalid @enderror" 
                                           id="nama_brg" name="nama_brg" 
                                           placeholder="Misal: Printer HP LaserJet" maxlength="100"
                                           value="{{ old('nama_brg', $asset->nama_brg) }}" required>
                                    @error('nama_brg')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="merk_tipe" class="form-label">
                                        <i class="fas fa-tag"></i> Merk / Tipe
                                    </label>
                                    <input type="text" class="form-control @error('merk_tipe') is-invalid @enderror" 
                                           id="merk_tipe" name="merk_tipe" 
                                           placeholder="Misal: HP P3015" maxlength="80"
                                           value="{{ old('merk_tipe', $asset->merk_tipe) }}">
                                    @error('merk_tipe')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lokasi" class="form-label">
                                        <i class="fas fa-map-marker-alt"></i> Lokasi
                                    </label>
                                    <input type="text" class="form-control @error('lokasi') is-invalid @enderror" 
                                           id="lokasi" name="lokasi" 
                                           placeholder="Misal: Lab TKJ" maxlength="60"
                                           value="{{ old('lokasi', $asset->lokasi) }}">
                                    @error('lokasi')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="spesifikasi" class="form-label">
                                <i class="fas fa-info-circle"></i> Spesifikasi
                            </label>
                            <textarea class="form-control @error('spesifikasi') is-invalid @enderror" 
                                      id="spesifikasi" name="spesifikasi" rows="3"
                                      placeholder="Deskripsi detail aset, kondisi, fitur khusus, dll...">{{ old('spesifikasi', $asset->spesifikasi) }}</textarea>
                            <small class="form-text text-muted">Detil teknis dan deskripsi aset</small>
                            @error('spesifikasi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="thn_perolehan" class="form-label">
                                        <i class="fas fa-calendar"></i> Tahun Perolehan
                                    </label>
                                    <input type="number" class="form-control @error('thn_perolehan') is-invalid @enderror" 
                                           id="thn_perolehan" name="thn_perolehan" 
                                           placeholder="2024" min="1900" max="2099"
                                           value="{{ old('thn_perolehan', $asset->thn_perolehan) }}">
                                    @error('thn_perolehan')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="harga_perolehan" class="form-label">
                                        <i class="fas fa-money-bill"></i> Harga Perolehan
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('harga_perolehan') is-invalid @enderror" 
                                               id="harga_perolehan" name="harga_perolehan" 
                                               placeholder="0" min="0" step="0.01"
                                               value="{{ old('harga_perolehan', $asset->harga_perolehan) }}">
                                    </div>
                                    @error('harga_perolehan')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="asal_usul" class="form-label">
                                        <i class="fas fa-arrow-down"></i> Asal Usul <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('asal_usul') is-invalid @enderror" 
                                            id="asal_usul" name="asal_usul" required>
                                        <option value="">-- Pilih Asal Usul --</option>
                                        @foreach($asalUsul as $option)
                                            <option value="{{ $option }}" @selected(old('asal_usul', $asset->asal_usul) === $option)>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('asal_usul')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                Dibuat: {{ $asset->created_at->format('d M Y H:i') }}
                            </small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('assets.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="reset" class="btn btn-warning">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Perbarui Aset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
