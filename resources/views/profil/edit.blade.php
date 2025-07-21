@extends('layouts.template')
@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Edit Profil Saya</h5>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>NIP</label>
                    <input type="text" name="nip" class="form-control" value="{{ old('nip', $user->nip) }}" readonly>
                </div>

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                </div>

                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control" required>
                        <option value="L" {{ $user->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $user->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Agama</label>
                    <input type="text" name="agama" class="form-control" value="{{ old('agama', $user->agama) }}">
                </div>

                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="text" name="no_tlp" class="form-control" value="{{ old('no_tlp', $user->no_tlp) }}">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="form-group">
                    <label>Password Baru (kosongkan jika tidak ingin ganti)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <div class="form-group">
                    <label>Foto Profil</label><br>
                    @if ($user->foto)
                        <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto" class="rounded mb-2" width="100">
                    @endif
                    <input type="file" name="foto" class="form-control-file">
                </div>

                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection
