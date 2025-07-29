@extends('layouts.template')
@section('title', 'Profil Saya')

@section('content')
<style>
    .profile-container {
        max-width: 1050px;
        margin: 0 auto;
        background: #f8f9fa;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        padding: 30px;
        font-family: 'Segoe UI', sans-serif;
    }

    .profile-header {
        display: flex;
        align-items: center;
        border-bottom: 2px solid #144272;
        padding-bottom: 20px;
        margin-bottom: 25px;
    }

    .profile-photo {
        border-radius: 50%;
        border: 4px solid #144272;
        width: 150px;
        height: 150px;
        object-fit: cover;
    }

    .profile-name {
        font-size: 1.5rem;
        font-weight: bold;
        color: #0a2647;
        margin-top: 15px;
    }

    .badge-nip {
        background: #144272;
        color: white;
        font-weight: 500;
        border-radius: 5px;
        padding: 4px 10px;
        margin-top: 6px;
        display: inline-block;
    }

    .profile-info {
        width: 100%;
        padding-left: 40px;
    }

    .info-label {
        font-weight: 600;
        color: #0a2647;
        width: 170px;
        display: inline-block;
    }

    .info-row {
        margin-bottom: 10px;
        font-size: 0.95rem;
    }

    .section-title {
        background-color: #0a2647;
        color: white;
        padding: 6px 14px;
        font-weight: 600;
        border-radius: 8px;
        display: inline-block;
        margin-bottom: 20px;
        font-size: 1rem;
    }

    .btn-edit {
        background-color: #ffc107;
        color: #000;
        font-weight: bold;
        padding: 8px 20px;
        border-radius: 6px;
    }

    .btn-edit:hover {
        background-color: #e0a800;
    }

    @media(max-width: 768px) {
        .profile-header {
            flex-direction: column;
            align-items: center;
        }

        .profile-info {
            padding-left: 0;
            margin-top: 20px;
        }
    }
</style>

<div class="container-fluid">
    <div class="profile-container">
        <div class="profile-header">
            <div class="text-center">
                @if ($user->foto)
                    <img src="{{ asset('storage/' . $user->foto) }}" class="profile-photo">
                @else
                    <img src="{{ asset('adminlte/dist/img/avatar.png') }}" class="profile-photo">
                @endif
                <div class="profile-name">{{ $user->nama }}</div>
                <div class="badge-nip">{{ $user->nip }}</div>
            </div>
            <div class="profile-info">
                <div class="section-title"># Biodata Pegawai</div>

                <div class="info-row"><span class="info-label">Jenis Kelamin:</span> {{ $user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                <div class="info-row"><span class="info-label">Email:</span> {{ $user->email }}</div>
                <div class="info-row"><span class="info-label">No. Telepon:</span> {{ $user->no_tlp ?? '-' }}</div>
                <div class="info-row"><span class="info-label">Agama:</span> {{ $user->agama ?? '-' }}</div>
                <div class="info-row"><span class="info-label">Jabatan:</span> {{ $user->jabatan->refJabatan->nama_jabatan ?? '-' }}</div>
                <div class="info-row"><span class="info-label">Pangkat:</span> {{ $user->pangkat->refPangkat->golongan_pangkat ?? '-' }}</div>

                <div class="mt-4">
                    <a href="{{ route('profil.edit') }}" class="btn btn-edit">
                        <i class="fas fa-edit mr-1"></i> Edit Profil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
