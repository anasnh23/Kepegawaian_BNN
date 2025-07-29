@extends('layouts.template')
@section('title', 'Riwayat Persetujuan Dokumen')

@section('content')
<div class="container-fluid">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h4 class="card-title"><i class="fas fa-check-double"></i> Riwayat Persetujuan Dokumen Cuti</h4>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover text-center">
          <thead class="thead-dark">
            <tr>
              <th>No</th>
              <th>Nama Pegawai</th>
              <th>NIP</th>
              <th>Jenis Cuti</th>
              <th>Periode</th>
              <th>Lama Cuti</th>
              <th>Dokumen</th>
              <th>Status</th>
              <th>Tanggal Persetujuan</th>
            </tr>
          </thead>
          <tbody>
            @forelse($cuti as $item)
              @php $approval = $item->approvalPimpinan; @endphp
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->pegawai->nama ?? '-' }}</td>
                <td>{{ $item->pegawai->nip ?? '-' }}</td>
                <td>{{ $item->jenis_cuti }}</td>
                <td>
                  {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') }}
                  s/d
                  {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d-m-Y') }}
                </td>
                <td>{{ $item->lama_cuti }} hari</td>
                <td>
                  @if($approval && $approval->dokumen_path)
                    <a href="{{ asset('storage/' . $approval->dokumen_path) }}" target="_blank" class="btn btn-outline-info btn-sm">
                      <i class="fas fa-file-pdf"></i> Lihat
                    </a>
                  @else
                    <span class="text-muted">Tidak Ada</span>
                  @endif
                </td>
                <td>
                  @if($approval?->status == 'Disetujui')
                    <span class="badge badge-success">Disetujui</span>
                  @elseif($approval?->status == 'Ditolak')
                    <span class="badge badge-danger">Ditolak</span>
                  @else
                    <span class="badge badge-warning text-dark">Menunggu</span>
                  @endif
                </td>
                <td>
                  {{ $approval->updated_at ? \Carbon\Carbon::parse($approval->updated_at)->format('d-m-Y H:i') : '-' }}
                </td>
              </tr>
            @empty
              <tr><td colspan="9">Belum ada dokumen yang diproses</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
