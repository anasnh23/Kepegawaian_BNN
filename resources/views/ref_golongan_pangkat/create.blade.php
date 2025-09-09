{{-- resources/views/refgolonganpangkat/create.blade.php --}}
<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Tambah Data Referensi Golongan Pangkat</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formCreateRefGolonganPangkat" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        <div class="row">

            <div class="form-group col-md-6">
                <label for="golongan_pangkat">Golongan Pangkat <span class="text-danger">*</span></label>
                <input type="text" name="golongan_pangkat" class="form-control" required>
            </div>

            <div class="form-group col-md-6">
                <label for="gaji_pokok">Gaji Pokok <span class="text-danger">*</span></label>
                <input type="number" name="gaji_pokok" class="form-control" required min="0" step="0.01">
            </div>

            <div class="form-group col-md-6">
                <label for="masa_kerja_min">Masa Kerja Min <span class="text-danger">*</span></label>
                <input type="number" name="masa_kerja_min" class="form-control" required min="0">
            </div>

            <div class="form-group col-md-6">
                <label for="masa_kerja_maks">Masa Kerja Maks <span class="text-danger">*</span></label>
                <input type="number" name="masa_kerja_maks" class="form-control" required min="0">
            </div>

            <div class="form-group col-md-12">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" class="form-control"></textarea>
            </div>

        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan</button>
    </div>
</form>
