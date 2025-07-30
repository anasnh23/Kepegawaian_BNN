<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Edit Jabatan Referensi</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formEditJabatan" enctype="multipart/form-data">
    <input type="hidden" name="id_ref_jabatan" value="{{ $refJabatan->id_ref_jabatan }}">

    <div class="modal-body">
        <div class="row">

            <div class="form-group col-md-6">
                <label for="nama_jabatan">Nama Jabatan <span class="text-danger">*</span></label>
                <input type="text" name="nama_jabatan" class="form-control" required value="{{ $refJabatan->nama_jabatan }}">
                @error('nama_jabatan')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label for="eselon">Eselon</label>
                <input type="text" name="eselon" class="form-control" value="{{ $refJabatan->eselon }}">
                @error('eselon')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-12">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" class="form-control">{{ $refJabatan->keterangan }}</textarea>
                @error('keterangan')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
