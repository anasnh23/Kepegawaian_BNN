@extends('layouts.template')
@section('title', 'Data Pegawai')

@section('content')
<div class="container-fluid">

  {{-- ====== BNN THEME (khusus halaman ini) ====== --}}
  <style>
    :root{
      --bnn-navy:#0a2647; --bnn-blue:#144272; --bnn-cyan:#2c74b3; --bnn-gold:#f4c430;
      --ink:#0f172a; --muted:#6b7a8c; --soft:#eef3fb;
    }
    .bnn-card{ border:1px solid #e6edf6; box-shadow:0 8px 24px rgba(16,24,40,.06); border-radius:14px; overflow:hidden; }
    .bnn-card .card-header{
      background: linear-gradient(135deg, var(--bnn-navy), var(--bnn-blue));
      color:#fff; border:0; padding:14px 18px;
    }
    .bnn-toolbar{
      display:flex; gap:.65rem; align-items:center; justify-content:flex-end; margin-bottom:.75rem;
    }
    .btn-bnn{
      background:linear-gradient(135deg, var(--bnn-gold), #ffd666);
      color:#172554; border:0; font-weight:800; border-radius:10px;
      box-shadow:0 6px 16px rgba(244,196,48,.35);
    }
    .btn-bnn:hover{ filter:brightness(1.03); }
    .filter-chip{
      border-radius:10px; border:1px solid #dde6f4; background:#fff;
      padding:.45rem .7rem; font-size:.9rem; box-shadow:0 1px 2px rgba(16,24,40,.04);
    }
    .filter-chip:focus{ border-color:var(--bnn-cyan); box-shadow:0 0 0 .18rem rgba(44,116,179,.18); }
    .thead-bnn th{
      background:#0f1f39; color:#eaf2ff; border-color:#0f1f39; font-weight:700; vertical-align:middle;
    }
    table.table tbody td{ vertical-align:middle; }
    .badge-level{ background:#e7eef9; color:#0a2647; font-weight:700; border-radius:8px; padding:.25rem .5rem; }
    .table thead th, .table td{ border-color:#e7eef3; }
  </style>

  <!-- Toolbar Tambah -->
  <div class="bnn-toolbar">
    <button id="btnTambahPegawai" class="btn btn-bnn shadow-sm">
      <i class="fas fa-user-plus mr-1"></i> Tambah data
    </button>
  </div>

  <!-- Kartu Tabel Pegawai -->
  <div class="card bnn-card">
    <div class="card-header d-flex align-items-center">
      <h5 class="mb-0"><i class="fas fa-users mr-2"></i> Data Pegawai</h5>
    </div>

    <div class="card-body">

      <!-- FILTER BAR -->
      <div class="row mb-3">
        <div class="col-md-4 mb-2">
          <input type="text" id="filterNama" class="form-control filter-chip" placeholder="Cari Nama atau NIP">
        </div>
        <div class="col-md-3 mb-2">
          <select id="filterLevel" class="form-control filter-chip">
            <option value="">-- Semua Level --</option>
            @foreach ($levels as $level)
              <option value="{{ $level->level_name }}">{{ $level->level_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3 mb-2">
          <select id="filterJabatan" class="form-control filter-chip">
            <option value="">-- Semua Jabatan --</option>
            @foreach ($jabatans as $j)
              <option value="{{ $j->nama_jabatan }}">{{ $j->nama_jabatan }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2 mb-2 text-right">
          <button id="btnResetFilter" class="btn btn-outline-secondary w-100 shadow-sm" style="border-radius:10px;">
            <i class="fas fa-sync-alt"></i> Reset
          </button>
        </div>
      </div>

      <!-- TABEL -->
      <div class="table-responsive">
        <table class="table table-hover text-sm m-0">
          <thead class="thead-bnn text-center">
            <tr>
              <th>No</th>
              <th>Foto</th>
              <th>NIP</th>
              <th>Nama</th>
              <th>Jenis Kelamin</th>
              <th>Email</th>
              <th>No. HP</th>
              <th>Level</th>
              <th>Pendidikan</th>
              <th>Jabatan</th>
              <th>Pangkat</th>
              <th>Aksi</th>
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
                <td>{{ $data->nip }}</td>
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
                  <button class="btn btn-sm btn-info btnViewPegawai" data-id="{{ $data->id_user }}"><i class="fas fa-eye"></i></button>
                  <button class="btn btn-sm btn-warning btnEditPegawai" data-id="{{ $data->id_user }}"><i class="fas fa-pencil-alt"></i></button>
                  <button class="btn btn-sm btn-danger btnDeletePegawai" data-id="{{ $data->id_user }}"><i class="fas fa-trash-alt"></i></button>
                </td>
              </tr>
            @empty
              <tr><td colspan="12" class="text-center text-muted">Belum ada data pegawai.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Tambah / Edit / View Pegawai -->
  <div class="modal fade" id="modalFormPegawai" tabindex="-1" aria-labelledby="modalPegawaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" id="modalPegawaiContent">
        {{-- Konten form create/edit/view akan dimuat via AJAX --}}
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

  // === CREATE ===
  $('#btnTambahPegawai').click(function () {
    $('#modalFormPegawai').modal('show');
    $('#modalPegawaiContent').load("{{ url('/pegawai/create') }}", function(){
      // besarkan modal agar nyaman (sinkron dgn create.blade)
      $('#modalFormPegawai .modal-dialog').removeClass('modal-lg').addClass('modal-xl');
    });
  });

  $(document).on('submit', '#formCreatePegawai', function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    $.ajax({
      url: "{{ url('/pegawai') }}",
      type: "POST",
      data: formData, contentType: false, processData: false,
      success: function (res) {
        $('#modalFormPegawai').modal('hide');
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1500, showConfirmButton: false });
        setTimeout(() => location.reload(), 1500);
      },
      error: function (err) {
        console.error(err.responseJSON);
        Swal.fire({ icon: 'error', title: 'Gagal!', text: err.responseJSON?.message || 'Gagal menyimpan data.' });
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
    let formData = new FormData(this);
    let idUser = formData.get('id_user');
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('_method', 'PUT');
    $.ajax({
      url: `/pegawai/${idUser}`, type: 'POST',
      data: formData, contentType: false, processData: false,
      success: function (res) {
        $('#modalFormPegawai').modal('hide');
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1500, showConfirmButton: false });
        setTimeout(() => location.reload(), 1500);
      },
      error: function (err) {
        console.error(err.responseJSON);
        Swal.fire({ icon: 'error', title: 'Gagal!', text: err.responseJSON?.message || 'Gagal update data.' });
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
    Swal.fire({
      title: 'Yakin hapus data ini?', text: "Data tidak bisa dikembalikan!",
      icon: 'warning', showCancelButton: true,
      confirmButtonColor: '#d33', cancelButtonColor: '#aaa',
      confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `/pegawai/${id}`, type: 'POST',
          data: { _method: 'DELETE', _token: $('meta[name="csrf-token"]').attr('content') },
          success: function (res) {
            $('#row-' + id).fadeOut('slow', function () { $(this).remove(); });
            Swal.fire({ icon: 'success', title: 'Terhapus!', text: res.message || 'Data berhasil dihapus.', timer: 1500 });
          },
          error: function (err) {
            console.error(err.responseJSON);
            Swal.fire({ icon: 'error', title: 'Gagal!', text: err.responseJSON?.message || 'Gagal menghapus data.' });
          }
        });
      }
    });
  });

  // === FILTER ===
  $('#filterNama, #filterLevel, #filterJabatan').on('input change', function () {
    let nama = $('#filterNama').val().toLowerCase();
    let level = $('#filterLevel').val().toLowerCase();
    let jabatan = $('#filterJabatan').val().toLowerCase();
    $('#tabelPegawai tr').each(function () {
      let row = $(this);
      let textNama = row.find('td:eq(3)').text().toLowerCase();
      let textNIP = row.find('td:eq(2)').text().toLowerCase();
      let textLevel = row.find('td:eq(7)').text().toLowerCase();
      let textJabatan = row.find('td:eq(9)').text().toLowerCase();
      let matchNama = (nama === '' || textNama.includes(nama) || textNIP.includes(nama));
      let matchLevel = (level === '' || textLevel.includes(level));
      let matchJabatan = (jabatan === '' || textJabatan.includes(jabatan));
      row.toggle(matchNama && matchLevel && matchJabatan);
    });
  });

  $('#btnResetFilter').click(function () {
    $('#filterNama').val(''); $('#filterLevel').val(''); $('#filterJabatan').val('');
    $('#filterNama').trigger('input');
  });
});
</script>
@endpush
