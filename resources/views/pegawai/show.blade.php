<div class="modal-header bg-secondary text-white">
    <h5 class="modal-title">Detail Pegawai</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-4 text-center">
            <img src="{{ $pegawai->foto ? asset('storage/' . $pegawai->foto) : asset('images/default.png') }}"
                 class="img-thumbnail rounded-circle mb-2" width="120" height="120">
            <h6 class="mt-2">{{ $pegawai->nama }}</h6>
            <p class="text-muted">{{ $pegawai->nip }}</p>
        </div>
        <div class="col-md-8">
            <table class="table table-sm table-borderless">
                <tr><th width="35%">Email</th><td>{{ $pegawai->email }}</td></tr>
                <tr><th>Username</th><td>{{ $pegawai->username }}</td></tr>
                <tr><th>Jenis Kelamin</th><td>{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                <tr><th>Agama</th><td>{{ $pegawai->agama }}</td></tr>
                <tr><th>No HP</th><td>{{ $pegawai->no_tlp }}</td></tr>
                <tr><th>Level</th><td>{{ $pegawai->level->level_name ?? '-' }}</td></tr>
                <tr><th>Pendidikan</th><td>{{ $pegawai->pendidikan->jenis_pendidikan ?? '-' }}</td></tr>
                <tr><th>Tahun Kelulusan</th><td>{{ $pegawai->pendidikan->tahun_kelulusan ?? '-' }}</td></tr>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer justify-content-end">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>
