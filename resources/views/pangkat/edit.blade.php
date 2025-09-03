{{-- resources/views/pangkat/edit.blade.php --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title">Edit Data Pangkat</h5>
    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formEditPangkat" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="id_pangkat" value="{{ $pangkat->id_pangkat }}"> {{-- Tambahkan ini untuk JS update --}}
    <div class="modal-body">
        <div class="row">

            <div class="form-group col-md-6">
                <label for="id_user">Pegawai <span class="text-danger">*</span></label>
                <select name="id_user" class="form-control" required>
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id_user }}" {{ $pangkat->id_user == $user->id_user ? 'selected' : '' }}>{{ $user->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="id_ref_pangkat">Pangkat/Golongan <span class="text-danger">*</span></label>
                <select name="id_ref_pangkat" class="form-control" required>
                    <option value="">-- Pilih Pangkat --</option>
                    @foreach ($refPangkats as $ref)
                        <option value="{{ $ref->id_ref_pangkat }}" {{ $pangkat->id_ref_pangkat == $ref->id_ref_pangkat ? 'selected' : '' }}>
                            {{ $ref->pangkat ?? $ref->golongan_pangkat }} / {{ $ref->golongan ?? '' }}
                        </option> <!-- Asumsi field 'pangkat' dan 'golongan', sesuaikan jika berbeda -->
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="tmt">TMT <span class="text-danger">*</span></label>
                <input type="date" name="tmt" class="form-control" value="{{ $pangkat->tmt }}" required>
            </div>

        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Update</button>
    </div>
</form>
