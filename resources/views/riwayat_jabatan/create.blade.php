{{-- resources/views/riwayatjabatan/create.blade.php --}}
<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Tambah Data Riwayat Jabatan</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formCreateRiwayatJabatan" enctype="multipart/form-data">
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
                <label for="nama_jabatan">Nama Jabatan <span class="text-danger">*</span></label>
                <input type="text" name="nama_jabatan" class="form-control" required>
            </div>

            <div class="form-group col-md-6">
                <label for="tmt_mulai">TMT Mulai <span class="text-danger">*</span></label>
                <input type="date" name="tmt_mulai" class="form-control" required>
            </div>

            <div class="form-group col-md-6">
                <label for="tmt_selesai">TMT Selesai</label>
                <input type="date" name="tmt_selesai" class="form-control">
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