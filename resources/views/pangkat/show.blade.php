{{-- resources/views/pangkat/show.blade.php --}}
<div class="modal-header" style="background-color: #003366; color: white;">
    <h5 class="modal-title"><i class="fas fa-eye mr-2"></i>Detail Pangkat</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body" style="background-color: #f8f9fa;">
    <div class="row">

        {{-- Informasi Detail Pangkat --}}
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-sm">
                <tbody>
                    <tr>
                        <th class="bg-light">Nama Pegawai</th>
                        <td>{{ $pangkat->user->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Pangkat/Golongan</th>
                        <td>{{ $pangkat->refPangkat->pangkat ?? '-' }} / {{ $pangkat->refPangkat->golongan ?? '-' }}</td> <!-- Asumsi field 'pangkat' dan 'golongan', sesuaikan jika berbeda -->
                    </tr>
                    <tr>
                        <th class="bg-light">TMT</th>
                        <td>{{ $pangkat->tmt ? date('d-m-Y', strtotime($pangkat->tmt)) : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

<div class="modal-footer justify-content-between">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>
