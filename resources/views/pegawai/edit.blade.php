<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Edit Data Pegawai</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formEditPegawai" enctype="multipart/form-data">
    {{-- CSRF akan ditambahkan manual melalui JS --}}
    <div class="modal-body">
        <div class="row">

            <input type="hidden" name="id_user" value="{{ $pegawai->id_user }}">

            <div class="form-group col-md-6">
                <label for="nip">NIP <span class="text-danger">*</span></label>
                <input type="text" name="nip" class="form-control" value="{{ $pegawai->nip }}" required>
            </div>

            <div class="form-group col-md-6">
                <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" value="{{ $pegawai->nama }}" required>
            </div>

            <div class="form-group col-md-6">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ $pegawai->email }}" required>
            </div>

            <div class="form-group col-md-6">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control" value="{{ $pegawai->username }}">
            </div>

            <div class="form-group col-md-6">
                <label for="password">Password <small>(Isi jika ingin mengubah)</small></label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="form-group col-md-6">
                <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="jenis_kelamin" class="form-control" required>
                    <option value="L" {{ $pegawai->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ $pegawai->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="agama">Agama</label>
                <input type="text" name="agama" class="form-control" value="{{ $pegawai->agama }}">
            </div>

            <div class="form-group col-md-6">
                <label for="no_tlp">No. HP</label>
                <input type="text" name="no_tlp" class="form-control" value="{{ $pegawai->no_tlp }}">
            </div>

            <div class="form-group col-md-6">
                <label for="id_level">Level <span class="text-danger">*</span></label>
                <select name="id_level" class="form-control" required>
                    @foreach ($levels as $level)
                        <option value="{{ $level->id_level }}" {{ $pegawai->id_level == $level->id_level ? 'selected' : '' }}>
                            {{ $level->level_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="jenis_pendidikan">Pendidikan</label>
                <select name="jenis_pendidikan" class="form-control">
                    <option value="">-- Pilih --</option>
                    <option value="SMA" {{ optional($pegawai->pendidikan)->jenis_pendidikan == 'SMA' ? 'selected' : '' }}>SMA</option>
                    <option value="S1" {{ optional($pegawai->pendidikan)->jenis_pendidikan == 'S1' ? 'selected' : '' }}>S1</option>
                    <option value="S2" {{ optional($pegawai->pendidikan)->jenis_pendidikan == 'S2' ? 'selected' : '' }}>S2</option>
                    <option value="S3" {{ optional($pegawai->pendidikan)->jenis_pendidikan == 'S3' ? 'selected' : '' }}>S3</option>
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="tahun_kelulusan">Tahun Kelulusan</label>
                <input type="number" name="tahun_kelulusan" class="form-control" min="1970" max="{{ date('Y') }}"
                       value="{{ optional($pegawai->pendidikan)->tahun_kelulusan }}">
            </div>

            <div class="form-group col-md-6">
                <label for="foto">Foto Baru</label>
                <input type="file" name="foto" class="form-control-file">
                @if($pegawai->foto)
                    <small class="form-text text-muted">Foto saat ini: {{ $pegawai->foto }}</small>
                @endif
            </div>

        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
