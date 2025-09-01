@extends('layouts.template')
@section('title', 'Data Jabatan Referensi')

@section('content')
<style>
  :root{
    --bnn-navy:#003366; --bnn-navy-2:#0b2f5e; --bnn-blue:#144272;
    --bnn-gold:#f0ad4e; --bnn-gold-2:#d89a2b;
    --soft:#f5f8fc; --ink:#0f172a; --line:#e6edf6; --muted:#6b7a8c;
  }

  /* Hero Header */
  .bnn-hero{
    background: linear-gradient(135deg, var(--bnn-navy), #012148 60%, var(--bnn-navy-2));
    color:#fff; border-radius:16px; padding:18px 20px; position:relative; overflow: hidden;
    box-shadow: 0 14px 36px rgba(0,33,72,.22);
    z-index:0;
  }
  .bnn-hero::after{
    content:""; position:absolute; right:-60px; top:-60px; width:200px; height:200px; opacity:.08;
    background:url('{{ asset('images/bnn-watermark.svg') }}') no-repeat center/contain;
    pointer-events: none;   /* fix: jangan blok klik */
    z-index:-1;             /* fix: di belakang konten */
  }
  .bnn-hero > *{ position:relative; z-index:1; } /* pastikan konten di atas watermark */
  .bnn-hero h4{ font-weight:800; margin-bottom:2px; }
  .bnn-hero .sub{ color:#dbe7ff; font-size:.95rem; }

  /* Card */
  .bnn-card{ border:1px solid var(--line); border-radius:14px; box-shadow:0 8px 24px rgba(16,24,40,.06); overflow:hidden; }
  .bnn-card .card-header{ background:var(--bnn-navy); color:#fff; font-weight:700; }

  /* Toolbar */
  .btn-bnn{
    background:linear-gradient(135deg, var(--bnn-gold), #ffd777);
    color:#172554; border:0; font-weight:800; border-radius:10px;
    box-shadow:0 6px 16px rgba(244,196,48,.35);
  }
  .btn-bnn:hover{ filter:brightness(1.03); color:#172554; }

  .toolbar-chip{
    border-radius:10px; border:1px solid #dde6f4; background:#fff; padding:.45rem .7rem;
    font-size:.92rem; box-shadow:0 1px 2px rgba(16,24,40,.04);
  }
  .toolbar-chip:focus{ border-color:var(--bnn-gold-2); box-shadow:0 0 0 .18rem rgba(240,173,78,.22); }

  /* Table */
  .table thead th{ background:#0f1f39; color:#eaf2ff; border-color:#0f1f39; font-weight:700; vertical-align:middle; }
  .table td, .table th{ border-color:#e9eef6; }
  .table-hover tbody tr:hover{ background:#fbfdff; }
  .thead-sticky{ position: sticky; top:0; z-index: 2; }
  .table-wrap{ max-height: 70vh; overflow:auto; border-radius: 10px; }
</style>

<div class="container-fluid">

  {{-- ======= Header ======= --}}
  <div class="bnn-hero mb-3 d-flex align-items-center justify-content-between">
    <div>
      <h4><i class="fas fa-briefcase mr-2"></i>Data Jabatan Referensi</h4>
      <div class="sub">Kelola daftar jabatan sebagai acuan penempatan pegawai</div>
    </div>
    <div class="d-flex">
      <button type="button" id="btnTambahJabatan" class="btn btn-bnn btn-sm">
        <i class="fas fa-plus mr-1"></i> Tambah Data
      </button>
    </div>
  </div>

  {{-- ======= Konten ======= --}}
  <div class="card bnn-card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="mb-0"><i class="fas fa-database mr-2"></i> Daftar Jabatan</h5>
      <div class="d-flex" style="gap:.5rem;">
        <input type="text" id="filterNama" class="form-control form-control-sm toolbar-chip" placeholder="Cari nama jabatan…">
        <button type="button" id="btnResetFilter" class="btn btn-outline-secondary btn-sm">
          <i class="fas fa-sync-alt"></i> Reset
        </button>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-wrap">
        <table class="table table-bordered table-hover text-sm m-0" id="tableJabatan">
          <thead class="thead-sticky text-center">
            <tr>
              <th width="6%">No</th>
              <th>Nama Jabatan</th>
              <th width="22%">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($refJabatans as $index => $refJabatan)
            <tr id="row-{{ $refJabatan->id_ref_jabatan }}">
              <td class="text-center align-middle">{{ $index + 1 }}</td>
              <td class="align-middle">
                <span class="font-weight-bold text-primary">{{ $refJabatan->nama_jabatan }}</span>
              </td>
              <td class="text-center align-middle">
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-sm btn-info btnViewJabatan" data-id="{{ $refJabatan->id_ref_jabatan }}" title="Lihat">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-warning btnEditJabatan" data-id="{{ $refJabatan->id_ref_jabatan }}" title="Ubah">
                    <i class="fas fa-pencil-alt"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-danger btnDeleteJabatan" data-id="{{ $refJabatan->id_ref_jabatan }}" title="Hapus">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </div>
              </td>
            </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted p-4">Belum ada data jabatan referensi.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- ======= Modal Tambah / Edit / View ======= --}}
  <div class="modal fade" id="modalFormJabatan" tabindex="-1" aria-labelledby="modalJabatanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" id="modalJabatanContent">
        {{-- Konten form create/edit/view akan dimuat via AJAX --}}
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

  // ===== CREATE =====
  $('#btnTambahJabatan').on('click', function () {
    $('#modalFormJabatan').modal('show');
    $('#modalJabatanContent').load("{{ url('/ref_jabatan/create') }}");
  });

  $(document).on('submit', '#formCreateJabatan', function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    $.ajax({
      url: "{{ url('/ref_jabatan') }}",
      type: "POST",
      data: formData,
      contentType: false, processData: false,
      success: function (res) {
        $('#modalFormJabatan').modal('hide');
        Swal.fire({ icon:'success', title:'Berhasil!', text: res.message, timer:1500, showConfirmButton:false });
        setTimeout(()=>location.reload(), 1200);
      },
      error: function (err) {
        Swal.fire({ icon:'error', title:'Gagal!', text: err.responseJSON?.message || 'Gagal menyimpan data.' });
      }
    });
  });

  // ===== EDIT =====
  $(document).on('click', '.btnEditJabatan', function () {
    const id = $(this).data('id');
    $('#modalFormJabatan').modal('show');
    $('#modalJabatanContent').load(`/ref_jabatan/${id}/edit`);
  });

  $(document).on('submit', '#formEditJabatan', function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    let idJabatan = formData.get('id_ref_jabatan'); // pastikan name field ada di form create/edit
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('_method', 'PUT');
    $.ajax({
      url: `/ref_jabatan/${idJabatan}`,
      type: 'POST',
      data: formData,
      contentType: false, processData: false,
      success: function (res) {
        $('#modalFormJabatan').modal('hide');
        Swal.fire({ icon:'success', title:'Berhasil!', text: res.message, timer:1500, showConfirmButton:false });
        setTimeout(()=>location.reload(), 1200);
      },
      error: function (err) {
        Swal.fire({ icon:'error', title:'Gagal!', text: err.responseJSON?.message || 'Gagal update data.' });
      }
    });
  });

  // ===== VIEW =====
  $(document).on('click', '.btnViewJabatan', function () {
    const id = $(this).data('id');
    $('#modalFormJabatan').modal('show');
    $('#modalJabatanContent').load(`/ref_jabatan/${id}`);
  });

  // ===== DELETE =====
  $(document).on('click', '.btnDeleteJabatan', function () {
    const id = $(this).data('id');
    Swal.fire({
      title:'Yakin hapus data ini?',
      text:'Data tidak bisa dikembalikan!',
      icon:'warning',
      showCancelButton:true,
      confirmButtonColor:'#d33',
      cancelButtonColor:'#aaa',
      confirmButtonText:'Ya, hapus!',
      cancelButtonText:'Batal'
    }).then((result)=>{
      if (result.isConfirmed) {
        $.ajax({
          url: `/ref_jabatan/${id}`,
          type: 'POST',
          data: { _method:'DELETE', _token:$('meta[name="csrf-token"]').attr('content') },
          success: function (res) {
            $('#row-'+id).fadeOut('slow', function(){ $(this).remove(); });
            Swal.fire({ icon:'success', title:'Terhapus!', text: res.message || 'Data berhasil dihapus.', timer:1500 });
          },
          error: function (err) {
            Swal.fire({ icon:'error', title:'Gagal!', text: err.responseJSON?.message || 'Gagal menghapus data.' });
          }
        });
      }
    });
  });

  // ===== FILTER (dengan debounce) =====
  const $filter = $('#filterNama');
  const $rows = $('#tableJabatan tbody tr');
  let t;
  function applyFilter(){
    const q = ($filter.val() || '').toLowerCase();
    $rows.each(function(){
      const textNama = $(this).find('td:eq(1)').text().toLowerCase();
      $(this).toggle(textNama.includes(q));
    });
  }
  $filter.on('input', function(){
    clearTimeout(t); t = setTimeout(applyFilter, 120);
  });
  $('#btnResetFilter').on('click', function(){
    $filter.val(''); applyFilter();
  });
});
</script>
@endpush
