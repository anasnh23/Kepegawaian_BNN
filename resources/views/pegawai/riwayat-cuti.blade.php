@extends('layouts.template')
@section('content')
<div class="container-fluid">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h4 class="card-title"><i class="fas fa-history"></i> Riwayat Cuti Pegawai BNN</h4>
    </div>

    <div class="card-body">
      <ul class="nav nav-tabs mb-3 border-bottom border-warning" id="riwayatTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active font-weight-bold" id="admin-tab" data-toggle="tab" href="#riwayat_admin" role="tab">
            <i class="fas fa-user-check"></i> Approval Admin
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link font-weight-bold" id="pimpinan-tab" data-toggle="tab" href="#riwayat_pimpinan" role="tab">
            <i class="fas fa-user-tie"></i> Persetujuan Pimpinan
          </a>
        </li>
      </ul>

      <div class="tab-content" id="riwayatTabContent">
        {{-- Tab Riwayat Admin --}}
        <div class="tab-pane fade show active" id="riwayat_admin" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover text-center">
              <thead class="thead-dark">
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>NIP</th>
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
                  <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d-m-Y') }}</td>
                  <td>{{ $item->lama_cuti }} hari</td>
                  <td class="text-left">{{ $item->keterangan ?? '-' }}</td>
                  <td>
                    @if($item->status == 'Disetujui')
                      <span class="badge badge-success">Disetujui</span>
                    @elseif($item->status == 'Ditolak')
                      <span class="badge badge-danger">Ditolak</span>
                    @else
                      <span class="badge badge-warning text-dark">Menunggu</span>
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

        {{-- Tab Riwayat Pimpinan --}}
        <div class="tab-pane fade" id="riwayat_pimpinan" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover text-center">
              <thead class="thead-dark">
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>NIP</th>
                  <th>Jenis Cuti</th>
                  <th>Dokumen</th>
                  <th>Status Upload</th>
                  <th>Status Pimpinan</th>
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
                    @if($approval && $approval->dokumen_path)
                      <a href="{{ asset('storage/' . $approval->dokumen_path) }}" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-file-pdf"></i> Lihat
                      </a>
                    @else
                      <span class="text-muted">Belum Upload</span>
                    @endif
                  </td>
                  <td>
                    @if($approval && $approval->dokumen_path)
                      <span class="badge badge-info">Terkirim</span>
                    @else
                      <span class="badge badge-secondary">Belum</span>
                    @endif
                  </td>
                  <td>
                    @if($approval && $approval->status == 'Disetujui')
                      <span class="badge badge-success">Disetujui</span>
                    @elseif($approval && $approval->status == 'Ditolak')
                      <span class="badge badge-danger">Ditolak</span>
                    @else
                      <span class="badge badge-warning text-dark">Menunggu</span>
                    @endif
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7">Belum ada data dokumen cuti</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
