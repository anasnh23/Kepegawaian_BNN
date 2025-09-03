@extends('layouts.template')
@section('title', 'Data Pangkat')

@section('content')
<div class="container-fluid">

    @if(auth()->user()->id_level == 1)
    <!-- Tombol Tambah Pangkat (hanya untuk admin) -->
    <div class="d-flex justify-content-end mb-2">
        <button id="btnTambahPangkat" class="btn btn-warning text-dark font-weight-bold shadow-sm">
            <i class="fas fa-plus"></i> Tambah data
        </button>
    </div>
    @endif

    <!-- Tabel Pangkat -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Data Pangkat</h5>
        </div>

        <div class="card-body">
            <!-- FILTER BAR (hanya untuk admin, karena non-admin data sedikit) -->
            @if(auth()->user()->id_level == 1)
            <div class="row mb-3">
                <div class="col-md-3 mb-2">
                    <input type="text" id="filterNama" class="form-control form-control-sm shadow-sm" placeholder="Cari Nama Pegawai">
                </div>
                <div class="col-md-3 mb-2">
                    <input type="text" id="filterGolongan" class="form-control form-control-sm shadow-sm" placeholder="Cari Golongan Pangkat">
                </div>
                <div class="col-md-3 mb-2">
                    <input type="text" id="filterNamaJabatan" class="form-control form-control-sm shadow-sm" placeholder="Cari Nama Jabatan">
                </div>
                <div class="col-md-2 mb-2">
                    <button id="btnResetFilter" class="btn btn-sm btn-outline-secondary w-100 shadow-sm">
                        <i class="fas fa-sync-alt"></i> Reset
                    </button>
                </div>
            </div>
            @endif

            <!-- TABEL -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-sm m-0">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th>No</th>
                            @if(auth()->user()->id_level == 1)
                            <th>Nama Pegawai</th> <!-- Hanya tampil untuk admin -->
                            @endif
                            <th>Golongan Pangkat</th>
                            <th>Nama Jabatan</th> <!-- Kolom baru -->
                            @if(auth()->user()->id_level == 1)
                            <th>Aksi</th> <!-- Hanya tampil untuk admin -->
                            @endif
                        </tr>
                    </thead>
                    <tbody id="tabelPangkat">
                        @forelse ($pangkats as $index => $data)
                        <tr id="row-{{ $data->id_pangkat }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            @if(auth()->user()->id_level == 1)
                            <td>{{ $data->user->nama ?? '-' }}</td>
                            @endif
                            <td class="text-center">{{ $data->refPangkat->golongan_pangkat ?? '-' }}</td> <!-- Mengambil dari tabel ref_golongan_pangkat kolom golongan_pangkat via relasi refPangkat -->
                            <td class="text-center">{{ $data->jabatanModel->refJabatan->nama_jabatan ?? '-' }}</td> <!-- Kolom baru: Dari jabatan (id_ref_jabatan) ke ref_jabatan (nama_jabatan) via relasi nested -->
                            @if(auth()->user()->id_level == 1)
                            <td class="text-center">
                                <button class="btn btn-sm btn-info btnViewPangkat" data-id="{{ $data->id_pangkat }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning btnEditPangkat" data-id="{{ $data->id_pangkat }}">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btnDeletePangkat" data-id="{{ $data->id_pangkat }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->id_level == 1 ? 5 : 3 }}" class="text-center text-muted">
                                Belum ada data pangkat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah / Edit / View Pangkat -->
    <div class="modal fade" id="modalFormPangkat" tabindex="-1" aria-labelledby="modalPangkatLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="modalPangkatContent">
                {{-- Konten form create/edit/view akan dimuat via AJAX --}}
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // === CREATE (hanya jika admin) ===
    @if(auth()->user()->id_level == 1)
    $('#btnTambahPangkat').click(function () {
        $('#modalFormPangkat').modal('show');
        $('#modalPangkatContent').load("{{ route('pangkat.create') }}");
    });

    $(document).on('submit', '#formCreatePangkat', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: "{{ route('pangkat.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#modalFormPangkat').modal('hide');
                Swal.fire({ 
                    icon: 'success', 
                    title: 'Berhasil!', 
                    text: res.success, 
                    timer: 1500, 
                    showConfirmButton: false 
                });
                setTimeout(() => location.reload(), 1500);
            },
            error: function (err) {
                console.error(err.responseJSON);
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Gagal!', 
                    text: err.responseJSON?.message || 'Gagal menyimpan data.' 
                });
            }
        });
    });
    @endif

    // === EDIT (hanya jika admin) ===
    @if(auth()->user()->id_level == 1)
    $(document).on('click', '.btnEditPangkat', function () {
        const id = $(this).data('id');
        $('#modalFormPangkat').modal('show');
        $('#modalPangkatContent').load(`{{ url('/pangkat') }}/${id}/edit`);
    });

    $(document).on('submit', '#formEditPangkat', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        let id = $(this).find('input[name="id_pangkat"]').val() || formData.get('id_pangkat');

        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('_method', 'PUT');

        $.ajax({
            url: `{{ url('/pangkat') }}/${id}`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#modalFormPangkat').modal('hide');
                Swal.fire({ 
                    icon: 'success', 
                    title: 'Berhasil!', 
                    text: res.success, 
                    timer: 1500, 
                    showConfirmButton: false 
                });
                setTimeout(() => location.reload(), 1500);
            },
            error: function (err) {
                console.error(err.responseJSON);
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Gagal!', 
                    text: err.responseJSON?.message || 'Gagal update data.' 
                });
            }
        });
    });
    @endif

    // === VIEW / DETAIL ===
    $(document).on('click', '.btnViewPangkat', function () {
        const id = $(this).data('id');
        $('#modalFormPangkat').modal('show');
        $('#modalPangkatContent').load(`{{ url('/pangkat') }}/${id}`);
    });

    // === DELETE (hanya jika admin) ===
    @if(auth()->user()->id_level == 1)
    $(document).on('click', '.btnDeletePangkat', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Yakin hapus data ini?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('/pangkat') }}/${id}`,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        $('#row-' + id).fadeOut('slow', function () {
                            $(this).remove();
                        });
                        Swal.fire({ 
                            icon: 'success', 
                            title: 'Terhapus!', 
                            text: res.success || 'Data berhasil dihapus.', 
                            timer: 1500 
                        });
                    },
                    error: function (err) {
                        console.error(err.responseJSON);
                        Swal.fire({ 
                            icon: 'error', 
                            title: 'Gagal!', 
                            text: err.responseJSON?.message || 'Gagal menghapus data.' 
                        });
                    }
                });
            }
        });
    });
    @endif

    // === FILTER (hanya jika admin) ===
    @if(auth()->user()->id_level == 1)
    $('#filterNama, #filterGolongan, #filterNamaJabatan').on('input change', function () {
        let nama = $('#filterNama').val().toLowerCase();
        let golongan = $('#filterGolongan').val().toLowerCase();
        let namaJabatan = $('#filterNamaJabatan').val().toLowerCase();

        $('#tabelPangkat tr').each(function () {
            let row = $(this);
            let textNama = row.find('td:eq(1)').text().toLowerCase(); // Kolom nama pegawai
            let textGolongan = row.find('td:eq(2)').text().toLowerCase(); // Kolom golongan pangkat
            let textNamaJabatan = row.find('td:eq(3)').text().toLowerCase(); // Kolom nama jabatan (indeks 3 untuk admin)

            let matchNama = nama === '' || textNama.includes(nama);
            let matchGolongan = golongan === '' || textGolongan.includes(golongan);
            let matchNamaJabatan = namaJabatan === '' || textNamaJabatan.includes(namaJabatan);

            row.toggle(matchNama && matchGolongan && matchNamaJabatan);
        });
    });

    $('#btnResetFilter').click(function () {
        $('#filterNama').val('');
        $('#filterGolongan').val('');
        $('#filterNamaJabatan').val('');
        $('#filterNama').trigger('input'); // Trigger ulang filter
    });
    @endif

});
</script>
@endpush
