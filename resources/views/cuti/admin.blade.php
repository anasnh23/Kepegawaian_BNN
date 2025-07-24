@extends('layouts.template')
@section('content')
<div class="container-fluid">
  <div class="card shadow">
    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
      <h4 class="card-title mb-0">Manajemen Cuti Pegawai</h4>
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
              <th>Keterangan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($cuti as $item)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ optional($item->pegawai)->nama ?? '-' }}</td>
              <td>{{ $item->jenis_cuti }}</td>
              <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
              <td>
                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') }}
                s/d
                {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d-m-Y') }}
              </td>
              <td>{{ $item->lama_cuti }} hari</td>
              <td>{{ $item->keterangan ?? '-' }}</td>
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
                @if($item->status == 'Menunggu')
                  <button onclick="setujui({{ $item->id_cuti }})" class="btn btn-sm btn-success me-1">
                    <i class="fas fa-check"></i>
                  </button>
                  <button onclick="tolak({{ $item->id_cuti }})" class="btn btn-sm btn-danger me-1">
                    <i class="fas fa-times"></i>
                  </button>
                @endif
                <button class="btn btn-sm btn-warning btnEditStatus" data-id="{{ $item->id_cuti }}" data-status="{{ $item->status }}">
                  <i class="fas fa-pencil-alt"></i>
                </button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="9">Tidak ada data cuti</td>
            </tr>
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
    title: 'Setujui cuti ini?',
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
    title: 'Tolak cuti ini?',
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
    url: "{{ url('/cuti/set-status') }}",
    type: "POST",
    data: {
      _token: '{{ csrf_token() }}',
      id: id,
      status: status
    },
    success: function(res) {
      Swal.fire('Berhasil', res.message, 'success').then(() => location.reload());
    },
    error: function(err) {
      Swal.fire('Gagal', 'Terjadi kesalahan.', 'error');
    }
  });
}

$(document).ready(function() {
  $('.btnEditStatus').click(function() {
    const id = $(this).data('id');
    const current = $(this).data('status');

    Swal.fire({
      title: 'Ubah Status Cuti',
      input: 'select',
      inputOptions: {
        'Menunggu': 'Menunggu',
        'Disetujui': 'Disetujui',
        'Ditolak': 'Ditolak'
      },
      inputValue: current,
      showCancelButton: true,
      confirmButtonText: 'Simpan',
      inputLabel: 'Pilih status baru'
    }).then((result) => {
      if (result.isConfirmed && result.value !== current) {
        updateStatus(id, result.value);
      }
    });
  });
});
</script>
@endpush
