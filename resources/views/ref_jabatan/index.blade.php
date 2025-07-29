@extends('layouts.template')
@section('title', 'Data Jabatan Referensi')

@section('content')
<div class="container-fluid">

    <!-- Tombol Tambah Jabatan -->
    <div class="d-flex justify-content-end mb-3">
        <button id="btnTambahJabatan" class="btn btn-warning text-dark font-weight-bold shadow-sm">
            <i class="fas fa-plus mr-1"></i> Tambah Data
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Data Jabatan Referensi</h5>
        </div>

        <div class="card-body p-3">
            <!-- FILTER BAR -->
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <input type="text" id="filterNama" class="form-control form-control-sm shadow-sm" placeholder="Cari Nama Jabatan">
                </div>
                <div class="col-md-2 mb-2 text-right">
                    <button id="btnResetFilter" class="btn btn-sm btn-outline-secondary w-100 shadow-sm">
                        <i class="fas fa-sync-alt"></i> Reset
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover text-sm m-0" id="tableJabatan">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Jabatan</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($refJabatans as $index => $refJabatan)
                        <tr id="row-{{ $refJabatan->id_ref_jabatan }}">
                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle">{{ $refJabatan->nama_jabatan }}</td>
                            <td class="text-center align-middle">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-info btnViewJabatan" data-id="{{ $refJabatan->id_ref_jabatan }}"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-sm btn-warning btnEditJabatan" data-id="{{ $refJabatan->id_ref_jabatan }}"><i class="fas fa-pencil-alt"></i></button>
                                    <button class="btn btn-sm btn-danger btnDeleteJabatan" data-id="{{ $refJabatan->id_ref_jabatan }}"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Belum ada data jabatan referensi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah / Edit / View Jabatan -->
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
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // === CREATE ===
$('#btnTambahJabatan').click(function () {
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
        contentType: false,
        processData: false,
        success: function (res) {
            $('#modalFormJabatan').modal('hide');
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
$(document).on('click', '.btnEditJabatan', function () {
    const id = $(this).data('id');
    $('#modalFormJabatan').modal('show');
    $('#modalJabatanContent').load(`/ref_jabatan/${id}/edit`);
});

$(document).on('submit', '#formEditJabatan', function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    let idJabatan = formData.get('id_ref_jabatan'); // Pastikan ini sesuai dengan field di form

    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('_method', 'PUT');

    $.ajax({
        url: `/ref_jabatan/${idJabatan}`,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            $('#modalFormJabatan').modal('hide');
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1500, showConfirmButton: false });
            setTimeout(() => location.reload(), 1500);
        },
        error: function (err) {
            console.error(err.responseJSON);
            Swal.fire({ icon: 'error', title: 'Gagal!', text: err.responseJSON?.message || 'Gagal update data.' });
        }
    });
});


    // === VIEW / DETAIL ===
$(document).on('click', '.btnViewJabatan', function () {
    const id = $(this).data('id');
    $('#modalFormJabatan').modal('show');
    $('#modalJabatanContent').load(`/ref_jabatan/${id}`);
});

// === DELETE ===
$(document).on('click', '.btnDeleteJabatan', function () {
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
                url: `/ref_jabatan/${id}`,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content') // Pastikan ini benar
                },
                success: function (res) {
                    $('#row-' + id).fadeOut('slow', function () {
                        $(this).remove();
                    });
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
    $('#filterNama').on('input', function () {
        let nama = $(this).val().toLowerCase();
        $('#tableJabatan tbody tr').each(function () {
            let row = $(this);
            let textNama = row.find('td:eq(1)').text().toLowerCase();
            row.toggle(textNama.includes(nama));
        });
    });

    $('#btnResetFilter').click(function () {
        $('#filterNama').val('');
        $('#filterNama').trigger('input'); // Trigger ulang filter
    });
});
</script>
@endpush