@extends('layouts.template')
@section('title', $breadcrumb->title)

@section('content')
<div class="container-fluid">

    @if(auth()->user()->id_level == 1)
    <!-- Tombol Tambah Riwayat KGP (hanya untuk admin) -->
    <div class="d-flex justify-content-end mb-2">
        <button id="btnTambahKgp" class="btn btn-warning text-dark font-weight-bold shadow-sm">
            <i class="fas fa-plus"></i> Tambah data
        </button>
    </div>
    @endif

    <!-- Tabel Riwayat KGP -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ $breadcrumb->title }}</h5>
        </div>

        <div class="card-body">
            <!-- FILTER BAR (hanya untuk admin, karena non-admin data sedikit) -->
            @if(auth()->user()->id_level == 1)
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <input type="text" id="filterNama" class="form-control form-control-sm shadow-sm" placeholder="Cari Nama Pegawai">
                </div>
                <div class="col-md-2 mb-2 text-right">
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
                            <th>Tahun KGP</th>
                            <th>TMT</th>
                            <th>Aksi</th> <!-- Selalu tampil, tapi isi conditional -->
                        </tr>
                    </thead>
                    <tbody id="tabelKgp">
                        @forelse ($kgps as $index => $data)
                        <tr id="row-{{ $data->id_kgp }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            @if(auth()->user()->id_level == 1)
                            <td>{{ $data->pegawai->nama ?? '-' }}</td>
                            @endif
                            <td>{{ $data->tahun_kgp ?? '-' }}</td>
                            <td>{{ $data->tmt ?? '-' }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-info btnViewKgp" data-id="{{ $data->id_kgp }}"><i class="fas fa-eye"></i></button>
                                @if(auth()->user()->id_level == 1)
                                <button class="btn btn-sm btn-warning btnEditKgp" data-id="{{ $data->id_kgp }}"><i class="fas fa-pencil-alt"></i></button>
                                <button class="btn btn-sm btn-danger btnDeleteKgp" data-id="{{ $data->id_kgp }}"><i class="fas fa-trash-alt"></i></button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="{{ auth()->user()->id_level == 1 ? 5 : 4 }}" class="text-center text-muted">Belum ada data riwayat KGP.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah / Edit / View Riwayat KGP -->
    <div class="modal fade" id="modalFormKgp" tabindex="-1" aria-labelledby="modalKgpLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="modalKgpContent">
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
    $('#btnTambahKgp').click(function () {
        $('#modalFormKgp').modal('show');
        $('#modalKgpContent').load("{{ route('kgp.create') }}");
    });

    $(document).on('submit', '#formCreateKgp', function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('kgp.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#modalFormKgp').modal('hide');
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.success, timer: 1500, showConfirmButton: false });
                setTimeout(() => location.reload(), 1500);
            },
            error: function (err) {
                console.error(err.responseJSON);
                Swal.fire({ icon: 'error', title: 'Gagal!', text: err.responseJSON?.message || 'Gagal menyimpan data.' });
            }
        });
    });
    @endif

    // === EDIT (hanya jika admin) ===
    @if(auth()->user()->id_level == 1)
    $(document).on('click', '.btnEditKgp', function () {
        const id = $(this).data('id');
        $('#modalFormKgp').modal('show');
        const editUrl = "{{ route('kgp.edit', ':id') }}".replace(':id', id);
        $('#modalKgpContent').load(editUrl);
    });

    $(document).on('submit', '#formEditKgp', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        let id = formData.get('id_kgp');

        // Tambahkan _method: 'PUT' jika tidak terdeteksi dari form
        if (!formData.has('_method')) {
            formData.append('_method', 'PUT');
        }

        const updateUrl = "{{ route('kgp.update', ':id') }}".replace(':id', id);

        $.ajax({
            url: updateUrl,
            type: "POST", // Gunakan POST dengan _method: 'PUT'
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#modalFormKgp').modal('hide');
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.success, timer: 1500, showConfirmButton: false });
                setTimeout(() => location.reload(), 1500);
            },
            error: function (err) {
                console.error(err.responseJSON);
                Swal.fire({ icon: 'error', title: 'Gagal!', text: err.responseJSON?.message || 'Gagal update data.' });
            }
        });
    });
    @endif

    // === VIEW / DETAIL ===
    $(document).on('click', '.btnViewKgp', function () {
        const id = $(this).data('id');
        $('#modalFormKgp').modal('show');
        const showUrl = "{{ route('kgp.show', ':id') }}".replace(':id', id);
        $('#modalKgpContent').load(showUrl);
    });

    // === DELETE (hanya jika admin) ===
    @if(auth()->user()->id_level == 1)
    $(document).on('click', '.btnDeleteKgp', function () {
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
                const destroyUrl = "{{ route('kgp.destroy', ':id') }}".replace(':id', id);
                $.ajax({
                    url: destroyUrl,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        $('#row-' + id).fadeOut('slow', function () {
                            $(this).remove();
                        });
                        Swal.fire({ icon: 'success', title: 'Terhapus!', text: res.success || 'Data berhasil dihapus.', timer: 1500 });
                    },
                    error: function (err) {
                        console.error(err.responseJSON);
                        Swal.fire({ icon: 'error', title: 'Gagal!', text: err.responseJSON?.message || 'Gagal menghapus data.' });
                    }
                });
            }
        });
    });
    @endif

    // === FILTER (hanya jika admin) ===
    @if(auth()->user()->id_level == 1)
    $('#filterNama').on('input', function () {
        let nama = $(this).val().toLowerCase();

        $('#tabelKgp tr').each(function () {
            let row = $(this);
            let textNama = row.find('td:eq(1)').text().toLowerCase();

            let matchNama = nama === '' || textNama.includes(nama);

            row.toggle(matchNama);
        });
    });

    $('#btnResetFilter').click(function () {
        $('#filterNama').val('');
        $('#filterNama').trigger('input'); // Trigger ulang filter
    });
    @endif

});
</script>
@endpush