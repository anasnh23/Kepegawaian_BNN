{{-- resources/views/riwayat_gaji/create.blade.php --}}
<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Tambah Data Riwayat Gaji</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formCreateRiwayatGaji" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        <div class="row">

            <div class="form-group col-md-6">
                <label for="id_user">Pegawai <span class="text-danger">*</span></label>
                <select name="id_user" class="form-control" required>
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach ($users as $user) {{-- Asumsi $users dikirim dari controller --}}
                        <option value="{{ $user->id_user }}">{{ $user->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="tanggal_berlaku">Tanggal Berlaku <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_berlaku" class="form-control" required>
            </div>

            <div class="form-group col-md-6">
                <label for="gaji_pokok">Gaji Pokok <span class="text-danger">*</span></label>
                <input type="number" name="gaji_pokok" class="form-control" required min="0">
            </div>

            <div class="form-group col-md-12">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3"></textarea>
            </div>

        </div>
    </div>

    <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan</button>
    </div>
</form>
