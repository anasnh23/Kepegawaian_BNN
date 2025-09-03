@extends('layouts.template')
@section('title', 'Ubah Status Approval Pimpinan')

@section('content')
<style>
:root {
  --bnn-navy:#003366; --bnn-gold:#ffc107;
}
.card-header-bnn {
  background: var(--bnn-navy); color:#fff; font-weight:700;
}
label { font-weight:600; }
.btn-bnn {
  border-radius:6px; padding:8px 16px; font-weight:600;
}
.btn-save { background:#4e73df; color:#fff; }
.btn-save:hover { background:#3752c8; color:#fff; }
.btn-cancel { background:#6c757d; color:#fff; }
.btn-cancel:hover { background:#555e64; color:#fff; }
</style>

<div class="container-fluid">
  <div class="card shadow">
    <div class="card-header card-header-bnn">
      <h4 class="card-title"><i class="fas fa-edit mr-2 text-warning"></i> Ubah Status Cuti Pegawai</h4>
    </div>

    <div class="card-body">
      <form id="formApproval" action="{{ route('approval.updateStatus', $approval->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Nama Pegawai</label>
            <input type="text" class="form-control" 
                   value="{{ $approval->cuti->pegawai->nama ?? '-' }}" readonly>
          </div>

          <div class="col-md-6 mb-3">
            <label>Jenis Cuti</label>
            <input type="text" class="form-control" 
                   value="{{ $approval->cuti->jenis_cuti }}" readonly>
          </div>

          <div class="col-md-6 mb-3">
            <label>Periode Cuti</label>
            <input type="text" class="form-control"
              value="{{ date('d-m-Y', strtotime($approval->cuti->tanggal_mulai)) }} 
                     s/d {{ date('d-m-Y', strtotime($approval->cuti->tanggal_selesai)) }}"
              readonly>
          </div>

          <div class="col-md-6 mb-3">
            <label>Status Pimpinan</label>
            <select name="status" class="form-control" required>
              <option value="Menunggu" {{ $approval->status == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
              <option value="Disetujui" {{ $approval->status == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
              <option value="Ditolak" {{ $approval->status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
          </div>

          <div class="col-md-12 mb-3">
            <label>Keterangan</label>
            <textarea class="form-control" rows="2" readonly>{{ $approval->cuti->keterangan ?? '-' }}</textarea>
          </div>
        </div>

        <div class="mt-3">
          <button type="submit" class="btn btn-bnn btn-save mr-2">
            <i class="fas fa-save"></i> Simpan Perubahan
          </button>
          <a href="{{ route('approval.dokumen') }}" class="btn btn-bnn btn-cancel">
            <i class="fas fa-arrow-left"></i> Kembali
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('formApproval').addEventListener('submit', function(e){
  e.preventDefault();
  Swal.fire({
    title: 'Konfirmasi',
    text: 'Apakah Anda yakin ingin menyimpan perubahan status cuti?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, Simpan',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#003366'
  }).then((result) => {
    if (result.isConfirmed) {
      e.target.submit();
    }
  });
});
</script>

@if(session('success'))
<script>
Swal.fire('Berhasil','{{ session('success') }}','success');
</script>
@endif
@if(session('error'))
<script>
Swal.fire('Gagal','{{ session('error') }}','error');
</script>
@endif
@endpush
