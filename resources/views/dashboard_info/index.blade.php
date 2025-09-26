@extends('layouts.template')
@section('title', 'Informasi Dashboard')

@section('content')
<style>
  :root{
    --bnn-navy:#003366; --bnn-blue:#144272; --bnn-cyan:#2c74b3; --bnn-gold:#f0ad4e;
    --ink:#0f172a; --muted:#6b7a8c; --soft:#eef3fb; --line:#e6edf6;
  }
  /* Hero */
  .bnn-hero{
    background: linear-gradient(135deg, var(--bnn-navy), #012148 60%, #0b2f5e);
    color:#fff; border-radius:16px; padding:16px 18px; position:relative; overflow:hidden;
    box-shadow:0 12px 28px rgba(0,33,72,.22);
  }
  .bnn-hero::after{
    content:""; position:absolute; right:-60px; top:-60px; width:200px; height:200px; opacity:.08;
    background:url('{{ asset('images/bnn-watermark.svg') }}') no-repeat center/contain;
    pointer-events:none; z-index:-1;
  }
  .bnn-hero h4{ font-weight:800; margin:0; }
  .bnn-hero .sub{ color:#dbe7ff; }

  /* Card */
  .bnn-card{ border:1px solid var(--line); border-radius:14px; overflow:hidden; box-shadow:0 8px 24px rgba(16,24,40,.06); }
  .bnn-card .card-header{
    background: linear-gradient(135deg, var(--bnn-navy), var(--bnn-blue));
    color:#fff; border:0; padding:14px 18px; font-weight:700;
  }

  /* Toolbar */
  .bnn-toolbar{ display:flex; gap:.65rem; align-items:center; justify-content:flex-end; margin:.75rem 0; }
  .btn-bnn{
    background:linear-gradient(135deg, var(--bnn-gold), #ffd666); color:#172554; border:0; font-weight:800;
    border-radius:10px; box-shadow:0 6px 16px rgba(244,196,48,.35);
  }
  .btn-bnn:hover{ filter:brightness(1.04); color:#172554; }
  .filter-chip{
    border-radius:10px; border:1px solid #dde6f4; background:#fff; padding:.45rem .7rem; font-size:.9rem;
    box-shadow:0 1px 2px rgba(16,24,40,.04);
  }
  .filter-chip:focus{ border-color:var(--bnn-cyan); box-shadow:0 0 0 .18rem rgba(44,116,179,.18); }

  /* Table */
  .table thead th{ background:#0f1f39; color:#eaf2ff; border-color:#0f1f39; font-weight:700; vertical-align:middle; }
  .table td, .table th{ border-color:#e7eef3; }
  .table-hover tbody tr:hover{ background:#fbfdff; }
  .thead-sticky{ position:sticky; top:0; z-index:2; }
  table.table tbody td{ vertical-align:middle; }
  .badge-pill{ border-radius:999px; padding:.25rem .6rem; font-weight:700; }
  .badge-target{ background:#eaf1ff; color:#0a3a8a; }
  .badge-aktif{ background:#e9f9ef; color:#0f6b3e; }
  .badge-non{ background:#eceef3; color:#374151; }
</style>

<div class="container-fluid">
  {{-- Hero / Header --}}
  <div class="bnn-hero d-flex align-items-center justify-content-between mb-3">
    <div>
      <h4><i class="fas fa-bullhorn me-2"></i> Informasi Dashboard</h4>
      <div class="sub">Kelola pengumuman/baner yang muncul di beranda pegawai/pimpinan</div>
    </div>
    <div class="d-flex">
      <button type="button" id="btnTambahInfo" class="btn btn-bnn shadow-sm">
        <i class="fas fa-plus me-1"></i> Tambah Informasi
      </button>
    </div>
  </div>

  {{-- Kartu Tabel --}}
  <div class="card bnn-card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <span><i class="fas fa-database me-2"></i>Daftar Informasi</span>
      <div class="d-none d-md-flex align-items-center" style="gap:.5rem;">
        <input type="text" id="filterQ" class="form-control form-control-sm filter-chip" placeholder="Cari judul/konten">
        <select id="filterTarget" class="form-control form-control-sm filter-chip" style="min-width:160px">
          <option value="">-- Semua Target --</option>
          <option value="pegawai">Pegawai</option>
          <option value="pimpinan">Pimpinan</option>
          <option value="semua">Semua</option>
        </select>
        <select id="filterStatus" class="form-control form-control-sm filter-chip" style="min-width:160px">
          <option value="">-- Semua Status --</option>
          <option value="1">Aktif</option>
          <option value="0">Nonaktif</option>
        </select>
        <button type="button" id="btnResetFilter" class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">
          <i class="fas fa-sync-alt"></i> Reset
        </button>
      </div>
    </div>

    <div class="card-body">
      {{-- FILTER (mobile) --}}
      <div class="row mb-3 d-md-none">
        <div class="col-12 mb-2">
          <input type="text" id="filterQ_m" class="form-control filter-chip" placeholder="Cari judul/konten">
        </div>
        <div class="col-6 mb-2">
          <select id="filterTarget_m" class="form-control filter-chip">
            <option value="">-- Semua Target --</option>
            <option value="pegawai">Pegawai</option>
            <option value="pimpinan">Pimpinan</option>
            <option value="semua">Semua</option>
          </select>
        </div>
        <div class="col-6 mb-2">
          <select id="filterStatus_m" class="form-control filter-chip">
            <option value="">-- Semua Status --</option>
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
        </div>
        <div class="col-12">
          <button type="button" id="btnResetFilter_m" class="btn btn-outline-secondary w-100" style="border-radius:10px;">
            <i class="fas fa-sync-alt"></i> Reset
          </button>
        </div>
      </div>

      {{-- Tabel --}}
      <div class="table-responsive">
        <table class="table table-hover text-sm m-0">
          <thead class="thead-sticky text-center">
            <tr>
              <th style="width:56px">No</th>
              <th>Judul</th>
              <th>Konten (ringkas)</th>
              <th style="width:120px">Target</th>
              <th style="width:210px">Periode</th>
              <th style="width:110px">Status</th>
              <th style="width:130px">Urutan</th>
              <th style="width:150px">Aksi</th>
            </tr>
          </thead>
          <tbody id="tabelInfo">
            @forelse ($infos as $index => $row)
              @php
                $mulai   = $row->mulai? \Carbon\Carbon::parse($row->mulai)->translatedFormat('d M Y') : '-';
                $selesai = $row->selesai? \Carbon\Carbon::parse($row->selesai)->translatedFormat('d M Y') : '-';
              @endphp
              <tr id="row-{{ $row->id }}">
                <td class="text-center">{{ $infos->firstItem() + $index }}</td>
                <td class="fw-bold">{{ $row->judul }}</td>
                <td class="text-muted">
                  {{ \Illuminate\Support\Str::limit(strip_tags($row->konten), 120) }}
                </td>
                <td class="text-center">
                  <span class="badge badge-pill badge-target">{{ strtoupper($row->target) }}</span>
                </td>
                <td class="text-center">{{ $mulai }} — {{ $selesai }}</td>
                <td class="text-center">
                  <span class="badge badge-pill {{ $row->aktif ? 'badge-aktif' : 'badge-non' }}">
                    {{ $row->aktif ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td class="text-center">
                  <span class="badge bg-light text-dark">{{ (int)$row->urutan }}</span>
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-info btnViewInfo" data-id="{{ $row->id }}" title="Lihat">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-warning btnEditInfo" data-id="{{ $row->id }}" title="Ubah">
                      <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button type="button" class="btn btn-danger btnDeleteInfo" data-id="{{ $row->id }}" title="Hapus">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="8" class="text-center text-muted py-4">Belum ada informasi.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Paginate --}}
      <div class="mt-3 d-flex justify-content-between align-items-center">
        <div class="text-muted small">Menampilkan {{ $infos->count() }} dari {{ $infos->total() }} data</div>
        <div>{{ $infos->withQueryString()->links() }}</div>
      </div>
    </div>
  </div>

  {{-- Modal (konten via AJAX) --}}
  <div class="modal fade" id="modalFormInfo" tabindex="-1" aria-labelledby="modalInfoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content" id="modalInfoContent">
        {{-- konten akan diload via AJAX --}}
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

  // === CREATE ===
  $('#btnTambahInfo').on('click', function () {
    $('#modalFormInfo').modal('show');
    $('#modalInfoContent').load("{{ route('dashboard-info.create') }}");
  });

  // submit create
  $(document).on('submit', '#formCreateInfo', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    $.ajax({
      url: "{{ route('dashboard-info.store') }}",
      type: "POST",
      data: formData, contentType: false, processData: false,
      success: function (res) {
        $('#modalFormInfo').modal('hide');
        if (window.Swal) Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message || 'Informasi ditambahkan.', timer: 1500, showConfirmButton: false });
        setTimeout(() => location.reload(), 1200);
      },
      error: function (err) {
        const msg = err.responseJSON?.message || 'Gagal menyimpan data.';
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Gagal!', text: msg });
      }
    });
  });

  // === VIEW ===
  $(document).on('click', '.btnViewInfo', function () {
    const id = $(this).data('id');
    $('#modalFormInfo').modal('show');
    $('#modalInfoContent').load("{{ url('dashboard-info') }}/" + id); // show()
  });

  // === EDIT ===
  $(document).on('click', '.btnEditInfo', function () {
    const id = $(this).data('id');
    $('#modalFormInfo').modal('show');
    $('#modalInfoContent').load("{{ url('dashboard-info') }}/" + id + "/edit");
  });

  // submit edit
  $(document).on('submit', '#formEditInfo', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const id = formData.get('id'); // pastikan hidden input name="id" ada di _edit
    formData.append('_method', 'PUT');
    $.ajax({
      url: "{{ url('dashboard-info') }}/" + id,
      type: "POST",
      data: formData, contentType: false, processData: false,
      success: function (res) {
        $('#modalFormInfo').modal('hide');
        if (window.Swal) Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message || 'Informasi diperbarui.', timer: 1500, showConfirmButton: false });
        setTimeout(() => location.reload(), 1200);
      },
      error: function (err) {
        const msg = err.responseJSON?.message || 'Gagal update data.';
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Gagal!', text: msg });
      }
    });
  });

  // === DELETE ===
  $(document).on('click', '.btnDeleteInfo', function () {
    const id = $(this).data('id');
    const execDelete = () => $.ajax({
      url: "{{ url('dashboard-info') }}/" + id,
      type: 'POST',
      data: { _method: 'DELETE' },
      success: function (res) {
        $('#row-'+id).fadeOut(250, function(){ $(this).remove(); });
        if (window.Swal) Swal.fire({ icon: 'success', title: 'Terhapus!', text: res.message || 'Data berhasil dihapus.', timer: 1500 });
      },
      error: function (err) {
        const msg = err.responseJSON?.message || 'Gagal menghapus data.';
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Gagal!', text: msg });
      }
    });

    if (window.Swal) {
      Swal.fire({
        title: 'Yakin hapus data ini?', text: "Data tidak bisa dikembalikan!",
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#d33', cancelButtonColor: '#aaa',
        confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
      }).then(r => { if (r.isConfirmed) execDelete(); });
    } else {
      if (confirm('Yakin hapus data ini?')) execDelete();
    }
  });

  // === FILTER (client-side, sinkron desktop & mobile) ===
  const syncFilter = () => {
    const q   = ($('#filterQ').val() || $('#filterQ_m').val() || '').toLowerCase();
    const tgt = ($('#filterTarget').val() || $('#filterTarget_m').val() || '').toLowerCase();
    const sts = ($('#filterStatus').val() || $('#filterStatus_m').val() || '');

    $('#tabelInfo tr').each(function () {
      const row = $(this);
      const judul   = row.find('td:eq(1)').text().toLowerCase();
      const konten  = row.find('td:eq(2)').text().toLowerCase();
      const target  = row.find('td:eq(3)').text().toLowerCase();
      const status  = row.find('td:eq(5)').text().toLowerCase().includes('aktif') ? '1' : '0';

      const matchQ   = (q === ''   || judul.includes(q) || konten.includes(q));
      const matchTgt = (tgt === '' || target.includes(tgt));
      const matchSts = (sts === '' || sts === status);

      row.toggle(matchQ && matchTgt && matchSts);
    });
  };

  $('#filterQ, #filterTarget, #filterStatus').on('input change', syncFilter);
  $('#filterQ_m, #filterTarget_m, #filterStatus_m').on('input change', function(){
    if (this.id.endsWith('_m')) {
      const id = this.id.replace('_m','');
      $('#'+id).val($(this).val());
    }
    syncFilter();
  });

  $('#btnResetFilter, #btnResetFilter_m').on('click', function(){
    $('#filterQ, #filterTarget, #filterStatus, #filterQ_m, #filterTarget_m, #filterStatus_m').val('');
    syncFilter();
  });
});
</script>
@endpush
