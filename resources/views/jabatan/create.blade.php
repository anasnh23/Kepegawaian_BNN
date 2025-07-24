@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Tambah Jabatan</h1>
        <form action="{{ route('jabatan.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nama Jabatan</label>
                <input type="text" name="nama_jabatan" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
@endsection
