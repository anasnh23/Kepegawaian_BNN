{{-- resources/views/pegawai/edit.blade.php --}}
{{-- Modal: Edit Data Pegawai (BNN Theme) --}}
<form id="formEditPegawai" enctype="multipart/form-data" autocomplete="off" novalidate>
    @csrf
    <input type="hidden" name="id_user" value="{{ $pegawai->id_user }}">

    {{-- ================= HEADER ================= --}}
    <div class="modal-header bnn-header">
        <h5 class="modal-title bnn-title">
            <i class="fas fa-user-edit mr-2"></i> Edit Data Pegawai
        </h5>

        <button type="button"
                id="btnCloseEditModal"
                class="close text-white"
                aria-label="Tutup"
                data-dismiss="modal" data-bs-dismiss="modal">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    {{-- ================= BODY ================= --}}
    <div class="modal-body bnn-body">
        {{-- ======= STYLE (khusus komponen ini) ======= --}}
        <style>
            :root{
                --bnn-navy:#003366; --bnn-navy-2:#0b2f5e; --bnn-blue:#144272;
                --bnn-cyan:#2c74b3; --bnn-gold:#f0ad4e; --bnn-soft:#f5f8fc; --muted:#6b7a90;
            }
            .bnn-header{
                background:linear-gradient(135deg,var(--bnn-navy),#012148 60%,var(--bnn-navy-2));
                color:#fff; position:relative; overflow:hidden;
            }
            .bnn-header::after{
                content:""; position:absolute; right:-60px; top:-60px; width:180px; height:180px; opacity:.07;
                background:url('{{ asset('images/bnn-watermark.svg') }}') no-repeat center/contain;
                pointer-events:none;
            }
            .bnn-title{ font-weight:800; letter-spacing:.2px; text-shadow:0 2px 6px rgba(0,0,0,.25); }
            .bnn-body{ background:var(--bnn-soft); }

            .bnn-section{
                background:#fff; border:1px solid #e6edf6; border-radius:14px; padding:16px;
                margin-bottom:14px; box-shadow:0 6px 18px rgba(0,33,72,.06);
            }
            .bnn-section .section-title{
                display:inline-flex; align-items:center; gap:8px;
                background:#eaf1ff; color:var(--bnn-navy); font-weight:800;
                padding:6px 12px; border-radius:10px; margin-bottom:12px;
            }

            .form-control, .custom-select { border-radius:10px; }
            .form-control:focus, .custom-select:focus{
                border-color:var(--bnn-cyan); box-shadow:0 0 0 .18rem rgba(44,116,179,.20);
            }
            label{ font-weight:700; color:#243659; }
            .hint{ color:var(--muted); font-size:.82rem; }
            .invalid-feedback{ display:block; }

            .btn-bnn{
                background:linear-gradient(135deg,var(--bnn-navy),#1a3d73);
                color:#fff; border:0; font-weight:800; letter-spacing:.2px; border-radius:10px;
            }
            .btn-ghost{
                background:#fff; border:1px solid #e1e7f0; color:#1d3b66; font-weight:700; border-radius:10px;
            }
            .btn-reset{ background:var(--bnn-gold); color:#1a1200; border:0; font-weight:800; }
            .avatar-preview{ width:88px; height:88px; object-fit:cover; border-radius:50%; border:2px solid #e1e7f0; }
        </style>

        <div class="row">
            {{-- ================= IDENTITAS ================= --}}
            <div class="col-12 bnn-section">
                <span class="section-title"><i class="fas fa-id-card"></i> Identitas Pegawai</span>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nip">NIP <span class="text-danger">*</span></label>
                        <input type="text" id="nip" name="nip" class="form-control"
                               value="{{ $pegawai->nip }}" required
                               inputmode="numeric" pattern="[0-9]*" maxlength="20"
                               placeholder="Masukkan NIP">
                        <small class="hint">Hanya angka, max 20 digit.</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" id="nama" name="nama" class="form-control"
                               value="{{ $pegawai->nama }}" required placeholder="Nama sesuai dokumen">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-control" required>
                            <option value="L" {{ $pegawai->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ $pegawai->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="agama">Agama</label>
                        <input type="text" id="agama" name="agama" class="form-control"
                               value="{{ $pegawai->agama }}" placeholder="Contoh: Islam">
                    </div>
                </div>
            </div>

            {{-- ================= AKUN & KEAMANAN ================= --}}
            <div class="col-12 bnn-section">
                <span class="section-title"><i class="fas fa-shield-alt"></i> Akun & Keamanan</span>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="{{ $pegawai->email }}" required placeholder="nama@instansi.go.id">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="no_tlp">No. HP</label>
                        <input type="text" id="no_tlp" name="no_tlp" class="form-control"
                               value="{{ $pegawai->no_tlp }}" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="usernameEdit">Username <span class="text-danger">*</span></label>
                        <input type="text" id="usernameEdit" name="username" class="form-control"
                               value="{{ $pegawai->username }}" required>
                        <small class="hint">Gunakan huruf kecil & tanpa spasi.</small>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="passwordEdit">Password <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                        <div class="input-group" id="wrapPass">
                            <input type="password" id="passwordEdit" name="password" class="form-control" autocomplete="new-password">
                            <div class="input-group-append">
                                <button type="button" id="btnTogglePass" class="btn btn-ghost" tabindex="-1" title="Lihat/Sembunyikan">
                                    <i class="far fa-eye"></i>
                                </button>
                                <button type="button" id="btnResetPassword" class="btn btn-reset" tabindex="-1" title="Reset = Username">
                                    <i class="fas fa-undo mr-1"></i> Reset
                                </button>
                            </div>
                        </div>
                        <input type="hidden" id="resetPasswordFlag" name="reset_password" value="0">
                        <small class="hint d-block mt-1">Tombol <strong>Reset</strong> akan mengisi password = username.</small>
                        <small class="hint d-block" id="passStrength"></small>
                    </div>
                </div>
            </div>

            {{-- ================= KEDINASAN ================= --}}
            <div class="col-12 bnn-section">
                <span class="section-title"><i class="fas fa-briefcase"></i> Kedinasan</span>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="id_level">Level <span class="text-danger">*</span></label>
                        <select id="id_level" name="id_level" class="form-control" required>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id_level }}" {{ $pegawai->id_level == $level->id_level ? 'selected' : '' }}>
                                    {{ $level->level_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="id_ref_jabatan">Jabatan</label>
                        <select id="id_ref_jabatan" name="id_ref_jabatan" class="form-control">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach ($jabatans as $jabatan)
                                <option value="{{ $jabatan->id_ref_jabatan }}" {{ optional($pegawai->jabatan)->id_ref_jabatan == $jabatan->id_ref_jabatan ? 'selected' : '' }}>
                                    {{ $jabatan->nama_jabatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="tmt_jabatan">TMT Jabatan</label>
                        <input type="date" id="tmt_jabatan" name="tmt_jabatan" class="form-control"
                               value="{{ optional($pegawai->jabatan)->tmt }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="id_ref_pangkat">Pangkat</label>
                        <select id="id_ref_pangkat" name="id_ref_pangkat" class="form-control">
                            <option value="">-- Pilih Pangkat --</option>
                            @foreach ($pangkats as $pangkat)
                                <option value="{{ $pangkat->id_ref_pangkat }}" {{ optional($pegawai->pangkat)->id_ref_pangkat == $pangkat->id_ref_pangkat ? 'selected' : '' }}>
                                    {{ $pangkat->golongan_pangkat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="tmt_pangkat">TMT Pangkat</label>
                        <input type="date" id="tmt_pangkat" name="tmt_pangkat" class="form-control"
                               value="{{ optional($pegawai->pangkat)->tmt }}">
                    </div>

                    {{-- ====== Gaji Pokok (Rp) ====== --}}
                    <div class="form-group col-md-6">
                        <label for="gaji_pokok_display">Gaji Pokok (Rp)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            {{-- Input tampilan (Rupiah terformat) --}}
                            <input
                                type="text"
                                id="gaji_pokok_display"
                                class="form-control @error('gaji_pokok') is-invalid @enderror"
                                inputmode="numeric"
                                placeholder="0"
                                value="{{ $pegawai->gaji_pokok ? number_format((int)$pegawai->gaji_pokok, 0, ',', '.') : '' }}"
                            >
                            {{-- Nilai murni untuk dikirim ke server --}}
                            <input type="hidden" id="gaji_pokok" name="gaji_pokok" value="{{ $pegawai->gaji_pokok }}">
                        </div>
                        <small class="hint">Masukkan angka saja. Contoh: 5.000.000</small>
                        @error('gaji_pokok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- ================= PENDIDIKAN ================= --}}
            <div class="col-12 bnn-section">
                <span class="section-title"><i class="fas fa-graduation-cap"></i> Pendidikan</span>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="jenis_pendidikan">Pendidikan</label>
                        <select id="jenis_pendidikan" name="jenis_pendidikan" class="form-control">
                            <option value="">-- Pilih --</option>
                            @php $jp = optional($pegawai->pendidikan)->jenis_pendidikan; @endphp
                            <option value="SMA" {{ $jp == 'SMA' ? 'selected' : '' }}>SMA</option>
                            <option value="D3"  {{ $jp == 'D3'  ? 'selected' : '' }}>D3</option>
                            <option value="S1"  {{ $jp == 'S1'  ? 'selected' : '' }}>S1</option>
                            <option value="S2"  {{ $jp == 'S2'  ? 'selected' : '' }}>S2</option>
                            <option value="S3"  {{ $jp == 'S3'  ? 'selected' : '' }}>S3</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tahun_kelulusan">Tahun Kelulusan</label>
                        <input type="number" id="tahun_kelulusan" name="tahun_kelulusan" class="form-control"
                               min="1970" max="{{ date('Y') }}"
                               value="{{ optional($pegawai->pendidikan)->tahun_kelulusan }}"
                               placeholder="contoh: {{ date('Y') }}">
                    </div>
                </div>
            </div>

            {{-- ================= FOTO ================= --}}
            <div class="col-12 bnn-section">
                <span class="section-title"><i class="fas fa-camera"></i> Foto Pegawai</span>
                <div class="form-row align-items-center">
                    <div class="form-group col-md-8">
                        <label for="foto">Unggah Foto (PNG/JPG, maks 2MB)</label>
                        <input type="file" id="foto" name="foto" class="form-control-file" accept="image/*">
                        <small class="hint">Gunakan rasio 1:1 (persegi) agar avatar bulat rapi.</small>
                    </div>
                    <div class="form-group col-md-4 text-center">
                        @php
                            $fotoNow = $pegawai->foto ? asset('storage/'.$pegawai->foto) : asset('images/default.png');
                        @endphp
                        <img id="previewFoto" src="{{ $fotoNow }}" alt="Preview foto" class="avatar-preview">
                        <div class="hint mt-2">Pratinjau</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= FOOTER ================= --}}
    <div class="modal-footer" style="background:#f7f9fc;border-top:1px solid #e6edf6;">
        <button type="button" class="btn btn-ghost" data-dismiss="modal" data-bs-dismiss="modal">
            <i class="fas fa-times mr-1"></i> Batal
        </button>
        <button type="submit" class="btn btn-bnn">
            <i class="fas fa-save mr-1"></i> Simpan Perubahan
        </button>
    </div>
</form>

{{-- ================= SCRIPT ================= --}}
@push('scripts')
<script>
(function(){
  // ====== Close button fallback (BS4/BS5/Plain) ======
  document.getElementById('btnCloseEditModal')?.addEventListener('click', function(e){
    const modal = e.target.closest('.modal');
    if (!modal) return;
    try {
      if (window.bootstrap && bootstrap.Modal) { bootstrap.Modal.getOrCreateInstance(modal).hide(); return; }
    } catch(_) {}
    if (window.jQuery && $(modal).modal) { $(modal).modal('hide'); }
    else { modal.style.display='none'; document.body.classList.remove('modal-open'); document.querySelectorAll('.modal-backdrop').forEach(el=>el.remove()); }
  });

  // ====== Tooltip init (BS4/BS5) ======
  try {
    if (window.bootstrap && bootstrap.Tooltip) {
      [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(el => new bootstrap.Tooltip(el, {boundary:'window'}));
    } else if (window.jQuery) {
      $('[data-toggle="tooltip"]').tooltip({ boundary: 'window' });
    }
  } catch(e){}

  // ====== Reset password = username ======
  const $uname = $('#usernameEdit');
  const $pass  = $('#passwordEdit');
  const $flag  = $('#resetPasswordFlag');

  $('#btnResetPassword').on('click', function(){
    const u = ($uname.val() || '').trim();
    if(!u){
      return window.Swal ? Swal.fire({icon:'warning',title:'Username kosong',text:'Isi username dahulu.'})
                         : alert('Username kosong. Isi username dahulu.');
    }
    $pass.val(u);
    $flag.val('1');
    updateStrength();
    if (window.Swal) Swal.fire({icon:'info',title:'Password direset',text:'Password di-set sama dengan username.'});
  });

  // Sinkron jika username diubah setelah reset
  $uname.on('input', function(){ if($flag.val()==='1') { $pass.val(this.value); updateStrength(); } });

  // ====== Toggle show/hide password ======
  $('#btnTogglePass').on('click', function(){
    const type = $pass.attr('type') === 'password' ? 'text' : 'password';
    $pass.attr('type', type);
    $(this).find('i').toggleClass('fa-eye fa-eye-slash');
  });

  // ====== Strength indicator sederhana ======
  function updateStrength(){
    const v = ($pass.val()||'');
    let score = 0;
    if (v.length >= 8) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[a-z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;

    const el = document.getElementById('passStrength');
    const label = ['Sangat Lemah','Lemah','Cukup','Kuat','Sangat Kuat'];
    el.textContent = v ? ('Kekuatan password: ' + label[Math.max(0,score-1)]) : '';
  }
  $('#passwordEdit').on('input', updateStrength);

  // ====== Preview foto ======
  $('#foto').on('change', function(e){
    const file = e.target.files && e.target.files[0];
    if (!file) return;
    if (file.size > 2*1024*1024){ // 2MB
      if (window.Swal) Swal.fire({icon:'error',title:'Ukuran terlalu besar',text:'Maksimum 2MB.'});
      this.value = ''; return;
    }
    const reader = new FileReader();
    reader.onload = (ev)=> $('#previewFoto').attr('src', ev.target.result);
    reader.readAsDataURL(file);
  });

  // ====== Gaji Pokok: sinkronisasi & format Rupiah ======
  (function(){
    const disp = document.getElementById('gaji_pokok_display');
    const real = document.getElementById('gaji_pokok');
    if (!disp || !real) return;

    function fmtIDR(n){
      try { return new Intl.NumberFormat('id-ID').format(n); }
      catch(_) { return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
    }

    function syncFromDisplay(){
      const raw = (disp.value || '').replace(/[^\d]/g, '');
      const clipped = raw.slice(0, 12); // batas 12 digit
      real.value = clipped.length ? parseInt(clipped, 10) : '';
      disp.value = clipped ? fmtIDR(clipped) : '';
    }

    // Inisialisasi agar konsisten jika ada nilai awal
    syncFromDisplay();

    disp.addEventListener('input', syncFromDisplay);
    disp.addEventListener('blur', syncFromDisplay);

    // Pastikan sinkron saat submit
    document.getElementById('formEditPegawai')?.addEventListener('submit', syncFromDisplay);
  })();

  // ====== Validasi ringan sebelum submit ======
  $('#formEditPegawai').on('submit', function(ev){
    // Jika reset aktif, pastikan password ikut terkirim senilai username terkini
    if ($('#resetPasswordFlag').val()==='1') {
      $('#passwordEdit').val( ($('#usernameEdit').val()||'').trim() );
    }
    // Validasi HTML5
    if (this.checkValidity && !this.checkValidity()){
      ev.preventDefault(); ev.stopPropagation();
      $(this).addClass('was-validated');
      return false;
    }
  });

})();
</script>
@endpush
    