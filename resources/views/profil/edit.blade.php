@extends('layouts.template')
@section('title', 'Edit Profil')

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

    .form-label {
        font-weight: 600;
        color: #000 !important;
    }

    .btn-back {
        background-color: #6c757d;
        color: white;
    }

    .btn-back:hover {
        background-color: #5a6268;
    }
</style>

<div class="container-fluid">
    <div class="card shadow-sm mb-4 card-profile">
        <div class="card-body">
            <div class="row">
                <!-- FOTO PROFIL -->
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

                <!-- FORM PROFIL -->
                <div class="col-md-8">
                    <div class="form-section">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs mb-4" id="profilTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="data-tab" data-toggle="tab" href="#data" role="tab"><i class="fas fa-user-edit mr-1"></i> Data Diri</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="keamanan-tab" data-toggle="tab" href="#keamanan" role="tab"><i class="fas fa-lock mr-1"></i> Keamanan</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="profilTabContent">
                            <!-- Tab Data Diri -->
                            <div class="tab-pane fade show active" id="data" role="tabpanel">
                                <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">NIP</label>
                                        <input type="text" class="form-control" value="{{ $user->nip }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" class="form-control" required>
                                            <option value="L" {{ $user->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ $user->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Agama</label>
                                        <input type="text" name="agama" class="form-control" value="{{ old('agama', $user->agama) }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">No. Telepon</label>
                                        <input type="text" name="no_tlp" class="form-control" value="{{ old('no_tlp', $user->no_tlp) }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Jabatan</label>
                                        <input type="text" class="form-control" value="{{ $user->jabatan->refJabatan->nama_jabatan ?? '-' }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Pangkat</label>
                                        <input type="text" class="form-control" value="{{ $user->pangkat->refPangkat->golongan_pangkat ?? '-' }}" readonly>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary mt-2"><i class="fas fa-save mr-1"></i> Simpan Data</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Tab Keamanan -->
                            <div class="tab-pane fade" id="keamanan" role="tabpanel">
                                <form id="form-password">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">Password Lama</label>
                                        <input type="password" name="old_password" class="form-control" placeholder="Masukkan password lama" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Password Baru</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Konfirmasi Password Baru</label>
                                        <input type="password" name="password_confirmation" class="form-control" required>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary mt-2"><i class="fas fa-key mr-1"></i> Simpan Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('profil.show') }}" class="btn btn-back"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                        </div>
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
    // Upload foto dengan AJAX
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

    // AJAX submit untuk ubah password
    $('#form-password').on('submit', function(e) {
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: "{{ route('profil.updatePassword') }}",
            type: 'POST',
            data: formData,
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                $('#form-password')[0].reset();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan',
                        text: 'Silakan coba lagi.'
                    });
                }
            }
        });
    });
</script>
@endpush
