@extends('layouts.app')

@section('title','Dashboard - Inventaris Lab TKJ')

@section('content')
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">Total Aset</h5>
          <p class="card-text display-6">123</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">Kondisi Baik</h5>
          <p class="card-text display-6">95</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">Perlu Pemeliharaan</h5>
          <p class="card-text display-6">8</p>
        </div>
      </div>
    </div>
  </div>

  <p class="text-muted">Tampilan dashboard dasar. Ganti angka nyata dengan query model Anda.</p>
@endsection
