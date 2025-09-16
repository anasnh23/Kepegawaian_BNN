{{-- resources/views/riwayat_gaji/show.blade.php --}}
<div class="modal-header" style="background-color:#003366;color:white;">
    <h5 class="modal-title"><i class="fas fa-eye mr-2"></i>Detail Riwayat Gaji</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body" style="background-color:#f8f9fa;">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-sm">
                <tbody>
                    <tr>
                        <th class="bg-light" style="width:240px;">Nama Pegawai</th>
                        <td>{{ $riwayatGaji->user->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tanggal Berlaku</th>
                        <td>
                            @php
                                try { $tgl = \Carbon\Carbon::parse($riwayatGaji->tanggal_berlaku)->format('d/m/Y'); }
                                catch (\Exception $e) { $tgl = $riwayatGaji->tanggal_berlaku; }
                            @endphp
                            {{ $tgl ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Gaji Pokok</th>
                        <td>
                            @php
                                $nom = is_numeric($riwayatGaji->gaji_pokok) ? number_format($riwayatGaji->gaji_pokok, 0, ',', '.') : $riwayatGaji->gaji_pokok;
                            @endphp
                            Rp {{ $nom }}
                        </td>
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
