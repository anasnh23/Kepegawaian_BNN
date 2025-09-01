@extends('layouts.template')
@section('content')

@push('styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
@endpush

<style>
  :root {
    --bnn-navy:#003366; --bnn-gold:#ffc107; --bnn-green:#198754; --muted:#6c757d;
  }
  body {
    background: linear-gradient(180deg, #f8fbff 0%, #f5f8fc 100%);
  }
  .bnn-hero {
    background: linear-gradient(135deg, var(--bnn-navy), #064d9b);
    color: #fff; border-radius: 1rem; padding: 1.5rem 2rem;
  }
  .bnn-hero-badge { background: rgba(255,193,7,.15); color:#ffd95e; border: 1px solid rgba(255,193,7,.35); }
  .card.bnn-card { border:0; border-radius:1rem; box-shadow: 0 6px 16px rgba(0,0,0,.06); }
  .bnn-card .card-header { background: var(--bnn-navy); color:#fff; border-bottom: 3px solid var(--bnn-gold); border-top-left-radius:1rem; border-top-right-radius:1rem; }
  .bnn-tabs .nav-link { font-weight:600; color: var(--bnn-navy); }
  .bnn-tabs .nav-link.active { background: var(--bnn-navy); color: #fff !important; border-radius: 0.75rem; }

  .form-label { font-weight:600; color: var(--bnn-navy); }
  .form-control, .form-select {
    border-radius: .75rem;
    border: 1px solid #ced4da;
    padding: .6rem .9rem;
    font-size: .95rem;
    height: auto;
    line-height: 1.5;
    background-color: #fff;
  }
  .form-select {
    -webkit-appearance:none; -moz-appearance:none; appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M4 6l4 4 4-4' stroke='%23003366' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right .8rem center; background-size:16px;
    padding-right: 2.2rem;
  }
  .form-control:focus, .form-select:focus { border-color:#88b4ff; box-shadow:0 0 0 .2rem rgba(13,110,253,.12); }
  .form-select:invalid { color:#6c757d; }
  .form-select option[value=""] { color:#6c757d; }
  .bnn-hint { font-size: .8rem; color: var(--muted); }

  .btn-bnn { background: var(--bnn-green); border-color: var(--bnn-green); border-radius:.75rem; font-weight:600; }
  .btn-bnn-primary { background:#0552a1; border-color:#0552a1; border-radius:.75rem; font-weight:600; }
</style>

<div class="container-fluid py-3">
  <div class="bnn-hero mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
      <div>
        <span class="badge bnn-hero-badge mb-2">Sistem Kepegawaian BNN</span>
        <h3 class="mb-0">Pengajuan Cuti Pegawai</h3>
        <small class="text-white-50">Ajukan cuti sesuai Peraturan Kepala BNN No. 5 Tahun 2019</small>
      </div>
      <div class="text-end">
        <span class="badge bg-light text-dark">Tanggal: {{ date('Y-m-d') }}</span>
      </div>
    </div>
  </div>

  <div class="card bnn-card">
    <div class="card-header">
      <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Layanan Cuti BNN</h5>
    </div>
    <div class="card-body">

      <ul class="nav nav-tabs bnn-tabs mb-3">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#form">Form Pengajuan</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#upload">Upload ke Pimpinan</a></li>
      </ul>

      <div class="row">
        <div class="col-lg-8">
          <div class="tab-content">
            <div class="tab-pane fade show active" id="form">
              <form id="formCuti" method="POST" action="{{ url('/cuti/store') }}">
                @csrf
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label" for="jenis_cuti">Jenis Cuti</label>
                    <select id="jenis_cuti" name="jenis_cuti" class="form-select" required>
                      <option value="">-- Pilih Jenis Cuti --</option>
                      <option value="Tahunan">Cuti Tahunan</option>
                      <option value="Sakit">Cuti Sakit</option>
                      <option value="Melahirkan">Cuti Melahirkan</option>
                      <option value="Penting">Cuti Karena Alasan Penting</option>
                      <option value="Besar">Cuti Besar</option>
                      <option value="Bersama">Cuti Bersama</option>
                      <option value="Luar Tanggungan Negara">Cuti di Luar Tanggungan Negara</option>
                    </select>
                    <div class="bnn-hint">Pilih jenis cuti sesuai ketentuan berlaku.</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="tanggal_pengajuan">Tanggal Pengajuan</label>
                    <input id="tanggal_pengajuan" type="text" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="tanggal_mulai">Tanggal Mulai</label>
                    <input id="tanggal_mulai" name="tanggal_mulai" type="text" class="form-control datepick" placeholder="Pilih tanggal" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="tanggal_selesai">Tanggal Selesai</label>
                    <input id="tanggal_selesai" name="tanggal_selesai" type="text" class="form-control datepick" placeholder="Pilih tanggal" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label" for="keterangan">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" rows="3" class="form-control" placeholder="Tuliskan alasan cuti dengan jelas" required></textarea>
                  </div>
                </div>
                <div class="text-end mt-3">
                  <button type="submit" class="btn btn-bnn"><i class="fas fa-paper-plane"></i> Kirim Pengajuan</button>
                </div>
              </form>
            </div>

            <div class="tab-pane fade" id="upload">
              <form id="formUpload" method="POST" action="{{ url('/cuti/upload-dokumen') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label" for="cuti_id">ID Pengajuan</label>
                    <input type="number" id="cuti_id" name="cuti_id" class="form-control" placeholder="ID cuti disetujui admin" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="dokumen">Dokumen (.pdf)</label>
                    <input type="file" id="dokumen" name="dokumen" class="form-control" accept="application/pdf" required>
                  </div>
                </div>
                <div class="text-end mt-3">
                  <button type="submit" class="btn btn-bnn-primary"><i class="fas fa-upload"></i> Upload</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card bnn-card mb-3">
            <div class="card-header">Ringkasan</div>
            <div class="card-body">
              <p class="mb-1">Jenis Cuti: <span id="sumJenis">-</span></p>
              <p class="mb-1">Rentang: <span id="sumRange">-</span></p>
              <p>Total Hari: <span id="sumHari2">0</span></p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const fpOpts = { dateFormat:'Y-m-d', allowInput:true, minDate:'today'};
  flatpickr('#tanggal_mulai', fpOpts);
  flatpickr('#tanggal_selesai', fpOpts);

  document.getElementById('jenis_cuti').addEventListener('change', e=>{
    document.getElementById('sumJenis').innerText = e.target.value || '-';
  });
</script>
@endpush

@endsection
