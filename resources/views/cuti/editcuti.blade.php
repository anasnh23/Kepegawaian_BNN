@extends('layouts.template')
@section('content')

<div class="container-fluid">
  <div class="card shadow">
    <div class="card-header bg-warning text-dark">
      <h4 class="card-title">Ubah Status Cuti Pegawai</h4>
    </div>

    <div class="card-body">
      <form action="{{ url('/cuti/update-status/'.$cuti->id_cuti) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Nama Pegawai</label>
            <input type="text" class="form-control" value="{{ optional($cuti->pegawai)->nama ?? '-' }}" readonly>
          </div>

          <div class="col-md-6 mb-3">
            <label>Jenis Cuti</label>
            <input type="text" class="form-control" value="{{ $cuti->jenis_cuti }}" readonly>
          </div>

          <div class="col-md-6 mb-3">
            <label>Periode Cuti</label>
            <input type="text" class="form-control"
              value="{{ date('d-m-Y', strtotime($cuti->tanggal_mulai)) }} s/d {{ date('d-m-Y', strtotime($cuti->tanggal_selesai)) }}"
              readonly>
          </div>

          <div class="col-md-6 mb-3">
            <label>Status</label>
            <select name="status" class="form-control" required>
              <option value="Menunggu" {{ $cuti->status == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
              <option value="Disetujui" {{ $cuti->status == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
              <option value="Ditolak" {{ $cuti->status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
          </div>

          <div class="col-md-12 mb-3">
            <label>Keterangan</label>
            <textarea class="form-control" rows="2" readonly>{{ $cuti->keterangan ?? '-' }}</textarea>
          </div>

        </div>

        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Simpan Perubahan
        </button>
        <a href="{{ url('/cuti') }}" class="btn btn-secondary">Kembali</a>
      </form>
    </div>
  </div>
</div>

@endsection
