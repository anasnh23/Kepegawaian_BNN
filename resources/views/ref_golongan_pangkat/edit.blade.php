{{-- resources/views/refgolonganpangkat/edit.blade.php --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title">Edit Data Referensi Golongan Pangkat</h5>
    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formEditRefGolonganPangkat" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="id_ref_pangkat" value="{{ $refGolonganPangkat->id_ref_pangkat }}"> {{-- Tambahkan ini untuk JS update --}}
    <div class="modal-body">
        <div class="row">

            <div class="form-group col-md-6">
                <label for="golongan_pangkat">Golongan Pangkat <span class="text-danger">*</span></label>
                <input type="text" name="golongan_pangkat" class="form-control" value="{{ $refGolonganPangkat->golongan_pangkat }}" required>
            </div>

            <div class="form-group col-md-6">
                <label for="gaji_pokok">Gaji Pokok <span class="text-danger">*</span></label>
                <input type="number" name="gaji_pokok" class="form-control" value="{{ $refGolonganPangkat->gaji_pokok }}" required min="0" step="0.01">
            </div>

            <div class="form-group col-md-6">
                <label for="masa_kerja_min">Masa Kerja Min <span class="text-danger">*</span></label>
                <input type="number" name="masa_kerja_min" class="form-control" value="{{ $refGolonganPangkat->masa_kerja_min }}" required min="0">
            </div>

            <div class="form-group col-md-6">
                <label for="masa_kerja_maks">Masa Kerja Maks <span class="text-danger">*</span></label>
                <input type="number" name="masa_kerja_maks" class="form-control" value="{{ $refGolonganPangkat->masa_kerja_maks }}" required min="0">
            </div>

            <div class="form-group col-md-12">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" class="form-control">{{ $refGolonganPangkat->keterangan }}</textarea>
            </div>

        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Update</button>
    </div>
</form>
