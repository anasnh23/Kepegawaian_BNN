@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Jabatan</h1>
        <form action="{{ route('jabatan.update', $jabatan) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nama Jabatan</label>
                <input type="text" name="nama_jabatan" class="form-control" value="{{ $jabatan->nama_jabatan }}" required>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control">{{ $jabatan->deskripsi }}</textarea>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
@endsection
