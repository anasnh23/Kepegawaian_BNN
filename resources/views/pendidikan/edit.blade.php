{{-- resources/views/pendidikan/edit.blade.php --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title">Edit Data Pendidikan</h5>
    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formEditPendidikan" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="id_pendidikan" value="{{ $pendidikan->id_pendidikan }}"> {{-- Tambahkan ini untuk JS update --}}
    <div class="modal-body">
        <div class="row">

            <div class="form-group col-md-6">
                <label for="id_user">Pegawai <span class="text-danger">*</span></label>
                <select name="id_user" class="form-control" required>
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id_user }}" {{ $pendidikan->id_user == $user->id_user ? 'selected' : '' }}>{{ $user->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="jenis_pendidikan">Jenis Pendidikan <span class="text-danger">*</span></label>
                <input type="text" name="jenis_pendidikan" class="form-control" value="{{ $pendidikan->jenis_pendidikan }}" required>
            </div>

            <div class="form-group col-md-6">
                <label for="tahun_kelulusan">Tahun Kelulusan <span class="text-danger">*</span></label>
                <input type="number" name="tahun_kelulusan" class="form-control" value="{{ $pendidikan->tahun_kelulusan }}" required min="1900" max="{{ date('Y') }}">
            </div>

        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Update</button>
    </div>
</form>