  @extends('layouts.template')
  @section('content')
  <div class="container-fluid">
    <div class="card shadow">
      <div class="card-header bg-primary text-white">
        <h4 class="card-title">Form Pengajuan Cuti Pegawai</h4>
      </div>
      <div class="card-body">
        <form id="formCuti" method="POST" action="{{ url('/cuti/store') }}">
          @csrf
          <div class="row">
            <div class="col-md-6">
              <label for="jenis_cuti">Jenis Cuti</label>
<select class="form-control" name="jenis_cuti" required>
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
              <label for="tanggal_pengajuan">Tanggal Pengajuan</label>
              <input type="date" name="tanggal_pengajuan" class="form-control" value="{{ date('Y-m-d') }}" readonly>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-6">
              <label for="tanggal_mulai">Tanggal Mulai Cuti</label>
              <input type="date" name="tanggal_mulai" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="tanggal_selesai">Tanggal Selesai Cuti</label>
              <input type="date" name="tanggal_selesai" class="form-control" required>
            </div>
          </div>

          <div class="form-group mt-3">
            <label for="keterangan">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2" placeholder="Alasan cuti..."></textarea>
          </div>

          <button type="submit" class="btn btn-success mt-3">
            <i class="fas fa-paper-plane"></i> Ajukan Cuti
          </button>
        </form>
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