<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Tambah Data Pegawai</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formCreatePegawai" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        <div class="row">

            <div class="form-group col-md-6">
                <label for="nip">NIP <span class="text-danger">*</span></label>
                <input type="text" name="nip" class="form-control" required>
            </div>

            <div class="form-group col-md-6">
                <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" required>
            </div>

            <div class="form-group col-md-6">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group col-md-6">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control">
            </div>

            <div class="form-group col-md-6">
                <label for="password">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group col-md-6">
                <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="jenis_kelamin" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="agama">Agama</label>
                <input type="text" name="agama" class="form-control">
            </div>

            <div class="form-group col-md-6">
                <label for="no_tlp">No. HP</label>
                <input type="text" name="no_tlp" class="form-control">
            </div>

            <div class="form-group col-md-6">
                <label for="id_level">Level <span class="text-danger">*</span></label>
                <select name="id_level" class="form-control" required>
                    <option value="">-- Pilih Level --</option>
                    @foreach ($levels as $level)
                        <option value="{{ $level->id_level }}">{{ $level->level_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="jenis_pendidikan">Pendidikan</label>
                <select name="jenis_pendidikan" class="form-control">
                    <option value="">-- Pilih --</option>
                    <option value="SMA">SMA</option>
                    <option value="S1">S1</option>
                    <option value="S2">S2</option>
                    <option value="S3">S3</option>
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="tahun_kelulusan">Tahun Kelulusan</label>
                <input type="number" name="tahun_kelulusan" class="form-control" min="1970" max="{{ date('Y') }}">
            </div>

            <div class="form-group col-md-6">
                <label for="foto">Foto</label>
                <input type="file" name="foto" class="form-control-file">
            </div>

        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan</button>
    </div>
</form>
