{{-- resources/views/riwayatjabatan/show.blade.php --}}
<div class="modal-header" style="background-color: #003366; color: white;">
    <h5 class="modal-title"><i class="fas fa-eye mr-2"></i>Detail Riwayat Jabatan</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body" style="background-color: #f8f9fa;">
    <div class="row">

        {{-- Informasi Detail Riwayat Jabatan --}}
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-sm">
                <tbody>
                    <tr>
                        <th class="bg-light">Nama Pegawai</th>
                        <td>{{ $riwayatJabatan->user->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Nama Jabatan</th>
                        <td>{{ $riwayatJabatan->nama_jabatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">TMT Mulai</th>
                        <td>{{ $riwayatJabatan->tmt_mulai ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">TMT Selesai</th>
                        <td>{{ $riwayatJabatan->tmt_selesai ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Keterangan</th>
                        <td>{{ $riwayatJabatan->keterangan ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

<div class="modal-footer justify-content-between">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>