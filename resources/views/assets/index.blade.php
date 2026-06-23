@extends('layouts.app')
@section('title','Data Aset')
@section('content')
  <h2>Data Aset</h2>
  <table class="table table-striped">
    <thead><tr><th>#</th><th>Nama</th><th>Kategori</th><th>Kondisi</th></tr></thead>
    <tbody>
    @foreach($items ?? [] as $i)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $i->name }}</td>
        <td>{{ $i->category }}</td>
        <td>{{ $i->condition }}</td>
      </tr>
    @endforeach
    </tbody>
  </table>
@endsection
