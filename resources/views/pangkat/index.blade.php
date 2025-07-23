@extends('layouts.template')
@section('title', 'Data Pangkat')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Data Pangkat Pegawai</h5>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-bordered text-sm m-0">
                <thead class="thead-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Golongan</th>
                        <th>Gaji Pokok</th>
                        <th>Masa Kerja</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pangkat as $i => $data)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $data->user->nama }}</td>
                        <td>{{ $data->refGolongan->golongan_pangkat }}</td>
                        <td>Rp{{ number_format($data->refGolongan->gaji_pokok) }}</td>
                        <td>{{ $data->refGolongan->masa_kerja_min }} - {{ $data->refGolongan->masa_kerja_maks }} tahun</td>
                        <td class="text-center">
                            <a href="{{ url('/pangkat/' . $data->id_pangkat) }}" class="btn btn-info btn-sm" title="Lihat">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ url('/pangkat/' . $data->id_pangkat . '/edit') }}" class="btn btn-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ url('/pangkat/' . $data->id_pangkat) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($pangkat->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada data pangkat pegawai.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
