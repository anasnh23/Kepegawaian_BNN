{{-- ====================== EDIT: Data Pegawai ====================== --}}
<div class="modal-header" style="background:#0a2647;color:#fff;border:0;">
  <h5 class="modal-title font-weight-bold">
    <i class="fas fa-user-edit mr-2"></i> Edit Data Pegawai
  </h5>
  <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<form id="formEditPegawai" enctype="multipart/form-data">
  <input type="hidden" name="id_user" value="{{ $pegawai->id_user }}">

  {{-- ====== STYLE ====== --}}
  <style>
    :root{
      --bnn-navy:#0a2647; --bnn-blue:#144272; --bnn-cyan:#2c74b3; --bnn-gold:#f4c430; --muted:#64748b;
    }
    .bnn-section{
      background:#f8fafc; border:1px solid #e6edf6; border-radius:14px; padding:16px 16px 8px;
      margin-bottom:14px; box-shadow:0 4px 12px rgba(16,24,40,.06);
    }
    .bnn-title{
      display:inline-flex; align-items:center; gap:8px; background:#e7eef9; color:var(--bnn-navy);
      padding:6px 12px; font-weight:800; border-radius:10px; margin-bottom:12px;
    }
    .form-control,.custom-select{ border-radius:10px; }
    .form-control:focus,.custom-select:focus{
      border-color:var(--bnn-cyan); box-shadow:0 0 0 .18rem rgba(44,116,179,.20);
    }
    .btn-bnn{ background:linear-gradient(135deg,var(--bnn-cyan),#3b82f6); color:#fff; border:0; font-weight:700; border-radius:10px; }
    .btn-bnn:hover{ filter:brightness(1.05); }

    /* tombol reset password */
    .btn-reset{ background:#f59e0b; color:#fff; border:0; border-radius:8px; font-weight:600; }
    .btn-reset:hover{ filter:brightness(1.08); }
  </style>

  <div class="modal-body" style="background:#f2f6fb;">
    <div class="row">

      {{-- Identitas Pegawai --}}
      <div class="col-12 bnn-section">
        <div class="bnn-title"><i class="fas fa-id-card"></i> Identitas Pegawai</div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>NIP <span class="text-danger">*</span></label>
            <input type="text" name="nip" class="form-control" value="{{ $pegawai->nip }}" required>
          </div>
          <div class="form-group col-md-6">
            <label>Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" value="{{ $pegawai->nama }}" required>
          </div>
          <div class="form-group col-md-6">
            <label>Jenis Kelamin <span class="text-danger">*</span></label>
            <select name="jenis_kelamin" class="form-control" required>
              <option value="L" {{ $pegawai->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
              <option value="P" {{ $pegawai->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
          </div>
          <div class="form-group col-md-6">
            <label>Agama</label>
            <input type="text" name="agama" class="form-control" value="{{ $pegawai->agama }}">
          </div>
        </div>
      </div>

      {{-- Kontak Pegawai --}}
      <div class="col-12 bnn-section">
        <div class="bnn-title"><i class="fas fa-address-book"></i> Kontak Pegawai</div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" value="{{ $pegawai->email }}" required>
          </div>
          <div class="form-group col-md-6">
            <label>No. HP</label>
            <input type="text" name="no_tlp" class="form-control" value="{{ $pegawai->no_tlp }}">
          </div>
          <div class="form-group col-md-6">
            <label>Username</label>
            <input type="text" id="usernameEdit" name="username" class="form-control" value="{{ $pegawai->username }}">
          </div>
          <div class="form-group col-md-6">
            <label>Password <small>(isi jika ingin ubah)</small></label>
            <div class="input-group">
              <input type="password" id="passwordEdit" name="password" class="form-control">
              <div class="input-group-append">
                <button type="button" id="btnResetPassword" class="btn btn-reset">
                  <i class="fas fa-undo mr-1"></i> Reset
                </button>
              </div>
            </div>
            <small class="text-muted d-block mt-1">Klik <strong>Reset</strong> untuk mengisi password = username.</small>
            <input type="hidden" id="resetPasswordFlag" name="reset_password" value="0">
          </div>
        </div>
      </div>

      {{-- Data Diri Pegawai --}}
      <div class="col-12 bnn-section">
        <div class="bnn-title"><i class="fas fa-briefcase"></i> Data Diri Pegawai</div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Level <span class="text-danger">*</span></label>
            <select name="id_level" class="form-control" required>
              @foreach ($levels as $level)
                <option value="{{ $level->id_level }}" {{ $pegawai->id_level == $level->id_level ? 'selected' : '' }}>
                  {{ $level->level_name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-6">
            <label>Pendidikan</label>
            <select name="jenis_pendidikan" class="form-control">
              <option value="">-- Pilih --</option>
              <option value="SMA" {{ optional($pegawai->pendidikan)->jenis_pendidikan == 'SMA' ? 'selected' : '' }}>SMA</option>
              <option value="S1"  {{ optional($pegawai->pendidikan)->jenis_pendidikan == 'S1'  ? 'selected' : '' }}>S1</option>
              <option value="S2"  {{ optional($pegawai->pendidikan)->jenis_pendidikan == 'S2'  ? 'selected' : '' }}>S2</option>
              <option value="S3"  {{ optional($pegawai->pendidikan)->jenis_pendidikan == 'S3'  ? 'selected' : '' }}>S3</option>
            </select>
          </div>
          <div class="form-group col-md-6">
            <label>Tahun Kelulusan</label>
            <input type="number" name="tahun_kelulusan" class="form-control"
                   min="1970" max="{{ date('Y') }}"
                   value="{{ optional($pegawai->pendidikan)->tahun_kelulusan }}">
          </div>
          <div class="form-group col-md-6">
            <label>Jabatan</label>
            <select name="id_ref_jabatan" class="form-control">
              <option value="">-- Pilih Jabatan --</option>
              @foreach ($jabatans as $jabatan)
                <option value="{{ $jabatan->id_ref_jabatan }}" {{ optional($pegawai->jabatan)->id_ref_jabatan == $jabatan->id_ref_jabatan ? 'selected' : '' }}>
                  {{ $jabatan->nama_jabatan }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-6">
            <label>TMT Jabatan</label>
            <input type="date" name="tmt_jabatan" class="form-control"
                   value="{{ optional($pegawai->jabatan)->tmt }}">
          </div>
          <div class="form-group col-md-6">
            <label>Pangkat</label>
            <select name="id_ref_pangkat" class="form-control">
              <option value="">-- Pilih Pangkat --</option>
              @foreach ($pangkats as $pangkat)
                <option value="{{ $pangkat->id_ref_pangkat }}" {{ optional($pegawai->pangkat)->id_ref_pangkat == $pangkat->id_ref_pangkat ? 'selected' : '' }}>
                  {{ $pangkat->golongan_pangkat }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-6">
            <label>TMT Pangkat</label>
            <input type="date" name="tmt_pangkat" class="form-control"
                   value="{{ optional($pegawai->pangkat)->tmt }}">
          </div>
        </div>
      </div>

      {{-- Foto --}}
      <div class="col-12 bnn-section">
        <div class="bnn-title"><i class="fas fa-camera"></i> Foto Pegawai</div>
        <div class="form-group">
          <input type="file" name="foto" class="form-control-file">
          @if($pegawai->foto)
            <div class="mt-2">
              <small class="text-muted">Foto saat ini:</small><br>
              <img src="{{ asset('storage/'.$pegawai->foto) }}" alt="Foto" width="80" height="80" class="rounded-circle border">
            </div>
          @endif
        </div>
      </div>

    </div>
  </div>

  <div class="modal-footer" style="background:#0a2647;border:0;">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
      <i class="fas fa-times mr-1"></i> Batal
    </button>
    <button type="submit" class="btn btn-bnn">
      <i class="fas fa-save mr-1"></i> Update
    </button>
  </div>
</form>

@push('scripts')
<script>
  // === Reset password = username
  $(document).on('click', '#btnResetPassword', function () {
    const $username = $('#usernameEdit');
    const $password = $('#passwordEdit');
    const $flag     = $('#resetPasswordFlag');

    const uname = ($username.val() || '').trim();
    if (!uname) {
      return Swal.fire({ icon:'warning', title:'Username kosong', text:'Isi username terlebih dahulu.' });
    }
    $password.val(uname);
    $flag.val('1');
    Swal.fire({ icon:'info', title:'Password direset', text:'Password di-set sama dengan username.' });
  });

  // === Sinkron bila username berubah setelah klik Reset
  $(document).on('input', '#usernameEdit', function () {
    if ($('#resetPasswordFlag').val() === '1') {
      $('#passwordEdit').val($(this).val());
    }
  });

  // === Guard sebelum submit (pastikan password ikut terkirim saat reset aktif)
  $(document).on('submit', '#formEditPegawai', function () {
    if ($('#resetPasswordFlag').val() === '1') {
      $('#passwordEdit').val(($('#usernameEdit').val() || '').trim());
    }
  });
</script>
@endpush
