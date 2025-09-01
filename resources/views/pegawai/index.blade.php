@extends('layouts.template')
@section('title', 'Data Pegawai')

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
  .bnn-toolbar{ display:flex; gap:.65rem; align-items:center; justify-content:flex-end; margin: .75rem 0; }
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
  .badge-level{ background:#e7eef9; color:#0a2647; font-weight:700; border-radius:8px; padding:.25rem .5rem; }

  /* Utility */
  .copyable{ cursor:pointer; border-bottom:1px dashed transparent; }
  .copyable:hover{ border-bottom-color:#9fb3d9; }
</style>

<div class="container-fluid">
  {{-- Hero / Header --}}
  <div class="bnn-hero d-flex align-items-center justify-content-between mb-3">
    <div>
      <h4><i class="fas fa-users mr-2"></i> Data Pegawai</h4>
      <div class="sub">Kelola data pegawai dengan cepat dan presisi</div>
    </div>
    <div class="d-flex">
      <button type="button" id="btnTambahPegawai" class="btn btn-bnn shadow-sm">
        <i class="fas fa-user-plus mr-1"></i> Tambah Data
      </button>
    </div>
  </div>

  {{-- Kartu Tabel --}}
  <div class="card bnn-card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <span><i class="fas fa-database mr-2"></i>Daftar Pegawai</span>
      <div class="d-none d-md-flex align-items-center" style="gap:.5rem;">
        <input type="text" id="filterNama" class="form-control form-control-sm filter-chip" placeholder="Cari Nama atau NIP">
        <select id="filterLevel" class="form-control form-control-sm filter-chip" style="min-width:180px">
          <option value="">-- Semua Level --</option>
          @foreach ($levels as $level)
            <option value="{{ $level->level_name }}">{{ $level->level_name }}</option>
          @endforeach
        </select>
        <select id="filterJabatan" class="form-control form-control-sm filter-chip" style="min-width:200px">
          <option value="">-- Semua Jabatan --</option>
          @foreach ($jabatans as $j)
            <option value="{{ $j->nama_jabatan }}">{{ $j->nama_jabatan }}</option>
          @endforeach
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
          <input type="text" id="filterNama_m" class="form-control filter-chip" placeholder="Cari Nama atau NIP">
        </div>
        <div class="col-6 mb-2">
          <select id="filterLevel_m" class="form-control filter-chip">
            <option value="">-- Semua Level --</option>
            @foreach ($levels as $level)
              <option value="{{ $level->level_name }}">{{ $level->level_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-6 mb-2">
          <select id="filterJabatan_m" class="form-control filter-chip">
            <option value="">-- Semua Jabatan --</option>
            @foreach ($jabatans as $j)
              <option value="{{ $j->nama_jabatan }}">{{ $j->nama_jabatan }}</option>
            @endforeach
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
              <th style="width:70px">Foto</th>
              <th>NIP</th>
              <th>Nama</th>
              <th>Jenis Kelamin</th>
              <th>Email</th>
              <th>No. HP</th>
              <th>Level</th>
              <th>Pendidikan</th>
              <th>Jabatan</th>
              <th>Pangkat</th>
              <th style="width:120px">Aksi</th>
            </tr>
          </thead>
          <tbody id="tabelPegawai">
            @forelse ($pegawai as $index => $data)
              <tr id="row-{{ $data->id_user }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">
                  <img src="{{ $data->foto ? asset('storage/' . $data->foto) : asset('images/default.png') }}"
                       class="rounded-circle" width="40" height="40" alt="Foto">
                </td>
                <td class="copyable" title="Klik untuk salin" onclick="copyText('{{ $data->nip }}')">{{ $data->nip }}</td>
                <td>{{ $data->nama }}</td>
                <td>{{ $data->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                <td>{{ $data->email }}</td>
                <td>{{ $data->no_tlp }}</td>
                <td>
                  @if(isset($data->level->level_name))
                    <span class="badge-level">{{ $data->level->level_name }}</span>
                  @else
                    -
                  @endif
                </td>
                <td>{{ $data->pendidikan->jenis_pendidikan ?? '-' }}</td>
                <td>{{ $data->jabatan->refJabatan->nama_jabatan ?? '-' }}</td>
                <td>{{ $data->pangkat->refPangkat->golongan_pangkat ?? '-' }}</td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-info btnViewPegawai" data-id="{{ $data->id_user }}" title="Lihat"><i class="fas fa-eye"></i></button>
                    <button type="button" class="btn btn-warning btnEditPegawai" data-id="{{ $data->id_user }}" title="Ubah"><i class="fas fa-pencil-alt"></i></button>
                    <button type="button" class="btn btn-danger btnDeletePegawai" data-id="{{ $data->id_user }}" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="12" class="text-center text-muted py-4">Belum ada data pegawai.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Modal --}}
  <div class="modal fade" id="modalFormPegawai" tabindex="-1" aria-labelledby="modalPegawaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" id="modalPegawaiContent">
        {{-- konten via AJAX --}}
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function copyText(t){
  if(!t) return;
  const ta = document.createElement('textarea');
  ta.value = t; document.body.appendChild(ta); ta.select();
  try{ document.execCommand('copy'); }catch(e){}
  document.body.removeChild(ta);
}

$(function () {
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

  // === CREATE ===
  $('#btnTambahPegawai').on('click', function () {
    $('#modalFormPegawai').modal('show');
    $('#modalPegawaiContent').load("{{ url('/pegawai/create') }}", function(){
      $('#modalFormPegawai .modal-dialog').removeClass('modal-lg').addClass('modal-xl');
    });
  });

  $(document).on('submit', '#formCreatePegawai', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    $.ajax({
      url: "{{ url('/pegawai') }}",
      type: "POST",
      data: formData, contentType: false, processData: false,
      success: function (res) {
        $('#modalFormPegawai').modal('hide');
        if (window.Swal) Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1500, showConfirmButton: false });
        setTimeout(() => location.reload(), 1200);
      },
      error: function (err) {
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Gagal!', text: err.responseJSON?.message || 'Gagal menyimpan data.' });
      }
    });
  });

  // === EDIT ===
  $(document).on('click', '.btnEditPegawai', function () {
    const id = $(this).data('id');
    $('#modalFormPegawai').modal('show');
    $('#modalPegawaiContent').load(`/pegawai/${id}/edit`);
  });

  $(document).on('submit', '#formEditPegawai', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const idUser = formData.get('id_user');
    formData.append('_method', 'PUT');
    $.ajax({
      url: `/pegawai/${idUser}`, type: 'POST',
      data: formData, contentType: false, processData: false,
      success: function (res) {
        $('#modalFormPegawai').modal('hide');
        if (window.Swal) Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1500, showConfirmButton: false });
        setTimeout(() => location.reload(), 1200);
      },
      error: function (err) {
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Gagal!', text: err.responseJSON?.message || 'Gagal update data.' });
      }
    });
  });

  // === VIEW ===
  $(document).on('click', '.btnViewPegawai', function () {
    const id = $(this).data('id');
    $('#modalFormPegawai').modal('show');
    $('#modalPegawaiContent').load(`/pegawai/${id}`);
  });

  // === DELETE ===
  $(document).on('click', '.btnDeletePegawai', function () {
    const id = $(this).data('id');
    const execDelete = () => $.ajax({
      url: `/pegawai/${id}`, type: 'POST',
      data: { _method: 'DELETE' },
      success: function (res) {
        $('#row-'+id).fadeOut(250, function(){ $(this).remove(); });
        if (window.Swal) Swal.fire({ icon: 'success', title: 'Terhapus!', text: res.message || 'Data berhasil dihapus.', timer: 1500 });
      },
      error: function (err) {
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Gagal!', text: err.responseJSON?.message || 'Gagal menghapus data.' });
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

  // === FILTER desktop & mobile sinkron ===
  const syncFilter = () => {
    const nama = ($('#filterNama').val() || $('#filterNama_m').val() || '').toLowerCase();
    const lvl  = ($('#filterLevel').val() || $('#filterLevel_m').val() || '').toLowerCase();
    const jab  = ($('#filterJabatan').val() || $('#filterJabatan_m').val() || '').toLowerCase();

    $('#tabelPegawai tr').each(function () {
      const row = $(this);
      const textNama    = row.find('td:eq(3)').text().toLowerCase();
      const textNIP     = row.find('td:eq(2)').text().toLowerCase();
      const textLevel   = row.find('td:eq(7)').text().toLowerCase();
      const textJabatan = row.find('td:eq(9)').text().toLowerCase();

      const matchNama = (nama === '' || textNama.includes(nama) || textNIP.includes(nama));
      const matchLevel = (lvl === '' || textLevel.includes(lvl));
      const matchJabatan = (jab === '' || textJabatan.includes(jab));
      row.toggle(matchNama && matchLevel && matchJabatan);
    });
  };

  $('#filterNama, #filterLevel, #filterJabatan').on('input change', syncFilter);
  $('#filterNama_m, #filterLevel_m, #filterJabatan_m').on('input change', function(){
    // mirror ke desktop inputs agar satu sumber kebenaran
    if (this.id.endsWith('_m')) {
      const id = this.id.replace('_m','');
      $('#'+id).val($(this).val());
    }
    syncFilter();
  });

  $('#btnResetFilter, #btnResetFilter_m').on('click', function(){
    $('#filterNama, #filterLevel, #filterJabatan, #filterNama_m, #filterLevel_m, #filterJabatan_m').val('');
    syncFilter();
  });
});
</script>
@endpush
