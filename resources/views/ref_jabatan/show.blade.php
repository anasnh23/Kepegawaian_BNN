<div class="modal-header" style="background-color: #003366; color: white;">
    <h5 class="modal-title"><i class="fas fa-id-badge mr-2"></i>Detail Jabatan Referensi</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body" style="background-color: #f8f9fa;">
    <div class="row">

        {{-- Informasi Detail Jabatan --}}
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-sm">
                <tbody>
                    <tr>
                        <th class="bg-light">Nama Jabatan</th>
                        <td>{{ $refJabatan->nama_jabatan }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Eselon</th>
                        <td>{{ $refJabatan->eselon ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Keterangan</th>
                        <td>{{ $refJabatan->keterangan ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

<div class="modal-footer justify-content-end bg-light">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-times-circle"></i> Tutup
    </button>
</div>