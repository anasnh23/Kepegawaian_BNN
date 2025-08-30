@extends('layouts.template')
@section('title', 'Data Pendidikan')

@section('content')
<div class="container-fluid">

    @if(auth()->user()->id_level == 1)
    <!-- Tombol Tambah Pendidikan (hanya untuk admin) -->
    <div class="d-flex justify-content-end mb-2">
        <button id="btnTambahPendidikan" class="btn btn-warning text-dark font-weight-bold shadow-sm">
            <i class="fas fa-plus"></i> Tambah data
        </button>
    </div>
    @endif

    <!-- Tabel Pendidikan -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Data Pendidikan</h5>
        </div>

        <div class="card-body">
            <!-- FILTER BAR (hanya untuk admin, karena non-admin data sedikit) -->
            @if(auth()->user()->id_level == 1)
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <input type="text" id="filterNama" class="form-control form-control-sm shadow-sm" placeholder="Cari Nama Pegawai">
                </div>
                <div class="col-md-3 mb-2">
                    <select id="filterJenisPendidikan" class="form-control form-control-sm shadow-sm">
                        <option value="">-- Semua Jenis Pendidikan --</option>
                        <option value="SMA">SMA</option>
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                    </select>
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
                            <th>Jenis Pendidikan</th>
                            <th>Tahun Kelulusan</th>
                            @if(auth()->user()->id_level == 1)
                            <th>Aksi</th> <!-- Hanya tampil untuk admin -->
                            @endif
                        </tr>
                    </thead>
                    <tbody id="tabelPendidikan">
                        @forelse ($pendidikans as $index => $data)
                        <tr id="row-{{ $data->id_pendidikan }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            @if(auth()->user()->id_level == 1)
                            <td>{{ $data->user->nama ?? '-' }}</td>
                            @endif
                            <td>{{ $data->jenis_pendidikan ?? '-' }}</td>
                            <td class="text-center">{{ $data->tahun_kelulusan ?? '-' }}</td>
                            @if(auth()->user()->id_level == 1)
                            <td class="text-center">
                                <button class="btn btn-sm btn-info btnViewPendidikan" data-id="{{ $data->id_pendidikan }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning btnEditPendidikan" data-id="{{ $data->id_pendidikan }}">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btnDeletePendidikan" data-id="{{ $data->id_pendidikan }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->id_level == 1 ? 5 : 3 }}" class="text-center text-muted">
                                Belum ada data pendidikan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah / Edit / View Pendidikan -->
    <div class="modal fade" id="modalFormPendidikan" tabindex="-1" aria-labelledby="modalPendidikanLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="modalPendidikanContent">
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
    $('#btnTambahPendidikan').click(function () {
        $('#modalFormPendidikan').modal('show');
        $('#modalPendidikanContent').load("{{ route('pendidikan.create') }}");
    });

    $(document).on('submit', '#formCreatePendidikan', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: "{{ route('pendidikan.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#modalFormPendidikan').modal('hide');
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
    $(document).on('click', '.btnEditPendidikan', function () {
        const id = $(this).data('id');
        $('#modalFormPendidikan').modal('show');
        $('#modalPendidikanContent').load(`{{ url('/pendidikan') }}/${id}/edit`);
    });

    $(document).on('submit', '#formEditPendidikan', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        let id = $(this).find('input[name="id_pendidikan"]').val() || formData.get('id_pendidikan');

        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('_method', 'PUT');

        $.ajax({
            url: `{{ url('/pendidikan') }}/${id}`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#modalFormPendidikan').modal('hide');
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
    $(document).on('click', '.btnViewPendidikan', function () {
        const id = $(this).data('id');
        $('#modalFormPendidikan').modal('show');
        $('#modalPendidikanContent').load(`{{ url('/pendidikan') }}/${id}`);
    });

    // === DELETE (hanya jika admin) ===
    @if(auth()->user()->id_level == 1)
    $(document).on('click', '.btnDeletePendidikan', function () {
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
                    url: `{{ url('/pendidikan') }}/${id}`,
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
    $('#filterNama, #filterJenisPendidikan').on('input change', function () {
        let nama = $('#filterNama').val().toLowerCase();
        let jenisPendidikan = $('#filterJenisPendidikan').val();

        $('#tabelPendidikan tr').each(function () {
            let row = $(this);
            let textNama = row.find('td:eq(1)').text().toLowerCase(); // Kolom nama pegawai
            let textJenis = row.find('td:eq(2)').text(); // Kolom jenis pendidikan

            let matchNama = nama === '' || textNama.includes(nama);
            let matchJenis = jenisPendidikan === '' || textJenis === jenisPendidikan;

            row.toggle(matchNama && matchJenis);
        });
    });

    $('#btnResetFilter').click(function () {
        $('#filterNama').val('');
        $('#filterJenisPendidikan').val('');
        $('#filterNama').trigger('input'); // Trigger ulang filter
    });
    @endif

});
</script>
@endpush