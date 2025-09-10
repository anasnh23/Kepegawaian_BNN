@extends('layouts.template')
@section('title', 'Data Riwayat Jabatan')

@section('content')
<div class="container-fluid">

    @if(auth()->user()->id_level == 1)
    <!-- Tombol Tambah Riwayat Jabatan (hanya untuk admin) -->
    <div class="d-flex justify-content-end mb-2">
        <button id="btnTambahRiwayatJabatan" class="btn btn-warning text-dark font-weight-bold shadow-sm">
            <i class="fas fa-plus"></i> Tambah data
        </button>
    </div>
    @endif

    <!-- Tabel Riwayat Jabatan -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Data Riwayat Jabatan</h5>
        </div>

        <div class="card-body">
            <!-- FILTER BAR (hanya untuk admin) -->
            @if(auth()->user()->id_level == 1)
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <input type="text" id="filterNama" class="form-control form-control-sm shadow-sm" placeholder="Cari Nama Pegawai">
                </div>
                <div class="col-md-4 mb-2">
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
                            <th>Nama Jabatan</th>
                            <th>TMT Mulai</th>
                            <th>TMT Selesai</th>
                            <th>Keterangan</th>
                            @if(auth()->user()->id_level == 1)
                            <th>Aksi</th> <!-- Hanya tampil untuk admin -->
                            @endif
                        </tr>
                    </thead>
                    <tbody id="tabelRiwayatJabatan">
                        @forelse ($riwayatJabatans as $index => $data)
                        <tr id="row-{{ $data->id_riwayat_jabatan }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            @if(auth()->user()->id_level == 1)
                            <td>{{ $data->user->nama ?? '-' }}</td>
                            @endif
                            <td>{{ $data->nama_jabatan ?? '-' }}</td>
                            <td class="text-center">{{ $data->tmt_mulai ?? '-' }}</td>
                            <td class="text-center">{{ $data->tmt_selesai ?? '-' }}</td>
                            <td>{{ $data->keterangan ?? '-' }}</td>
                            @if(auth()->user()->id_level == 1)
                            <td class="text-center">
                                <button class="btn btn-sm btn-info btnViewRiwayatJabatan" data-id="{{ $data->id_riwayat_jabatan }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning btnEditRiwayatJabatan" data-id="{{ $data->id_riwayat_jabatan }}">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btnDeleteRiwayatJabatan" data-id="{{ $data->id_riwayat_jabatan }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->id_level == 1 ? 7 : 5 }}" class="text-center text-muted">
                                Belum ada data riwayat jabatan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah / Edit / View Riwayat Jabatan -->
    <div class="modal fade" id="modalFormRiwayatJabatan" tabindex="-1" aria-labelledby="modalRiwayatJabatanLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="modalRiwayatJabatanContent">
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

    // === CREATE (hanya admin) ===
    @if(auth()->user()->id_level == 1)
    $('#btnTambahRiwayatJabatan').click(function () {
        $('#modalFormRiwayatJabatan').modal('show');
        $('#modalRiwayatJabatanContent').load("{{ route('riwayat_jabatan.create') }}");
    });

    $(document).on('submit', '#formCreateRiwayatJabatan', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: "{{ route('riwayat_jabatan.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#modalFormRiwayatJabatan').modal('hide');
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

    // === EDIT (hanya admin) ===
    @if(auth()->user()->id_level == 1)
    $(document).on('click', '.btnEditRiwayatJabatan', function () {
        const id = $(this).data('id');
        $('#modalFormRiwayatJabatan').modal('show');
        $('#modalRiwayatJabatanContent').load(`{{ url('/riwayat_jabatan') }}/${id}/edit`);
    });

    $(document).on('submit', '#formEditRiwayatJabatan', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        let id = $(this).find('input[name="id_riwayat_jabatan"]').val() || formData.get('id_riwayat_jabatan');

        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('_method', 'PUT');

        $.ajax({
            url: `{{ url('/riwayat_jabatan') }}/${id}`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#modalFormRiwayatJabatan').modal('hide');
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
    $(document).on('click', '.btnViewRiwayatJabatan', function () {
        const id = $(this).data('id');
        $('#modalFormRiwayatJabatan').modal('show');
        $('#modalRiwayatJabatanContent').load(`{{ url('/riwayat_jabatan') }}/${id}`);
    });

    // === DELETE (hanya admin) ===
    @if(auth()->user()->id_level == 1)
    $(document).on('click', '.btnDeleteRiwayatJabatan', function () {
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
                    url: `{{ url('/riwayat_jabatan') }}/${id}`,
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

    // === FILTER (hanya admin) ===
    @if(auth()->user()->id_level == 1)
    $('#filterNama, #filterNamaJabatan').on('input', function () {
        let nama = $('#filterNama').val().toLowerCase();
        let namaJabatan = $('#filterNamaJabatan').val().toLowerCase();

        $('#tabelRiwayatJabatan tr').each(function () {
            let row = $(this);
            let textNama = row.find('td:eq(1)').text().toLowerCase(); // Kolom nama pegawai
            let textJabatan = row.find('td:eq(2)').text().toLowerCase(); // Kolom nama jabatan

            let matchNama = nama === '' || textNama.includes(nama);
            let matchJabatan = namaJabatan === '' || textJabatan.includes(namaJabatan);

            row.toggle(matchNama && matchJabatan);
        });
    });

    $('#btnResetFilter').click(function () {
        $('#filterNama').val('');
        $('#filterNamaJabatan').val('');
        $('#filterNama').trigger('input'); // Trigger ulang filter
    });
    @endif

});
</script>
@endpush