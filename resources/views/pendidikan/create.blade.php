{{-- resources/views/pendidikan/create.blade.php --}}
<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Tambah Data Pendidikan</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formCreatePendidikan" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        <div class="row">

            <div class="form-group col-md-6">
                <label for="id_user">Pegawai <span class="text-danger">*</span></label>
                <select name="id_user" class="form-control" required>
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id_user }}">{{ $user->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="jenis_pendidikan">Jenis Pendidikan <span class="text-danger">*</span></label>
                <input type="text" name="jenis_pendidikan" class="form-control" required>
            </div>

            <div class="form-group col-md-6">
                <label for="tahun_kelulusan">Tahun Kelulusan <span class="text-danger">*</span></label>
                <input type="number" name="tahun_kelulusan" class="form-control" required min="1900" max="{{ date('Y') }}">
            </div>

        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan</button>
    </div>
</form>