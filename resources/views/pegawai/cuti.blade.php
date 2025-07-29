@extends('layouts.template')
@section('content')

<style>
  .nav-tabs .nav-link.active {
    background-color: #003366;
    color: #fff !important;
    font-weight: bold;
    border-color: #003366 #003366 #fff;
  }
  .nav-tabs .nav-link {
    color: #003366;
    font-weight: 500;
  }
  .card-header {
    background-color: #003366;
    color: white;
    border-bottom: 2px solid #ffc107;
  }
  .card-title i {
    margin-right: 6px;
  }
  .form-label {
    font-weight: 600;
    color: #003366;
  }
  .btn-success {
    background-color: #198754;
    border-color: #198754;
  }
  .btn-primary {
    background-color: #0056b3;
    border-color: #0056b3;
  }
</style>

<div class="container-fluid">
  <div class="card shadow border-0">
    <div class="card-header">
      <h5 class="card-title"><i class="fas fa-calendar-check"></i> Pengajuan Cuti Pegawai BNN</h5>
    </div>
    <div class="card-body">
      
      {{-- Tabs --}}
      <ul class="nav nav-tabs mb-4" id="cutiTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" data-toggle="tab" href="#form" role="tab">
            <i class="fas fa-edit"></i> Form Pengajuan
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#upl_pimp" role="tab">
            <i class="fas fa-upload"></i> Upload ke Pimpinan
          </a>
        </li>
      </ul>

      {{-- Tab Content --}}
      <div class="tab-content" id="cutiTabContent">

        {{-- Form Pengajuan --}}
        <div class="tab-pane fade show active" id="form" role="tabpanel">
          <form id="formCuti" method="POST" action="{{ url('/cuti/store') }}">
            @csrf
            <div class="row">
              <div class="col-md-6">
                <label class="form-label">Jenis Cuti</label>
                <select name="jenis_cuti" class="form-control" required>
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
              <div class="col-md-6">
                <label class="form-label">Tanggal Pengajuan</label>
                <input type="date" name="tanggal_pengajuan" class="form-control" value="{{ date('Y-m-d') }}" readonly>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-6">
                <label class="form-label">Tanggal Mulai Cuti</label>
                <input type="date" name="tanggal_mulai" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Tanggal Selesai Cuti</label>
                <input type="date" name="tanggal_selesai" class="form-control" required>
              </div>
            </div>

            <div class="form-group mt-3">
              <label class="form-label">Keterangan</label>
              <textarea name="keterangan" rows="3" class="form-control" placeholder="Tuliskan alasan cuti..." required></textarea>
            </div>

            <button type="submit" class="btn btn-success mt-3">
              <i class="fas fa-paper-plane"></i> Kirim Pengajuan
            </button>
          </form>

        </div>

        {{-- Upload ke Pimpinan --}}
        <div class="tab-pane fade" id="upl_pimp" role="tabpanel">
          <form method="POST" action="{{ url('/cuti/upload-dokumen') }}" enctype="multipart/form-data" class="p-3 mt-2">
            @csrf
            <div class="form-group">
              <label class="form-label">ID Pengajuan Cuti</label>
              <input type="number" name="cuti_id" class="form-control" placeholder="ID cuti disetujui oleh admin" required>
            </div>

            <div class="form-group mt-3">
              <label class="form-label">Upload Dokumen Cuti (.pdf)</label>
              <input type="file" name="dokumen" class="form-control" accept="application/pdf" required>
            </div>

            <button type="submit" class="btn btn-primary mt-3">
              <i class="fas fa-upload"></i> Upload ke Pimpinan
            </button>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function () {
    $('#formCuti').submit(function (e) {
      e.preventDefault();
      let formData = $(this).serialize();
      $.ajax({
        url: "{{ url('/cuti/store') }}",
        method: "POST",
        data: formData,
        success: function (res) {
          Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: res.message,
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            window.location.reload();
          });
        },
        error: function (xhr) {
          let response = xhr.responseJSON;
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            html: response.message ?? 'Terjadi kesalahan validasi.',
          });
        }
      });
    });
  });
</script>
@endpush

@endsection
