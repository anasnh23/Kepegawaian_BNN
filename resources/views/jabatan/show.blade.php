@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>Detail Jabatan</h1>
        <p><strong>Nama:</strong> {{ $jabatan->nama_jabatan }}</p>
        <p><strong>Deskripsi:</strong> {{ $jabatan->deskripsi }}</p>
        <a href="{{ route('jabatan.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
@endsection