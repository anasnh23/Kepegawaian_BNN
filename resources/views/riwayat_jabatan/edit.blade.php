{{-- resources/views/riwayatjabatan/edit.blade.php --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title">Edit Data Riwayat Jabatan</h5>
    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formEditRiwayatJabatan" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="id_riwayat_jabatan" value="{{ $riwayatJabatan->id_riwayat_jabatan }}"> {{-- Untuk JS update --}}
    <div class="modal-body">
        <div class="row">

            <div class="form-group col-md-6">
                <label for="id_user">Pegawai <span class="text-danger">*</span></label>
                <select name="id_user" class="form-control" required>
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id_user }}" {{ $riwayatJabatan->id_user == $user->id_user ? 'selected' : '' }}>{{ $user->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="nama_jabatan">Nama Jabatan <span class="text-danger">*</span></label>
                <input type="text" name="nama_jabatan" class="form-control" value="{{ $riwayatJabatan->nama_jabatan }}" required>
            </div>

            <div class="form-group col-md-6">
                <label for="tmt_mulai">TMT Mulai <span class="text-danger">*</span></label>
                <input type="date" name="tmt_mulai" class="form-control" value="{{ $riwayatJabatan->tmt_mulai }}" required>
            </div>

            <div class="form-group col-md-6">
                <label for="tmt_selesai">TMT Selesai</label>
                <input type="date" name="tmt_selesai" class="form-control" value="{{ $riwayatJabatan->tmt_selesai }}">
            </div>

            <div class="form-group col-md-12">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3">{{ $riwayatJabatan->keterangan }}</textarea>
            </div>

        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Update</button>
    </div>
</form>