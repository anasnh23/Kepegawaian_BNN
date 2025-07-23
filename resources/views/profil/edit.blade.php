@extends('layouts.template')
@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid">

    <!-- Header Profil -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body bg-light d-flex align-items-center">
            <div class="mr-4">
                @if ($user->foto)
                    <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto Profil" class="rounded-circle shadow" width="120" height="120" style="object-fit: cover;">
                @else
                    <img src="{{ asset('adminlte/dist/img/avatar.png') }}" alt="Foto Default" class="rounded-circle shadow" width="120" height="120">
                @endif
            </div>
            <div>
                <h4 class="mb-1">{{ $user->nama }}</h4>
                <p class="text-muted mb-0">{{ $user->nip }}</p>
                <small class="text-muted">Profil    pribadi - pastikan datamu akurat</small>
            </div>
        </div>
    </div>

    <!-- Form Edit Profil -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-user-edit mr-2"></i> Edit Profil Saya</h5>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Kolom kiri -->
                    <div class="col-md-6">
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
                    </div>

                    <!-- Kolom kanan -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>No. Telepon</label>
                            <input type="text" name="no_tlp" class="form-control" value="{{ old('no_tlp', $user->no_tlp) }}">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin ganti">
                        </div>
                        <div class="form-group">
                            <label>Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Upload Foto -->
                <div class="form-group">
                    <label>Foto Profil</label>
                    <input type="file" name="foto" class="form-control-file mt-1">
                </div>

                <div class="text-right mt-4">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
