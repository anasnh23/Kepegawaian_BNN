@extends('layouts.template')
@section('title', 'Ubah Status Approval Pimpinan')

@section('content')
<div class="container-fluid">
  <div class="card shadow">
    <div class="card-header bg-warning text-dark">
      <h4 class="card-title">Ubah Status Cuti Pegawai</h4>
    </div>

    <div class="card-body">
      <form action="{{ route('approval.updateStatus', $approval->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Nama Pegawai</label>
            <input type="text" class="form-control" value="{{ $approval->cuti->pegawai->nama ?? '-' }}" readonly>
          </div>

          <div class="col-md-6 mb-3">
            <label>Jenis Cuti</label>
            <input type="text" class="form-control" value="{{ $approval->cuti->jenis_cuti }}" readonly>
          </div>

          <div class="col-md-6 mb-3">
            <label>Periode Cuti</label>
            <input type="text" class="form-control"
              value="{{ date('d-m-Y', strtotime($approval->cuti->tanggal_mulai)) }} s/d {{ date('d-m-Y', strtotime($approval->cuti->tanggal_selesai)) }}"
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

        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Simpan Perubahan
        </button>
        <a href="{{ route('approval.dokumen') }}" class="btn btn-secondary">Kembali</a>
      </form>
    </div>
  </div>
</div>
@endsection
