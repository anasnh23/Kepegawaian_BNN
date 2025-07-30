{{-- resources/views/riwayat_gaji/show.blade.php --}}
<div class="modal-header" style="background-color: #003366; color: white;">
    <h5 class="modal-title"><i class="fas fa-money-bill-wave mr-2"></i>Detail Riwayat Gaji</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body" style="background-color: #f8f9fa;">
    <div class="row">

        {{-- Sidebar Identitas --}}
        <div class="col-md-4 text-center border-right">
            <h5 class="mt-2 text-primary font-weight-bold">{{ $riwayatGaji->user->nama ?? 'Pegawai Tidak Diketahui' }}</h5>
            <p class="text-muted mb-1">ID: {{ $riwayatGaji->id_riwayat_gaji }}</p>
        </div>

        {{-- Informasi Detail --}}
        <div class="col-md-8">
            <table class="table table-bordered table-striped table-sm">
                <tbody>
                    <tr>
                        <th width="35%" class="bg-light">Nama Pegawai</th>
                        <td>{{ $riwayatGaji->user->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tanggal Berlaku</th>
                        <td>{{ \Carbon\Carbon::parse($riwayatGaji->tanggal_berlaku)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Gaji Pokok</th>
                        <td>Rp. {{ number_format($riwayatGaji->gaji_pokok, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Keterangan</th>
                        <td>{{ $riwayatGaji->keterangan ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

<div class="modal-footer justify-content-end bg-light">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-times-circle"></i>