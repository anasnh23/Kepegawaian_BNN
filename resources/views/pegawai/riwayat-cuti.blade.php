@extends('layouts.template')
@section('content')
<div class="container-fluid">
  <div class="card shadow">
    <div class="card-header bg-info text-white">
      <h4 class="card-title">{{ $breadcrumb->title }}</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover text-center">
<thead class="thead-dark">
  <tr>
    <th>No</th>
    <th>Nama</th>       {{-- Tambahkan --}}
    <th>NIP</th>        {{-- Tambahkan --}}
    <th>Tanggal Pengajuan</th>
    <th>Jenis Cuti</th>
    <th>Periode</th>
    <th>Lama</th>
    <th>Keterangan</th>
    <th>Status</th>
    <th>Aksi</th>
  </tr>
</thead>
<tbody>
@forelse($cuti as $item)
<tr>
  <td>{{ $loop->iteration }}</td>
  <td>{{ $item->pegawai->nama ?? '-' }}</td>
  <td>{{ $item->pegawai->nip ?? '-' }}</td>
  <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
  <td>{{ $item->jenis_cuti }}</td>
  <td>
    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') }} s/d
    {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d-m-Y') }}
  </td>
  <td>{{ $item->lama_cuti }} hari</td>
  <td class="text-left">{{ $item->keterangan ?? '-' }}</td>
  <td>
    @if($item->status == 'Disetujui')
      <span class="badge bg-success">Disetujui</span>
    @elseif($item->status == 'Ditolak')
      <span class="badge bg-danger">Ditolak</span>
    @else
      <span class="badge bg-warning text-dark">Menunggu</span>
    @endif
  </td>
  <td>
    @if($item->status == 'Disetujui')
      <a href="{{ url('/cuti/cetak/' . $item->id_cuti) }}" target="_blank" class="btn btn-sm btn-primary">
        <i class="fas fa-print"></i> Cetak
      </a>
    @else
      <span class="text-muted">-</span>
    @endif
  </td>
</tr>
@empty
<tr>
  <td colspan="10">Belum ada data cuti</td>
</tr>
@endforelse
</tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
