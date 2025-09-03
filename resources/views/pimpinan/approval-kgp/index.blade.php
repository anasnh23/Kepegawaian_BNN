@extends('layouts.template')
@section('title', 'Approval Kenaikan Gaji Berkala')

@section('content')
<style>
:root{ --bnn-navy:#003366; --bnn-gold:#ffc107; }
body{ background:#f4f6f9; }
.bnn-hero{
  background:linear-gradient(135deg,var(--bnn-navy),#0b2f5e);
  color:#fff;border-radius:12px;padding:20px 24px;margin-bottom:20px;
  display:flex;justify-content:space-between;align-items:center;
}
.bnn-hero h2{margin:0;font-weight:700;}
.bnn-hero .btn-dashboard{background:var(--bnn-gold);color:#000;font-weight:600;border-radius:6px;}
.table thead th{background:var(--bnn-navy);color:#fff;font-size:.85rem;text-transform:uppercase;}
.badge-status{font-weight:600;border-radius:999px;padding:.35rem .7rem;font-size:.8rem;}
.st-acc{background:#e7f6ec;color:#146c2e;}
.st-pend{background:#fff7e6;color:#7a4d00;}
.st-rej{background:#fdeaea;color:#842029;}
.btn-sm{padding:.25rem .6rem;font-size:.8rem;border-radius:6px;}
</style>

<div class="container-fluid">
  {{-- HERO --}}
  <div class="bnn-hero">
    <div>
      <h2><i class="fas fa-money-check-alt text-warning mr-2"></i> Approval Kenaikan Gaji Berkala</h2>
      <small>Setujui, tolak, dan kelola usulan KGB pegawai</small>
    </div>
    <a href="{{ route('dashboard.pimpinan') }}" class="btn btn-dashboard btn-sm">
      <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
    </a>
  </div>

  {{-- TABLE --}}
  <div class="card shadow-sm">
    <div class="card-header bg-white font-weight-bold"><i class="fas fa-list mr-2"></i> Daftar Usulan KGB</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered table-hover m-0 text-center">
          <thead>
            <tr>
              <th>No</th><th>Nama Pegawai</th><th>Tahun KGB</th><th>TMT</th><th>Status</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($kgp as $row)
              @php
                $status = $row->status ?? 'Menunggu';
                $badge = $status === 'Disetujui' ? 'st-acc' : ($status === 'Ditolak' ? 'st-rej' : 'st-pend');
              @endphp
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="text-left">{{ $row->pegawai->nama ?? '-' }}</td>
                <td>{{ $row->tahun_kgp }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tmt)->format('d-m-Y') }}</td>
                <td><span class="badge-status {{ $badge }}">{{ $status }}</span></td>
                <td>
                  @if($status === 'Menunggu')
                    <button onclick="approveKGB({{ $row->id_kgp }})" class="btn btn-success btn-sm">
                      <i class="fas fa-check"></i> Setujui
                    </button>
                    <button onclick="rejectKGB({{ $row->id_kgp }})" class="btn btn-danger btn-sm">
                      <i class="fas fa-times"></i> Tolak
                    </button>
                  @else
                    <span class="text-muted">Selesai</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-muted">Belum ada usulan KGB</td></tr>
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
// Approve
function approveKGB(id){
  Swal.fire({
    title: 'Setujui usulan KGB ini?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, Setujui',
    confirmButtonColor: '#28a745',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if(result.isConfirmed){
      $.post("{{ url('/approval/kgb/approve') }}/"+id,
        {_token:'{{ csrf_token() }}'},
        function(res){ Swal.fire('Berhasil', res.message, 'success').then(()=>location.reload()); }
      ).fail(()=>Swal.fire('Gagal','Terjadi kesalahan','error'));
    }
  });
}

// Reject
function rejectKGB(id){
  Swal.fire({
    title: 'Tolak usulan KGB',
    input: 'textarea',
    inputLabel: 'Alasan Penolakan',
    inputPlaceholder: 'Tuliskan alasan penolakan...',
    inputAttributes: { 'aria-label': 'Alasan Penolakan' },
    showCancelButton: true,
    confirmButtonText: 'Tolak',
    confirmButtonColor: '#dc3545',
    cancelButtonText: 'Batal',
    preConfirm: (catatan) => {
      if(!catatan){ Swal.showValidationMessage('Alasan wajib diisi'); }
      return catatan;
    }
  }).then((result)=>{
    if(result.isConfirmed){
      $.post("{{ url('/approval/kgb/reject') }}/"+id,
        {_token:'{{ csrf_token() }}',catatan:result.value},
        function(res){ Swal.fire('Ditolak', res.message, 'success').then(()=>location.reload()); }
      ).fail(()=>Swal.fire('Gagal','Terjadi kesalahan','error'));
    }
  });
}
</script>
@endpush
