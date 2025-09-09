{{-- resources/views/pangkat/create.blade.php --}}
<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Tambah Data Pangkat</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formCreatePangkat" enctype="multipart/form-data">
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
                <label for="id_ref_pangkat">Pangkat/Golongan <span class="text-danger">*</span></label>
                <select name="id_ref_pangkat" class="form-control" required>
                    <option value="">-- Pilih Pangkat --</option>
                    @foreach ($refPangkats as $ref)
                        <option value="{{ $ref->id_ref_pangkat }}">{{ $ref->pangkat ?? $ref->golongan_pangkat }} / {{ $ref->golongan ?? '' }}</option> <!-- Asumsi field, sesuaikan -->
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="tmt">TMT <span class="text-danger">*</span></label>
                <input type="date" name="tmt" class="form-control" required>
            </div>

        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan</button>
    </div>
</form>
