<div class="modal-header bnn-header text-white">
  <h5 class="modal-title font-weight-bold">
    <i class="fas fa-pencil-alt mr-2"></i> Edit Jabatan Referensi
  </h5>
  <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<form id="formEditJabatan" enctype="multipart/form-data">
  <input type="hidden" name="id_ref_jabatan" value="{{ $refJabatan->id_ref_jabatan }}">
  @csrf

  <style>
    :root{
      --bnn-navy:#003366; --bnn-blue:#144272; --bnn-gold:#f0ad4e;
      --line:#e6edf6; --soft:#f8fafc;
    }
    .bnn-header{
      background:linear-gradient(135deg,var(--bnn-navy),var(--bnn-blue));
      border-top-left-radius:.5rem; border-top-right-radius:.5rem;
    }
    .bnn-section{ 
      background:var(--soft); border:1px solid var(--line);
      border-radius:12px; padding:14px; margin-bottom:12px;
    }
    .bnn-label{ font-weight:700; color:var(--bnn-navy); }
    .form-control{ border-radius:10px; }
    .form-control:focus{
      border-color:var(--bnn-gold);
      box-shadow:0 0 0 .18rem rgba(240,173,78,.25);
    }
    .btn-bnn{
      background:linear-gradient(135deg,var(--bnn-gold),#ffd777);
      color:#172554; font-weight:700; border-radius:10px;
      box-shadow:0 4px 12px rgba(244,196,48,.35);
    }
    .btn-bnn:hover{ filter:brightness(1.05); }
  </style>

  <div class="modal-body">
    <div class="row">

      <div class="col-12 bnn-section">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="nama_jabatan" class="bnn-label">Nama Jabatan <span class="text-danger">*</span></label>
            <input type="text" name="nama_jabatan" class="form-control" 
                   value="{{ $refJabatan->nama_jabatan }}" required>
            @error('nama_jabatan')
              <div class="text-danger small">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group col-md-6">
            <label for="eselon" class="bnn-label">Eselon</label>
            <input type="text" name="eselon" class="form-control" 
                   value="{{ $refJabatan->eselon }}">
            @error('eselon')
              <div class="text-danger small">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="form-group">
          <label for="keterangan" class="bnn-label">Keterangan</label>
          <textarea name="keterangan" class="form-control" rows="3">{{ $refJabatan->keterangan }}</textarea>
          @error('keterangan')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>
      </div>

    </div>
  </div>

  <div class="modal-footer justify-content-between">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
      <i class="fas fa-times mr-1"></i> Batal
    </button>
    <button type="submit" class="btn btn-bnn">
      <i class="fas fa-save mr-1"></i> Update
    </button>
  </div>
</form>
