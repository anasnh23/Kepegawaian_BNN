@extends('layouts.template')
@section('title', 'Data Riwayat Gaji')

@section('content')
<div class="container-fluid">

    @if(auth()->user()->id_level == 1)
    <!-- Tombol Tambah (Admin saja) -->
    <div class="d-flex justify-content-end mb-2">
        <button id="btnTambahGaji" class="btn btn-warning text-dark font-weight-bold shadow-sm">
            <i class="fas fa-plus"></i> Tambah data
        </button>
    </div>
    @endif

    <!-- Kartu Tabel -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Data Riwayat Gaji</h5>
        </div>

        <div class="card-body">
            {{-- FILTER (admin) --}}
            @if(auth()->user()->id_level == 1)
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <input type="text" id="filterNama" class="form-control form-control-sm shadow-sm" placeholder="Cari Nama Pegawai">
                </div>
                <div class="col-md-3 mb-2">
                    <input type="number" id="filterTahun" class="form-control form-control-sm shadow-sm" placeholder="Tahun Berlaku (YYYY)" min="1900" max="{{ date('Y') }}">
                </div>
                <div class="col-md-2 mb-2">
                    <button id="btnResetFilter" class="btn btn-sm btn-outline-secondary w-100 shadow-sm">
                        <i class="fas fa-sync-alt"></i> Reset
                    </button>
                </div>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover text-sm m-0">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th style="width: 60px;">No</th>
                            @if(auth()->user()->id_level == 1)
                                <th>Nama Pegawai</th>
                            @endif
                            <th>Tanggal Berlaku</th>
                            <th>Gaji Pokok</th>
                            @if(auth()->user()->id_level == 1)
                                <th style="width: 140px;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="tabelGaji">
                        @forelse ($riwayatGajis as $index => $data)
                        <tr id="row-{{ $data->id_riwayat_gaji }}">
                            <td class="text-center align-middle">{{ $index + 1 }}</td>

                            @if(auth()->user()->id_level == 1)
                            <td class="align-middle">{{ $data->user->nama ?? '-' }}</td>
                            @endif

                            <td class="align-middle">
                                @php
                                    try {
                                        $tgl = \Carbon\Carbon::parse($data->tanggal_berlaku)->format('d/m/Y');
                                    } catch (\Exception $e) {
                                        $tgl = $data->tanggal_berlaku;
                                    }
                                @endphp
                                {{ $tgl ?? '-' }}
                            </td>

                            <td class="align-middle text-left">
                                @php
                                    $nom = is_numeric($data->gaji_pokok) ? number_format($data->gaji_pokok, 0, ',', '.') : $data->gaji_pokok;
                                @endphp
                                Rp {{ $nom }}
                            </td>

                            @if(auth()->user()->id_level == 1)
                            <td class="text-center align-middle">
                                <button class="btn btn-sm btn-info btnViewGaji" data-id="{{ $data->id_riwayat_gaji }}" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning btnEditGaji" data-id="{{ $data->id_riwayat_gaji }}" title="Edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btnDeleteGaji" data-id="{{ $data->id_riwayat_gaji }}" title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->id_level == 1 ? 5 : 3 }}" class="text-center text-muted">
                                Belum ada data riwayat gaji.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Modal (Create/Edit/Show) -->
    <div class="modal fade" id="modalFormGaji" tabindex="-1" aria-labelledby="modalGajiLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="modalGajiContent">
                {{-- Konten dimuat via AJAX --}}
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(function () {
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    // === CREATE (admin) ===
    @if(auth()->user()->id_level == 1)
    $('#btnTambahGaji').on('click', function () {
        $('#modalFormGaji').modal('show');
        $('#modalGajiContent').load("{{ route('riwayat_gaji.create') }}");
    });

    $(document).on('submit', '#formCreateGaji', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('riwayat_gaji.store') }}",
            method: "POST",
            data: formData, contentType: false, processData: false,
            success: function (res) {
                $('#modalFormGaji').modal('hide');
                Swal.fire({icon:'success', title:'Berhasil!', text:res.success, timer:1500, showConfirmButton:false});
                setTimeout(()=>location.reload(), 1500);
            },
            error: function (err) {
                Swal.fire({icon:'error', title:'Gagal!', text: err.responseJSON?.message || 'Gagal menyimpan data.'});
            }
        });
    });
    @endif

    // === EDIT (admin) ===
    @if(auth()->user()->id_level == 1)
    $(document).on('click', '.btnEditGaji', function () {
        const id = $(this).data('id');
        $('#modalFormGaji').modal('show');
        $('#modalGajiContent').load(`{{ url('/riwayat_gaji') }}/${id}/edit`);
    });

    $(document).on('submit', '#formEditGaji', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const id = $(this).find('input[name="id_riwayat_gaji"]').val() || formData.get('id_riwayat_gaji');
        formData.append('_method', 'PUT');

        $.ajax({
            url: `{{ url('/riwayat_gaji') }}/${id}`,
            method: 'POST',
            data: formData, contentType: false, processData: false,
            success: function (res) {
                $('#modalFormGaji').modal('hide');
                Swal.fire({icon:'success', title:'Berhasil!', text:res.success, timer:1500, showConfirmButton:false});
                setTimeout(()=>location.reload(), 1500);
            },
            error: function (err) {
                Swal.fire({icon:'error', title:'Gagal!', text: err.responseJSON?.message || 'Gagal update data.'});
            }
        });
    });
    @endif

    // === VIEW (semua) ===
    $(document).on('click', '.btnViewGaji', function () {
        const id = $(this).data('id');
        $('#modalFormGaji').modal('show');
        $('#modalGajiContent').load(`{{ url('/riwayat_gaji') }}/${id}`);
    });

    // === DELETE (admin) ===
    @if(auth()->user()->id_level == 1)
    $(document).on('click', '.btnDeleteGaji', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus data ini?',
            text: 'Data tidak bisa dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('/riwayat_gaji') }}/${id}`,
                    method: 'POST',
                    data: {_method:'DELETE'},
                    success: function (res) {
                        $('#row-'+id).fadeOut('slow', function(){ $(this).remove(); });
                        Swal.fire({icon:'success', title:'Terhapus!', text:res.success || 'Data berhasil dihapus.', timer:1500});
                    },
                    error: function (err) {
                        Swal.fire({icon:'error', title:'Gagal!', text: err.responseJSON?.message || 'Gagal menghapus data.'});
                    }
                });
            }
        });
    });
    @endif

    // === FILTER (admin) ===
    @if(auth()->user()->id_level == 1)
    $('#filterNama, #filterTahun').on('input', function () {
        const nama = ($('#filterNama').val()||'').toLowerCase();
        const tahun = ($('#filterTahun').val()||'').trim();

        $('#tabelGaji tr').each(function () {
            const row = $(this);
            const textNama = row.find('td').eq(1).text().toLowerCase(); // kolom nama (admin)
            const tglText = row.find('td').eq(2).text(); // kolom tanggal (dd/mm/YYYY)
            const matchNama = nama === '' || textNama.includes(nama);
            const matchTahun = tahun === '' || (tglText.length >= 10 && tglText.slice(-4) === tahun);
            row.toggle(matchNama && matchTahun);
        });
    });

    $('#btnResetFilter').on('click', function () {
        $('#filterNama').val('');
        $('#filterTahun').val('');
        $('#filterNama').trigger('input');
    });
    @endif
});
</script>
@endpush
