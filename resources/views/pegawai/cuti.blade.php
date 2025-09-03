@extends('layouts.template')
@section('content')

@push('styles')
  {{-- SweetAlert2 & AdminLTE --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

<style>
  :root { --bnn-navy:#003366; --bnn-gold:#ffc107; --bnn-green:#198754; --muted:#6c757d; }
  body { background:linear-gradient(180deg,#f8fbff 0%,#f5f8fc 100%); }
  .bnn-hero { background:linear-gradient(135deg,var(--bnn-navy),#064d9b); color:#fff; border-radius:1rem; padding:1.5rem 2rem; }
  .bnn-hero-badge { background:rgba(255,193,7,.15); color:#ffd95e; border:1px solid rgba(255,193,7,.35); }
  .card.bnn-card { border:0; border-radius:1rem; box-shadow:0 6px 16px rgba(0,0,0,.06); }
  .bnn-card .card-header { background:var(--bnn-navy); color:#fff; border-bottom:3px solid var(--bnn-gold); font-weight:600; }
  .form-label { font-weight:600; color:var(--bnn-navy); }
  .btn-bnn { background:var(--bnn-green); border:0; border-radius:.75rem; font-weight:600; }
  .btn-bnn-primary { background:#0552a1; border:0; border-radius:.75rem; font-weight:600; }
</style>

<div class="container-fluid py-3">
  {{-- Hero --}}
  <div class="bnn-hero mb-3 d-flex justify-content-between align-items-center">
    <div>
      <span class="badge bnn-hero-badge mb-2">Sistem Kepegawaian BNN</span>
      <h3 class="mb-0">Pengajuan Cuti Pegawai</h3>
      <small class="text-white-50">Ajukan cuti sesuai Peraturan Kepala BNN No. 5 Tahun 2019</small>
    </div>
    <span class="badge bg-light text-dark"><i class="fas fa-calendar-day"></i> {{ date('d M Y') }}</span>
  </div>

  {{-- Card --}}
  <div class="card bnn-card">
    <div class="card-header"><i class="fas fa-calendar-check"></i> Layanan Cuti BNN</div>
    <div class="card-body">
      <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#form">Form Pengajuan</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#upload">Upload ke Pimpinan</a></li>
      </ul>

      <div class="row">
        {{-- Form pengajuan --}}
        <div class="col-lg-8">
          <div class="tab-content">
            <div class="tab-pane fade show active" id="form">
              <form id="formCuti" method="POST" action="{{ url('/cuti/store') }}">
                @csrf
                <div class="form-group">
                  <label class="form-label">Jenis Cuti</label>
                  <select name="jenis_cuti" id="jenis_cuti" class="form-control" required>
                    <option value="">-- Pilih Jenis Cuti --</option>
                    <option value="Tahunan">Cuti Tahunan</option>
                    <option value="Sakit">Cuti Sakit</option>
                    <option value="Melahirkan">Cuti Melahirkan</option>
                    <option value="Penting">Cuti Karena Alasan Penting</option>
                    <option value="Besar">Cuti Besar</option>
                    <option value="Bersama">Cuti Bersama</option>
                    <option value="Luar Tanggungan Negara">Cuti di Luar Tanggungan Negara</option>
                  </select>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
                  </div>
                  <div class="form-group col-md-6">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">Keterangan</label>
                  <textarea name="keterangan" class="form-control" rows="3" placeholder="Tuliskan alasan cuti" required></textarea>
                </div>
                <div class="text-right">
                  <button type="submit" class="btn btn-bnn"><i class="fas fa-paper-plane"></i> Kirim Pengajuan</button>
                </div>
              </form>
            </div>

            {{-- Upload --}}
            <div class="tab-pane fade" id="upload">
              <form id="formUpload" method="POST" action="{{ url('/cuti/upload-dokumen') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                  <label class="form-label">ID Pengajuan</label>
                  <input type="number" name="cuti_id" class="form-control" required>
                </div>
                <div class="form-group">
                  <label class="form-label">Dokumen Persetujuan (.pdf)</label>
                  <input type="file" name="dokumen" class="form-control" accept="application/pdf" required>
                </div>
                <div class="text-right">
                  <button type="submit" class="btn btn-bnn-primary"><i class="fas fa-upload"></i> Upload Dokumen</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        {{-- Ringkasan --}}
        <div class="col-lg-4">
          <div class="card bnn-card">
            <div class="card-header">Ringkasan</div>
            <div class="card-body">
              <p>Jenis Cuti: <span id="sumJenis">-</span></p>
              <p>Rentang: <span id="sumRange">-</span></p>
              <p>Total Hari: <span id="sumHari">0</span></p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('adminlte/plugins/moment/moment.min.js') }}"></script>
  <script>
    function countWorkdays(start, end){
      let s = moment(start), e = moment(end), count = 0;
      while(s <= e){
        if(s.isoWeekday() < 6) count++;
        s.add(1, 'day');
      }
      return count;
    }

    function updateRingkasan(){
      const jenis = document.getElementById('jenis_cuti').value;
      const t1 = document.getElementById('tanggal_mulai').value;
      const t2 = document.getElementById('tanggal_selesai').value;

      document.getElementById('sumJenis').innerText = jenis || '-';
      if(t1 && t2){
        document.getElementById('sumRange').innerText = `${t1} s.d ${t2}`;
        document.getElementById('sumHari').innerText = countWorkdays(t1, t2);
      }
    }

    document.getElementById('jenis_cuti').addEventListener('change', updateRingkasan);
    document.getElementById('tanggal_mulai').addEventListener('change', updateRingkasan);
    document.getElementById('tanggal_selesai').addEventListener('change', updateRingkasan);

    // === AJAX Pengajuan Cuti ===
    document.getElementById('formCuti').addEventListener('submit', function(e){
      e.preventDefault();
      let formData = new FormData(this);

      fetch(this.action, {
        method: "POST",
        headers: { "X-CSRF-TOKEN": formData.get("_token") },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: data.message || "Pengajuan cuti berhasil dikirim",
          confirmButtonColor: "#198754"
        });
        this.reset();
        updateRingkasan();
      })
      .catch(async err => {
        let msg = "Terjadi kesalahan.";
        try {
          const errorData = await err.response.json();
          msg = errorData.message || msg;
        } catch(e){}
        Swal.fire({
          icon: "error",
          title: "Gagal",
          text: msg,
          confirmButtonColor: "#d33"
        });
      });
    });

    // === AJAX Upload Dokumen ===
    document.getElementById('formUpload').addEventListener('submit', function(e){
      e.preventDefault();
      let formData = new FormData(this);

      fetch(this.action, {
        method: "POST",
        headers: { "X-CSRF-TOKEN": formData.get("_token") },
        body: formData
      })
      .then(res => res.ok ? res : Promise.reject(res))
      .then(res => res.text())
      .then(() => {
        Swal.fire({
          icon: "success",
          title: "Upload Berhasil!",
          text: "Dokumen cuti berhasil diupload ke pimpinan.",
          confirmButtonColor: "#198754"
        });
        this.reset();
      })
      .catch(() => {
        Swal.fire({
          icon: "error",
          title: "Upload Gagal",
          text: "Periksa kembali dokumen yang diupload.",
          confirmButtonColor: "#d33"
        });
      });
    });
  </script>
@endpush

@endsection
