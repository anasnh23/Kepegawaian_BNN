{{-- ====================== CREATE: Tambah Pegawai ====================== --}}
<div class="modal-header" style="background:#0a2647;color:#fff;border:0;">
  <h5 class="modal-title font-weight-bold">
    <i class="fas fa-user-plus mr-2"></i> Tambah Data Pegawai
  </h5>
  <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<form id="formCreatePegawai" action="{{ route('pegawai.store') }}" method="POST" enctype="multipart/form-data" novalidate>
  @csrf

  <style>
    :root{ --bnn-navy:#0a2647; --bnn-blue:#144272; --bnn-cyan:#2c74b3; --bnn-gold:#f4c430; --ink:#0f172a; --muted:#64748b; }
    .bnn-section{ background:#f8fafc; border:1px solid #e6edf6; border-radius:14px; padding:16px 16px 8px; box-shadow:0 10px 24px rgba(16,24,40,.06); margin-bottom:14px; }
    .bnn-title{ display:inline-flex; align-items:center; gap:10px; margin:-10px 0 10px; padding:6px 12px; background:#e7eef9; color:var(--bnn-navy); border-radius:10px; font-weight:800; letter-spacing:.3px; }
    .form-group{ margin-bottom: .9rem; }
    .form-control, .custom-select{ height:44px; padding:.55rem .9rem; font-size:.95rem; line-height:1.25; border-radius:10px; border:1px solid #dbe3ef; box-shadow:none; background:#fff; color:#0f172a; }
    .form-control:focus, .custom-select:focus{ border-color:var(--bnn-cyan); box-shadow:0 0 0 .18rem rgba(44,116,179,.20); }
    .form-control::placeholder{ color:#9aa7b5; }
    label{ font-weight:700; color:#0b1b31; }
    .hint{ color:var(--muted); font-size:.85rem; }
    .dropzone{ background:#0f1b2a; color:#e5e7eb; border:1.5px dashed #35527b; border-radius:14px; min-height:260px; display:flex; align-items:center; justify-content:center; text-align:center; position:relative; }
    .dropzone.dragover{ background:#0d2033; border-color:#22d3ee; }
    .dz-choose{ position:absolute; bottom:14px; left:50%; transform:translateX(-50%); }
    .preview{ position:absolute; inset:12px; border-radius:12px; overflow:hidden; display:none; }
    .preview img{ width:100%; height:100%; object-fit:cover; }
    .pw-meter{ height:8px; border-radius:10px; background:#e6edf6; overflow:hidden; }
    .pw-meter > div{ height:100%; width:0%; transition:width .2s; }
    .btn-soft{ border-radius:10px; padding:.6rem 1rem; font-weight:800; }
    .btn-bnn{ background:linear-gradient(135deg, var(--bnn-cyan), #3b82f6); color:#fff; border:0; }
    .btn-bnn:hover{ filter:brightness(1.03); }
  </style>

  <div class="modal-body" style="background:#f2f6fb;">
    <div class="row">
      {{-- ===== Kolom foto ===== --}}
      <div class="col-lg-4 mb-3">
        <div class="bnn-section" style="background:#0b1522;border-color:#1d2a44;color:#e5e7eb;">
          <span class="bnn-title" style="background:#193153;color:#fff;">
            <i class="fas fa-camera"></i> Foto Profil
          </span>

          <div id="dropArea" class="dropzone mb-2">
            <div class="text-center">
              <div class="mb-2" style="font-size:36px;opacity:.9;"><i class="fas fa-image"></i></div>
              <div class="mb-1">Tarik & taruh foto di sini</div>
              <div class="hint mb-3">atau klik tombol Pilih</div>
              <button type="button" class="btn btn-info btn-soft dz-choose" onclick="document.getElementById('foto').click()">
                <i class="fas fa-upload mr-1"></i> Pilih
              </button>
            </div>
            <div class="preview" id="previewBox">
              <img id="previewImg" alt="Preview foto">
            </div>
          </div>
          <input type="file" id="foto" name="foto" accept="image/*" class="d-none">
          <div class="hint">Format .jpg/.png, maks 2MB. Rasio 1:1 disarankan.</div>
        </div>
      </div>

      {{-- ===== Kolom form ===== --}}
      <div class="col-lg-8">
        {{-- Identitas Pegawai --}}
        <div class="bnn-section">
          <span class="bnn-title"><i class="fas fa-id-card"></i> Identitas Pegawai</span>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>NIP <span class="text-danger">*</span></label>
              <input type="text" name="nip" id="nip" class="form-control" required placeholder="Masukkan NIP">
            </div>
            <div class="form-group col-md-6">
              <label>Nama Lengkap <span class="text-danger">*</span></label>
              <input type="text" name="nama" class="form-control" required placeholder="Masukkan Nama">
            </div>
            <div class="form-group col-md-6">
              <label>Jenis Kelamin <span class="text-danger">*</span></label>
              <select name="jenis_kelamin" class="custom-select" required>
                <option value="">— Pilih —</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Agama</label>
              <input type="text" name="agama" class="form-control" placeholder="contoh: Islam">
            </div>
          </div>
        </div>

        {{-- Kontak Pegawai --}}
        <div class="bnn-section">
          <span class="bnn-title"><i class="fas fa-address-book"></i> Kontak Pegawai</span>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control" required placeholder="nama@instansi.go.id">
            </div>
            <div class="form-group col-md-6">
              <label>No. HP</label>
              <input type="text" name="no_tlp" id="no_tlp" class="form-control" placeholder="08xxxxxxxxxx">
            </div>
            <div class="form-group col-md-6">
              <label>Username</label>
              <input type="text" name="username" class="form-control" placeholder="opsional">
            </div>
            <div class="form-group col-md-6">
              <label>Password <span class="text-danger">*</span></label>
              <input type="password" name="password" id="password" class="form-control" required placeholder="Minimal 8 karakter">
              <div class="pw-meter mt-2"><div id="pwBar" style="background:#ef4444"></div></div>
              <div class="hint">Gunakan huruf besar, kecil, angka, dan simbol.</div>
            </div>
          </div>
        </div>

        {{-- Data Diri / Struktural Pegawai --}}
        <div class="bnn-section">
          <span class="bnn-title"><i class="fas fa-briefcase"></i> Data Diri Pegawai</span>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Level <span class="text-danger">*</span></label>
              <select name="id_level" class="custom-select" required>
                <option value="">— Pilih Level —</option>
                @foreach ($levels as $level)
                  <option value="{{ $level->id_level }}">{{ $level->level_name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Jabatan</label>
              <select name="id_ref_jabatan" class="custom-select">
                <option value="">— Pilih Jabatan —</option>
                @foreach ($jabatans as $jabatan)
                  <option value="{{ $jabatan->id_ref_jabatan }}">{{ $jabatan->nama_jabatan }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group col-md-6">
              <label>TMT Jabatan</label>
              <input type="date" name="tmt_jabatan" class="form-control">
            </div>

            <div class="form-group col-md-6">
              <label>Pangkat</label>
              <select name="id_ref_pangkat" class="custom-select">
                <option value="">— Pilih Pangkat —</option>
                @foreach ($pangkats as $pangkat)
                  <option value="{{ $pangkat->id_ref_pangkat }}">{{ $pangkat->golongan_pangkat }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group col-md-6">
              <label>TMT Pangkat</label>
              <input type="date" name="tmt_pangkat" class="form-control">
            </div>

            {{-- ====== Gaji Pokok (baru) ====== --}}
            <div class="form-group col-md-6">
              <label>Gaji Pokok (Rp)</label>
              <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                <input type="number" name="gaji_pokok" id="gaji_pokok" class="form-control" min="0" step="1000" placeholder="0">
              </div>
              <small class="hint">Isi nominal gaji pokok saat ini. Bisa dikosongkan jika belum ditetapkan.</small>
            </div>

            <div class="form-group col-md-6">
              <label>Pendidikan</label>
              <select name="jenis_pendidikan" class="custom-select">
                <option value="">— Pilih —</option>
                <option value="SMA">SMA</option>
                <option value="S1">S1</option>
                <option value="S2">S2</option>
                <option value="S3">S3</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Tahun Kelulusan</label>
              <input type="number" name="tahun_kelulusan" class="form-control" min="1970" max="{{ date('Y') }}" placeholder="YYYY">
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="modal-footer" style="background:#0a2647;border:0;">
    <button type="button" class="btn btn-secondary btn-soft" data-dismiss="modal">
      <i class="fas fa-times mr-1"></i> Batal
    </button>
    <button type="submit" class="btn btn-bnn btn-soft">
      <i class="fas fa-save mr-1"></i> Simpan
    </button>
  </div>
</form>

@push('scripts')
<script>
  /* ==== BESARKAN MODAL KE XL ==== */
  (function(){
    const $dlg = $('#modalFormPegawai .modal-dialog');
    if ($dlg.length) $dlg.removeClass('modal-sm modal-md modal-lg').addClass('modal-xl');
    $('#modalFormPegawai').one('hidden.bs.modal', function(){
      $('#modalFormPegawai .modal-dialog').removeClass('modal-xl').addClass('modal-lg');
    });
  })();

  // ====== Drag & Drop Foto + Preview ======
  (function(){
    const dropArea = document.getElementById('dropArea');
    const fileInput = document.getElementById('foto');
    const previewBox = document.getElementById('previewBox');
    const previewImg = document.getElementById('previewImg');
    if(!dropArea) return;

    ['dragenter','dragover'].forEach(evt =>
      dropArea.addEventListener(evt, e => { e.preventDefault(); e.stopPropagation(); dropArea.classList.add('dragover'); })
    );
    ['dragleave','drop'].forEach(evt =>
      dropArea.addEventListener(evt, e => { e.preventDefault(); e.stopPropagation(); dropArea.classList.remove('dragover'); })
    );
    dropArea.addEventListener('drop', e => {
      const f = e.dataTransfer.files?.[0]; if(!f) return;
      if(!f.type.startsWith('image/')) return alert('File harus gambar.');
      fileInput.files = e.dataTransfer.files; showPreview(f);
    });
    fileInput.addEventListener('change', e => {
      const f = e.target.files?.[0]; if(f) showPreview(f);
    });
    function showPreview(file){
      const r = new FileReader();
      r.onload = e => { previewImg.src = e.target.result; previewBox.style.display='block'; };
      r.readAsDataURL(file);
    }
  })();

  // ====== Mask sederhana ======
  $('#nip').on('input', function(){ this.value = this.value.replace(/[^0-9]/g,'').slice(0,18); });
  $('#no_tlp').on('input', function(){ this.value = this.value.replace(/[^0-9]/g,'').slice(0,15); });

  // ====== Password strength ======
  (function(){
    const pw = document.getElementById('password'), bar = document.getElementById('pwBar');
    if(!pw) return;
    pw.addEventListener('input', ()=>{
      const s = scorePassword(pw.value);
      bar.style.width = Math.max(10, s)+'%';
      bar.style.background = s<40 ? '#ef4444' : s<70 ? '#f59e0b' : '#22c55e';
    });
    function scorePassword(p){
      if(!p) return 0;
      let score=0, map={}; for(let c of p){ map[c]=(map[c]||0)+1; score+= 5/map[c]; }
      const v=[/\d/,/[a-z]/,[A-Z]/,/[\W_]/].reduce((a,rx)=>a+(rx.test(p)?1:0),0);
      return Math.min(100, Math.round(score + (v-1)*10));
    }
  })();

  // ====== Reset modal ======
  $('#modalFormPegawai').on('show.bs.modal', function(){
    const $f = $('#formCreatePegawai'); if(!$f.length) return;
    $f[0].reset(); $('.is-invalid').removeClass('is-invalid');
    $('#pwBar').css('width','0%');
    if (!$('#foto')[0].files.length) { $('#previewBox').hide(); $('#previewImg').attr('src',''); }
  });
</script>
@endpush
