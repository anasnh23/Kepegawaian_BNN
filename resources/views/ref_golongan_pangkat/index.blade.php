@extends('layouts.template')
@section('title', 'Data Referensi Golongan Pangkat')

@section('content')
<div class="container-fluid">

    @if(auth()->user()->id_level == 1)
    <!-- Tombol Tambah Referensi Golongan Pangkat (hanya untuk admin) -->
    <div class="d-flex justify-content-end mb-2">
        <button id="btnTambahRefGolonganPangkat" class="btn btn-warning text-dark font-weight-bold shadow-sm">
            <i class="fas fa-plus"></i> Tambah data
        </button>
    </div>
    @endif

    <!-- Tabel Referensi Golongan Pangkat -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Data Referensi Golongan Pangkat</h5>
        </div>

        <div class="card-body">
            <!-- FILTER BAR (hanya untuk admin, karena non-admin data sedikit) -->
            @if(auth()->user()->id_level == 1)
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <input type="text" id="filterGolonganPangkat" class="form-control form-control-sm shadow-sm" placeholder="Cari Golongan Pangkat">
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
                            <th>Golongan Pangkat</th>
                            <th>Gaji Pokok</th>
                            <th>Masa Kerja Min</th>
                            <th>Masa Kerja Maks</th>
                            <th>Keterangan</th>
                            @if(auth()->user()->id_level == 1)
                            <th>Aksi</th> <!-- Hanya tampil untuk admin -->
                            @endif
                        </tr>
                    </thead>
                    <tbody id="tabelRefGolonganPangkat">
                        @forelse ($refGolonganPangkats as $index => $data)
                        <tr id="row-{{ $data->id_ref_pangkat }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $data->golongan_pangkat ?? '-' }}</td>
                            <td class="text-right">{{ number_format($data->gaji_pokok, 2) ?? '-' }}</td>
                            <td class="text-center">{{ $data->masa_kerja_min ?? '-' }}</td>
                            <td class="text-center">{{ $data->masa_kerja_maks ?? '-' }}</td>
                            <td>{{ $data->keterangan ?? '-' }}</td>
                            @if(auth()->user()->id_level == 1)
                            <td class="text-center">
                                <button class="btn btn-sm btn-info btnViewRefGolonganPangkat" data-id="{{ $data->id_ref_pangkat }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning btnEditRefGolonganPangkat" data-id="{{ $data->id_ref_pangkat }}">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btnDeleteRefGolonganPangkat" data-id="{{ $data->id_ref_pangkat }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->id_level == 1 ? 7 : 6 }}" class="text-center text-muted">
                                Belum ada data referensi golongan pangkat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah / Edit / View Referensi Golongan Pangkat -->
    <div class="modal fade" id="modalFormRefGolonganPangkat" tabindex="-1" aria-labelledby="modalRefGolonganPangkatLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="modalRefGolonganPangkatContent">
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
    $('#btnTambahRefGolonganPangkat').click(function () {
        $('#modalFormRefGolonganPangkat').modal('show');
        $('#modalRefGolonganPangkatContent').load("{{ route('ref_golongan_pangkat.create') }}");
    });

    $(document).on('submit', '#formCreateRefGolonganPangkat', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: "{{ route('ref_golongan_pangkat.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#modalFormRefGolonganPangkat').modal('hide');
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
    $(document).on('click', '.btnEditRefGolonganPangkat', function () {
        const id = $(this).data('id');
        $('#modalFormRefGolonganPangkat').modal('show');
        $('#modalRefGolonganPangkatContent').load("{{ url('/ref_golongan_pangkat') }}/" + id + "/edit");
    });

    $(document).on('submit', '#formEditRefGolonganPangkat', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        let id = $(this).find('input[name="id_ref_pangkat"]').val() || formData.get('id_ref_pangkat');

        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('_method', 'PUT');

        $.ajax({
            url: "{{ url('/ref_golongan_pangkat') }}/" + id,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#modalFormRefGolonganPangkat').modal('hide');
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
    $(document).on('click', '.btnViewRefGolonganPangkat', function () {
        const id = $(this).data('id');
        $('#modalFormRefGolonganPangkat').modal('show');
        $('#modalRefGolonganPangkatContent').load("{{ url('/ref_golongan_pangkat') }}/" + id);
    });

    // === DELETE (hanya jika admin) ===
    @if(auth()->user()->id_level == 1)
    $(document).on('click', '.btnDeleteRefGolonganPangkat', function () {
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
                    url: "{{ url('/ref_golongan_pangkat') }}/" + id,
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
    $('#filterGolonganPangkat').on('input', function () {
        let golongan = $('#filterGolonganPangkat').val().toLowerCase();

        $('#tabelRefGolonganPangkat tr').each(function () {
            let row = $(this);
            let textGolongan = row.find('td:eq(1)').text().toLowerCase(); // Kolom golongan pangkat

            let matchGolongan = golongan === '' || textGolongan.includes(golongan);

            row.toggle(matchGolongan);
        });
    });

    $('#btnResetFilter').click(function () {
        $('#filterGolonganPangkat').val('');
        $('#filterGolonganPangkat').trigger('input'); // Trigger ulang filter
    });
    @endif

});
</script>
@endpush
