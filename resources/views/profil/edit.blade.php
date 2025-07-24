@extends('layouts.template')
@section('title', 'Profil Saya')

@section('content')
<style>
    .card-profile {
        background: linear-gradient(135deg, #0a2647, #144272);
        color: white;
        border-radius: 10px;
    }

    .camera-btn {
        background-color: #007bff;
        color: white;
        position: absolute;
        top: -8px;
        right: -8px;
        border-radius: 50%;
        padding: 7px 9px;
        border: 2px solid white;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .camera-btn:hover {
        background-color: #0056b3;
    }

    .form-section {
        background: #ffffff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .form-control {
        border: 1px solid #ced4da;
        border-radius: 6px;
        box-shadow: none;
        transition: border-color 0.3s, box-shadow 0.3s;
        background-color: #fff;
        color: #000;
    }

    .form-control:focus {
        border-color: #0a2647;
        box-shadow: 0 0 0 0.15rem rgba(10, 38, 71, 0.25);
    }

    .form-label {
        font-weight: 600;
        color: #000 !important;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #000;
        border-bottom: 2px solid #e0e0e0;
        margin-top: 20px;
        padding-bottom: 5px;
    }
</style>


<div class="container-fluid">
    <div class="card shadow-sm mb-4 card-profile">
        <div class="card-body">
            <div class="row">

                <!-- FOTO -->
                <div class="col-md-4 text-center position-relative">
                    <div class="p-3">
                        <div class="position-relative d-inline-block">
                            @if ($user->foto)
                                <img src="{{ asset('storage/' . $user->foto) }}" class="rounded-circle shadow-sm" width="150" height="150" style="object-fit: cover;">
                            @else
                                <img src="{{ asset('adminlte/dist/img/avatar.png') }}" class="rounded-circle shadow-sm" width="150" height="150">
                            @endif
                            <form id="upload-foto-form" action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="position-absolute">
                                @csrf
                                <label for="foto-upload" class="camera-btn" title="Ganti Foto">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" name="foto" id="foto-upload" class="d-none" accept="image/*">
                            </form>
                        </div>
                        <h5 class="mt-3">{{ $user->nama }}</h5>
                        <span class="badge badge-light text-dark">{{ $user->nip }}</span>
                        <div class="mt-2">
                            <p class="mb-1"><i class="fas fa-user-tag mr-2"></i> {{ $user->level->level_name ?? 'Pegawai' }}</p>
                            <p><i class="fas fa-venus-mars mr-2"></i> {{ $user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                    </div>
                </div>

                <!-- FORM -->
                <div class="col-md-8">
                    <div class="form-section">
                        <h5 class="mb-3 section-title"><i class="fas fa-user-cog mr-2 text-dark"></i> Informasi Akun</h5>
                        <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">NIP</label>
                                    <input type="text" class="form-control" value="{{ $user->nip }}" readonly>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-control" required>
                                        <option value="L" {{ $user->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ $user->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Agama</label>
                                    <input type="text" name="agama" class="form-control" value="{{ old('agama', $user->agama) }}">
                                </div>
                            </div>

                           <h6 class="mt-4 section-title">Kontak</h6>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="text" name="no_tlp" class="form-control" value="{{ old('no_tlp', $user->no_tlp) }}">
                                </div>
                            </div>

                 <h6 class="mt-4 section-title">Keamanan</h6>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Password Baru</label>
                                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin ganti">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control">
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary px-4 mt-3">
                                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('#foto-upload').on('change', function () {
        const formData = new FormData($('#upload-foto-form')[0]);

        $.ajax({
            url: $('#upload-foto-form').attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Foto berhasil diperbarui',
                    timer: 1500,
                    showConfirmButton: false
                });
                setTimeout(() => location.reload(), 1000);
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal upload foto',
                    text: 'Silakan coba lagi'
                });
            }
        });
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}'
        });
    @endif
</script>
@endpush
