@extends('layouts.app') <!-- Asumsikan ada layout utama -->

@section('content')
    <div class="container">
        <h1>Daftar Jabatan</h1>
        <a href="{{ route('jabatan.create') }}" class="btn btn-primary">Tambah Jabatan</a>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Jabatan</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jabatans as $jabatan)
                    <tr>
                        <td>{{ $jabatan->id }}</td>
                        <td>{{ $jabatan->nama_jabatan }}</td>
                        <td>{{ $jabatan->deskripsi }}</td>
                        <td>
                            <a href="{{ route('jabatan.show', $jabatan) }}" class="btn btn-info">Lihat</a>
                            <a href="{{ route('jabatan.edit', $jabatan) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('jabatan.destroy', $jabatan) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
