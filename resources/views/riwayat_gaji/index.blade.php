{{-- resources/views/riwayat_gaji/index.blade.php --}}
@extends('layouts.template')
@section('title', 'Data Riwayat Gaji')

@section('content')
<div class="container-fluid">

    @if(auth()->user()->id_level == 1)
    <!-- Tombol Tambah Riwayat Gaji (hanya untuk admin) -->
    <div class="d-flex justify-content-end mb-2">
        <button id="btnTambahRiwayatGaji" class="btn btn-warning text-dark font-weight-bold shadow-sm">
            <i class="fas fa-plus"></i> Tambah data
        </button>
    </div>
    @endif

    <!-- Tabel Riwayat Gaji -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Data Riwayat Gaji</h5>
        </div>

        <div class="card-body">
            <!-- FILTER BAR (hanya untuk admin) -->
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
                            <th>Nama Pegawai</th>
                            <th>Tanggal Berlaku</th>
                            <th>Gaji Pokok</th>
                            <th>Keterangan</th>
                            @if(auth()->user()->id_level == 1)
                            <th>Aksi</th> <!-- Hanya tampil untuk admin -->
                            @endif
                        </tr>
                    </thead>
                    <tbody id="tabelRiwayatGaji">
                        @forelse ($riwayatGajis as $index => $data)
                        <tr id="row-{{ $data->id_riwayat_gaji }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $data->user->nama ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($data->tanggal_berlaku)->format('d-m-Y') }}</td>
                            <td>Rp. {{ number_format($data->gaji_pokok, 0, ',', '.') }}</td>
                            <td>{{ $data->keterangan ?? '-' }}</td>
                            @if(auth()->user()->id_level == 1)
                            <td class="text-center">
                                <button class="btn btn-sm btn-info btnViewRiwayatGaji" data-id="{{ $data->id_riwayat_gaji }}"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-sm btn-warning btnEditRiwayatGaji" data-id="{{ $data->id_riwayat_gaji }}"><i class="fas fa-pencil-alt"></i></button>
                                <button class="btn btn-sm btn-danger btnDeleteRiwayatGaji" data-id="{{ $data->id_riwayat_gaji }}"><i class="fas fa-trash-alt"></i></button>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr><td colspan="{{ auth()->user()->id_level == 1 ? 6 : 5 }}" class="text-center text-muted">Belum ada data riwayat gaji.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah / Edit / View Riwayat Gaji -->
    <div class="modal fade" id="modalFormRiwayatGaji" tabindex="-1" aria-labelledby="modalRiwayatGajiLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="modalRiwayatGajiContent">
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
    $('#btnTambahRiwayatGaji').click(function () {
        $('#modalFormRiwayatGaji').modal('show');
        $('#modalRiwayatGajiContent').load("{{ route('riwayat_gaji.create') }}");
    });

    $(document).on('submit', '#formCreateRiwayatGaji', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: "{{ route('riwayat_gaji.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#modalFormRiwayatGaji').modal('hide');
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
    $(document).on('click', '.btnEditRiwayatGaji', function () {
        const id = $(this).data('id');
        $('#modalFormRiwayatGaji').modal('show');
        $('#modalRiwayatGajiContent').load(`{{ url('/riwayat_gaji') }}/${id}/edit`);
    });

    $(document).on('submit', '#formEditRiwayatGaji', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        let id = $(this).find('input[name="id_riwayat_gaji"]').val() || formData.get('id_riwayat_gaji'); // Asumsi hidden input jika perlu

        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('_method', 'PUT');

        $.ajax({
            url: `{{ url('/riwayat_gaji') }}/${id}`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#modalFormRiwayatGaji').modal('hide');
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
    $(document).on('click', '.btnViewRiwayatGaji', function () {
        const id = $(this).data('id');
        $('#modalFormRiwayatGaji').modal('show');
        $('#modalRiwayatGajiContent').load(`{{ url('/riwayat_gaji') }}/${id}`);
    });

    // === DELETE (hanya jika admin) ===
    @if(auth()->user()->id_level == 1)
    $(document).on('click', '.btnDeleteRiwayatGaji', function () {
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
                    url: `{{ url('/riwayat_gaji') }}/${id}`,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
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

        $('#tabelRiwayatGaji tr').each(function () {
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
