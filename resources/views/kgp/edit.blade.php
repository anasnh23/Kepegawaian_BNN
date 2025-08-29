
{{-- resources/views/kgp/edit.blade.php --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title">Edit Data Riwayat KGP</h5>
    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formEditKgp" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="id_kgp" value="{{ $kgp->id_kgp }}">
    <div class="modal-body">
        <div class="row">

            <div class="form-group col-md-6">
                <label for="id_user">Pegawai <span class="text-danger">*</span></label>
                <select name="id_user" class="form-control" required>
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id_user }}" {{ $kgp->id_user == $user->id_user ? 'selected' : '' }}>{{ $user->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="tahun_kgp">Tahun KGP <span class="text-danger">*</span></label>
                <input type="number" name="tahun_kgp" class="form-control" value="{{ $kgp->tahun_kgp }}" required min="1900" max="2100">
            </div>

            <div class="form-group col-md-6">
                <label for="tmt">TMT <span class="text-danger">*</span></label>
                <input type="date" name="tmt" class="form-control" value="{{ $kgp->tmt }}" required>
            </div>

        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Update</button>
    </div>
</form>
