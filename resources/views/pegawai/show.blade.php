<div class="modal-header" style="background-color: #003366; color: white;">
    <h5 class="modal-title"><i class="fas fa-id-badge mr-2"></i>Detail Profil Pegawai</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body" style="background-color: #f8f9fa;">
    <div class="row">

        {{-- Sidebar Foto dan Identitas --}}
        <div class="col-md-4 text-center border-right">
            <img src="{{ $pegawai->foto ? asset('storage/' . $pegawai->foto) : asset('images/default.png') }}"
                 class="rounded-circle border border-primary shadow-sm mb-2"
                 width="130" height="130" style="object-fit: cover;">
            <h5 class="mt-2 text-primary font-weight-bold">{{ $pegawai->nama }}</h5>
            <p class="text-muted mb-1">{{ $pegawai->nip }}</p>
            <span class="badge badge-warning text-dark px-3 py-1">
                {{ $pegawai->level->level_name ?? 'Level Tidak Diketahui' }}
            </span>
        </div>

        {{-- Informasi Detail --}}
        <div class="col-md-8">
            <table class="table table-bordered table-striped table-sm">
                <tbody>
                    <tr>
                        <th width="35%" class="bg-light">Email</th>
                        <td>{{ $pegawai->email }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Username</th>
                        <td>{{ $pegawai->username }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Jenis Kelamin</th>
                        <td>{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Agama</th>
                        <td>{{ $pegawai->agama }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">No HP</th>
                        <td>{{ $pegawai->no_tlp }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Pendidikan</th>
                        <td>
                            {{ $pegawai->pendidikan->jenis_pendidikan ?? '-' }}
                            @if($pegawai->pendidikan && $pegawai->pendidikan->tahun_kelulusan)
                                <span class="text-muted">({{ $pegawai->pendidikan->tahun_kelulusan }})</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Jabatan</th>
                        <td>
                            <span class="badge badge-info px-2 py-1">
                                {{ optional($pegawai->jabatan->refJabatan)->nama_jabatan ?? '-' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">TMT Jabatan</th>
                        <td>{{ optional($pegawai->jabatan->tmt) ? \Carbon\Carbon::parse($pegawai->jabatan->tmt)->format('d-m-Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Pangkat</th>
                        <td>
                            <span class="badge badge-success px-2 py-1">
                                {{ optional($pegawai->pangkat->refPangkat)->golongan_pangkat ?? '-' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">TMT Pangkat</th>
                        <td>{{ optional($pegawai->pangkat->tmt) ? \Carbon\Carbon::parse($pegawai->pangkat->tmt)->format('d-m-Y') : '-' }}</td>
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
