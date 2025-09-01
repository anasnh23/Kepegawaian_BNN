{{-- resources/views/kgp/show.blade.php --}}
<div class="modal-header" style="background-color: #003366; color: white;">
    <h5 class="modal-title"><i class="fas fa-eye mr-2"></i>Detail Riwayat KGP</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body" style="background-color: #f8f9fa;">
    <div class="row">

        {{-- Informasi Detail Riwayat KGP --}}
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-sm">
                <tbody>
                    @if(auth()->user()->id_level == 1)
                    <tr>
                        <th class="bg-light">Nama Pegawai</th>
                        <td>{{ $kgp->pegawai->nama ?? '-' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th class="bg-light">Tahun KGP</th>
                        <td>{{ $kgp->tahun_kgp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">TMT</th>
                        <td>{{ $kgp->tmt ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>
