@extends('layouts.template')
@section('title', 'Approval Dokumen Cuti')

@section('content')
<div class="container-fluid">
  <div class="card shadow">
    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
      <h4 class="card-title mb-0">Approval Dokumen Cuti Pegawai</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover text-center">
          <thead class="bg-primary text-white">
            <tr>
              <th>No</th>
              <th>Nama Pegawai</th>
              <th>Jenis Cuti</th>
              <th>Pengajuan</th>
              <th>Periode</th>
              <th>Lama</th>
              <th>Dokumen</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($cuti as $item)
              @php $approval = $item->approvalPimpinan; @endphp
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ optional($item->pegawai)->nama ?? '-' }}</td>
                <td>{{ $item->jenis_cuti ?? '-' }}</td>
                <td>{{ $item->tanggal_pengajuan ? \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') : '-' }}</td>
                <td>
                  {{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') : '-' }}
                  s/d
                  {{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d-m-Y') : '-' }}
                </td>
                <td>{{ $item->lama_cuti ? $item->lama_cuti . ' hari' : '-' }}</td>
                <td>
                  @if($approval && $approval->dokumen_path)
                    <a href="{{ asset('storage/' . $approval->dokumen_path) }}" class="badge bg-primary text-white" target="_blank">Lihat</a>
                  @else
                    <span class="text-muted">Belum Upload</span>
                  @endif
                </td>
                <td>
                  @if($approval?->status == 'Disetujui')
                    <span class="badge bg-success">Disetujui</span>
                  @elseif($approval?->status == 'Ditolak')
                    <span class="badge bg-danger">Ditolak</span>
                  @else
                    <span class="badge bg-warning text-dark">Menunggu</span>
                  @endif
                </td>
                <td>
                  @if($approval && ($approval->status === null || $approval->status === 'Menunggu'))
                    <button onclick="setujui({{ $approval->id }})" class="btn btn-sm btn-success me-1"><i class="fas fa-check"></i></button>
                    <button onclick="tolak({{ $approval->id }})" class="btn btn-sm btn-danger me-1"><i class="fas fa-times"></i></button>
                  @else
                    <span class="text-muted">Selesai</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="9">Tidak ada data dokumen cuti</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function setujui(id) {
  Swal.fire({
    title: 'Setujui dokumen ini?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, Setujui'
  }).then((result) => {
    if (result.isConfirmed) {
      updateStatus(id, 'Disetujui');
    }
  });
}

function tolak(id) {
  Swal.fire({
    title: 'Tolak dokumen ini?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, Tolak'
  }).then((result) => {
    if (result.isConfirmed) {
      updateStatus(id, 'Ditolak');
    }
  });
}

function updateStatus(id, status) {
  $.ajax({
    url: "{{ url('/approval-dokumen/update-status') }}",
    method: "POST",
    data: {
      _token: '{{ csrf_token() }}',
      id: id,
      status: status
    },
    success: function(res) {
      Swal.fire('Berhasil', res.message, 'success').then(() => location.reload());
    },
    error: function() {
      Swal.fire('Gagal', 'Terjadi kesalahan saat memperbarui status.', 'error');
    }
  });
}
</script>
@endpush
