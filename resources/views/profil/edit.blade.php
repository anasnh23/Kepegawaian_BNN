@extends('layouts.template')
@section('title', 'Edit Profil')

@section('content')
<style>
/* =========================
   THEME & SCOPING
   ========================= */
:root{
  --bnn-navy:#0a2647;
  --bnn-blue:#144272;
  --bnn-cyan:#2c74b3;
  --bnn-gold:#f4c430;
  --ink:#101828;
  --muted:#667085;
  --ring: rgba(44,116,179,.35);
  --card:#ffffff;
  --soft:#f8fafc;
  --line:#e4e7ec;
}
@media (prefers-color-scheme: dark){
  :root{
    --ink:#e5e7eb; --muted:#9aa4b2; --card:#0b1220; --soft:#0f172a; --line:#1f2937;
  }
}
.edit-profile-page *{ box-sizing:border-box; }
.content-wrapper{ position:relative; z-index:1; } /* supaya tak menutupi sidebar */

/* =========================
   AURORA HERO
   ========================= */
.edit-profile-page .hero{
  position:relative; border-radius:22px; color:#fff;
  background:
    radial-gradient(1200px 500px at 10% -10%, rgba(44,116,179,.35), transparent),
    radial-gradient(900px 420px at 90% 0%, rgba(10,38,71,.35), transparent),
    linear-gradient(135deg, var(--bnn-navy), var(--bnn-blue));
  overflow:hidden; box-shadow:0 20px 60px rgba(16,24,40,.25);
  isolation:isolate;
}
.edit-profile-page .hero:before{
  content:""; position:absolute; inset:0; pointer-events:none;
  background:url("{{ asset('adminlte/dist/img/pattern.svg') }}") center/620px repeat; opacity:.06;
}

/* =========================
   LEFT: AVATAR + UPLOAD
   ========================= */
.edit-profile-page .avatar-wrap{
  position:relative; width:176px; height:176px; margin:auto;
  border-radius:50%; padding:7px;
  background:linear-gradient(180deg, rgba(255,255,255,.25), rgba(255,255,255,.06));
  box-shadow: inset 0 1px 0 rgba(255,255,255,.25), 0 10px 28px rgba(0,0,0,.28);
}
.edit-profile-page .avatar{
  width:100%; height:100%; object-fit:cover; border-radius:50%;
  box-shadow:0 8px 24px rgba(0,0,0,.25);
}
.edit-profile-page .badge-level{
  position:absolute; bottom:-6px; right:-6px;
  background:linear-gradient(135deg, var(--bnn-gold), #ffd76a); color:#3a2a00;
  font-weight:800; font-size:.75rem; padding:6px 10px; border-radius:999px;
  box-shadow:0 6px 16px rgba(0,0,0,.25);
}
.edit-profile-page .camera-btn{
  position:absolute; top:-8px; right:-8px;
  border:2px solid #fff; border-radius:999px; padding:10px 12px; cursor:pointer;
  background:linear-gradient(135deg,#2b6cb0,#2c74b3); color:#fff;
  box-shadow:0 8px 18px rgba(44,116,179,.45);
  transition: transform .12s ease, filter .2s ease;
}
.edit-profile-page .camera-btn:hover{ transform:translateY(-1px) scale(1.03); filter:brightness(1.03); }

.edit-profile-page .dnd{
  border:1.5px dashed rgba(255,255,255,.6); color:#e6f0ff;
  border-radius:14px; padding:10px; margin-top:12px; font-size:.86rem;
  transition:.2s border-color, .2s background;
}
.edit-profile-page .dnd.dragover{ border-color:#fff; background:rgba(255,255,255,.08); }

.edit-profile-page .upload-progress{ height:6px; background:rgba(255,255,255,.25); border-radius:99px; overflow:hidden; margin-top:12px; display:none; }
.edit-profile-page .upload-progress .bar{ width:0%; height:100%; background:linear-gradient(90deg,#6ee7f9,#60a5fa); transition:width .25s ease; }

/* =========================
   RIGHT: CARD + STEPPER
   ========================= */
.edit-profile-page .card-soft{
  background: var(--card);
  border:1px solid rgba(255,255,255,.6); /* tetap tipis saat light */
  border-color: var(--line);
  border-radius:16px; box-shadow:0 10px 30px rgba(16,24,40,.10);
}
.edit-profile-page .stepper{
  display:flex; gap:14px; margin-bottom:18px; align-items:center; flex-wrap:wrap;
}
.edit-profile-page .step{
  display:flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px;
  border:1px dashed #dbe2ea; color:#475569; background:#f8fafc;
}
.edit-profile-page .step i{ color:#0ea5e9; }
.edit-profile-page .step.active{
  border-style:solid; background:#e6f1ff; color:#0a2647; border-color:#b3c7ff; font-weight:700;
}

/* Tabs */
.edit-profile-page .nav-tabs{ border-bottom:1px solid #eef2f6; position:sticky; top:0; z-index:5; background:transparent; }
.edit-profile-page .nav-tabs .nav-link{ border:0; border-bottom:2px solid transparent; color:var(--muted); font-weight:700; }
.edit-profile-page .nav-tabs .nav-link.active{ color:var(--bnn-blue); border-bottom-color:var(--bnn-blue); }

/* Floating label fields */
.edit-profile-page .fl{ position:relative; }
.edit-profile-page .fl input, .edit-profile-page .fl select{
  width:100%; border:1px solid #e4e7ec; border-radius:12px; padding:1.05rem .9rem .55rem;
  background:#fff; color:var(--ink); transition:border-color .15s ease, box-shadow .15s ease;
}
@media (prefers-color-scheme: dark){
  .edit-profile-page .fl input, .edit-profile-page .fl select{ background:#0b1220; }
}
.edit-profile-page .fl input:focus, .edit-profile-page .fl select:focus{
  border-color:var(--bnn-cyan); box-shadow:0 0 0 .18rem var(--ring); outline:0;
}
.edit-profile-page .fl label{
  position:absolute; left:12px; top:12px; padding:0 .35rem; background:var(--card);
  color:#64748b; font-weight:600; font-size:.86rem; transition:all .12s ease; pointer-events:none;
}
.edit-profile-page .fl input:focus + label,
.edit-profile-page .fl input:not(:placeholder-shown) + label,
.edit-profile-page .fl select:focus + label,
.edit-profile-page .fl select:not([value=""]) + label{
  transform:translateY(-14px); font-size:.72rem; color:#0a2647;
}

/* Buttons */
.edit-profile-page .btn-primary{
  background:linear-gradient(135deg, var(--bnn-cyan), #3b82f6);
  border:none; border-radius:12px; box-shadow:0 10px 20px rgba(59,130,246,.25);
  transition: transform .06s ease, filter .2s ease;
}
.edit-profile-page .btn-primary:hover{ transform:translateY(-1px); filter:brightness(1.02); }
.edit-profile-page .btn-back{ background:#101828; color:#fff; border-radius:12px; }
.edit-profile-page .btn-back:hover{ filter:brightness(1.05); }
.edit-profile-page .btn-icon{ display:inline-flex; align-items:center; gap:8px; }

/* Helpers */
.edit-profile-page .mini{ font-size:.9rem; color:var(--muted); }
.edit-profile-page .keyline{ height:1px; background:linear-gradient(90deg, transparent, rgba(17,24,39,.15), transparent); }

/* Password strength bar */
#pwStrength{ transition:width .2s ease; }

/* Tooltip helper */
[data-help]{ position:relative; }
[data-help]:hover::after{
  content: attr(data-help);
  position:absolute; left:0; bottom:calc(100% + 6px);
  background:#0f172a; color:#fff; font-size:.75rem; padding:6px 8px; border-radius:8px; white-space:nowrap;
  box-shadow:0 8px 16px rgba(0,0,0,.25);
}

/* Responsive */
@media (max-width: 992px){ .edit-profile-page .sticky-md-top{ position:static !important; } }
</style>

<div class="container-fluid edit-profile-page">
  <div class="card hero mb-5">
    <div class="card-body p-4 p-md-5">
      <div class="row align-items-start">
        <!-- LEFT -->
        <div class="col-lg-4 text-center mb-4 mb-lg-0">
          <div class="avatar-wrap mx-auto">
            @if ($user->foto)
              <img id="avatarPreview" src="{{ asset('storage/' . $user->foto) }}" class="avatar" alt="Foto Profil">
            @else
              <img id="avatarPreview" src="{{ asset('adminlte/dist/img/avatar.png') }}" class="avatar" alt="Foto Profil">
            @endif
            <span class="badge-level">{{ $user->level->level_name ?? 'Pegawai' }}</span>

            <form id="upload-foto-form" action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <input type="file" name="foto" id="foto-upload" class="d-none" accept="image/*">
              <label for="foto-upload" class="camera-btn" title="Ganti Foto"><i class="fas fa-camera"></i></label>
            </form>
          </div>

          <h4 class="mt-3 mb-0 fw-bold">{{ $user->nama }}</h4>
          <div class="mini mt-1"><i class="fas fa-id-badge mr-1"></i> {{ $user->nip }}</div>
          <div class="mini mt-1"><i class="fas fa-venus-mars mr-1"></i> {{ $user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>

          <div class="dnd mt-3" id="dndZone">Tarik & lepas foto ke sini atau klik ikon kamera di atas.</div>
          <div class="upload-progress" id="uploadProgress"><div class="bar" id="uploadBar"></div></div>
        </div>

        <!-- RIGHT -->
        <div class="col-lg-8">
          <div class="card-soft p-3 p-md-4">
            <!-- Stepper header -->
            <div class="stepper">
              <div class="step active"><i class="fas fa-user-edit"></i> Data Diri</div>
              <div class="step"><i class="fas fa-lock"></i> Keamanan</div>
              <div class="step"><i class="fas fa-check-circle"></i> Selesai</div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-3" id="profilTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="data-tab" data-toggle="tab" href="#data" role="tab"><i class="fas fa-user-edit mr-1"></i> Data Diri</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="keamanan-tab" data-toggle="tab" href="#keamanan" role="tab"><i class="fas fa-lock mr-1"></i> Keamanan</a>
              </li>
            </ul>

            <div class="tab-content" id="profilTabContent">
              {{-- DATA DIRI --}}
              <div class="tab-pane fade show active" id="data" role="tabpanel" aria-labelledby="data-tab">
                <form id="form-data" action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" novalidate>
                  @csrf
                  <div class="form-row">
                    <div class="form-group col-md-6 fl">
                      <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" placeholder=" " required>
                      <label>Nama Lengkap</label>
                    </div>
                    <div class="form-group col-md-6 fl" data-help="NIP tidak dapat diubah">
                      <input type="text" value="{{ $user->nip }}" placeholder=" " readonly>
                      <label>NIP</label>
                    </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group col-md-6 fl">
                      <select name="jenis_kelamin" placeholder=" " required value="{{ $user->jenis_kelamin }}">
                        <option value="" disabled {{ !$user->jenis_kelamin ? 'selected' : '' }}></option>
                        <option value="L" {{ $user->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $user->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                      </select>
                      <label>Jenis Kelamin</label>
                    </div>
                    <div class="form-group col-md-6 fl">
                      <input type="text" name="agama" value="{{ old('agama', $user->agama) }}" placeholder=" ">
                      <label>Agama</label>
                    </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group col-md-6 fl">
                      <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder=" " required>
                      <label>Email</label>
                    </div>
                    <div class="form-group col-md-6 fl">
                      <input type="text" name="no_tlp" value="{{ old('no_tlp', $user->no_tlp) }}" placeholder=" ">
                      <label>No. Telepon</label>
                    </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group col-md-6 fl" data-help="Otomatis dari sistem">
                      <input type="text" value="{{ $user->jabatan->refJabatan->nama_jabatan ?? '-' }}" placeholder=" " readonly>
                      <label>Jabatan</label>
                    </div>
                    <div class="form-group col-md-6 fl" data-help="Otomatis dari sistem">
                      <input type="text" value="{{ $user->pangkat->refPangkat->golongan_pangkat ?? '-' }}" placeholder=" " readonly>
                      <label>Pangkat</label>
                    </div>
                  </div>

                  <div class="text-right">
                    <button id="btn-save-data" type="submit" class="btn btn-primary btn-icon mt-2">
                      <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                      <i class="fas fa-save mr-1"></i> Simpan Data
                    </button>
                  </div>
                </form>
              </div>

              {{-- KEAMANAN --}}
              <div class="tab-pane fade" id="keamanan" role="tabpanel" aria-labelledby="keamanan-tab">
                <form id="form-password" novalidate>
                  @csrf
                  <div class="form-group fl">
                    <input type="password" name="old_password" placeholder=" " required>
                    <label>Password Lama</label>
                    <div class="input-group-append" style="position:absolute; right:8px; top:6px;">
                      <button class="btn btn-light border toggle-eye" type="button" title="Tampilkan/Sembunyikan"><i class="far fa-eye"></i></button>
                    </div>
                  </div>

                  <div class="form-group fl">
                    <input type="password" id="newPassword" name="password" placeholder=" " required>
                    <label>Password Baru</label>
                    <div class="input-group-append" style="position:absolute; right:8px; top:6px;">
                      <button class="btn btn-light border toggle-eye" type="button"><i class="far fa-eye"></i></button>
                    </div>
                    <div class="progress mt-2" style="height:8px;"><div id="pwStrength" class="progress-bar" style="width:0%"></div></div>
                    <small class="text-muted">Gunakan kombinasi huruf besar, kecil, angka, dan simbol.</small>
                    <ul class="mt-2 mb-0 mini" id="pwChecklist">
                      <li data-k="len">≥ 8 karakter</li>
                      <li data-k="mix">Huruf besar & kecil</li>
                      <li data-k="num">Mengandung angka</li>
                      <li data-k="sym">Mengandung simbol</li>
                    </ul>
                  </div>

                  <div class="form-group fl">
                    <input type="password" id="confirmPassword" name="password_confirmation" placeholder=" " required>
                    <label>Konfirmasi Password Baru</label>
                    <div class="input-group-append" style="position:absolute; right:8px; top:6px;">
                      <button class="btn btn-light border toggle-eye" type="button"><i class="far fa-eye"></i></button>
                    </div>
                    <small id="matchHint" class="mini" style="color:#ef4444; display:none;">Konfirmasi belum cocok.</small>
                  </div>

                  <div class="text-right">
                    <button id="btn-save-pass" type="submit" class="btn btn-primary btn-icon mt-2">
                      <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                      <i class="fas fa-key mr-1"></i> Simpan Password
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <div class="keyline my-4"></div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="mini">Versi 1.0 • Sistem Informasi Kepegawaian BNN</span>
              <a href="{{ route('profil.show') }}" class="btn btn-back btn-icon"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
            </div>
          </div>
        </div>
      </div><!-- /row -->
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
/* ===== Sweet Toast ===== */
function toast(icon, title, text=null, timer=1800){
  Swal.fire({icon, title, text, timer, showConfirmButton:false});
}

/* ===== Avatar: preview + drag & drop + progress ===== */
const inputFile = document.getElementById('foto-upload');
const dndZone   = document.getElementById('dndZone');
const avatarImg = document.getElementById('avatarPreview');
const progress  = document.getElementById('uploadProgress');
const bar       = document.getElementById('uploadBar');

['dragenter','dragover'].forEach(ev =>
  dndZone?.addEventListener(ev, e => { e.preventDefault(); dndZone.classList.add('dragover'); })
);
['dragleave','drop'].forEach(ev =>
  dndZone?.addEventListener(ev, e => { e.preventDefault(); dndZone.classList.remove('dragover'); })
);
dndZone?.addEventListener('drop', e => {
  const file = e.dataTransfer.files && e.dataTransfer.files[0];
  if(file && file.type.startsWith('image/')) uploadFoto(file);
});

inputFile?.addEventListener('change', (e) => {
  const file = e.target.files[0]; if(!file) return; uploadFoto(file);
});

function uploadFoto(file){
  const reader = new FileReader();
  reader.onload = e => avatarImg.src = e.target.result;
  reader.readAsDataURL(file);

  const form = document.getElementById('upload-foto-form');
  const formData = new FormData(form);
  formData.set('foto', file);

  progress.style.display = 'block'; bar.style.width = '0%';

  $.ajax({
    url: form.action, method: 'POST', data: formData, processData: false, contentType: false,
    xhr: function(){
      let xhr = new window.XMLHttpRequest();
      xhr.upload.addEventListener('progress', function(evt){
        if (evt.lengthComputable){ bar.style.width = Math.round((evt.loaded/evt.total)*100)+'%'; }
      }, false);
      return xhr;
    },
    success: function(){ toast('success','Foto berhasil diperbarui'); setTimeout(()=>{progress.style.display='none';}, 600); },
    error: function(){ toast('error','Gagal upload foto','Silakan coba lagi'); progress.style.display='none'; }
  });
}

/* ===== Toggle eye for all fields ===== */
$(document).on('click', '.toggle-eye', function(){
  const input = $(this).closest('.fl').find('input');
  const type  = input.attr('type') === 'password' ? 'text' : 'password';
  input.attr('type', type);
  $(this).find('i').toggleClass('fa-eye fa-eye-slash');
});

/* ===== Password scoring + checklist ===== */
function scorePassword(pw){
  let score = 0; if(!pw) return score;
  const letters = {};
  for(let i=0;i<pw.length;i++){ letters[pw[i]] = (letters[pw[i]]||0)+1; score += 5.0/letters[pw[i]]; }
  const varis = { digits:/\d/.test(pw), lower:/[a-z]/.test(pw), upper:/[A-Z]/.test(pw), sym:/\W/.test(pw) };
  let count = 0; for (let k in varis){ count += varis[k]?1:0; }
  score += (count-1) * 10; return parseInt(score);
}
function checklist(pw){
  const ok = {
    len: (pw||'').length >= 8,
    mix: /[a-z]/.test(pw) && /[A-Z]/.test(pw),
    num: /\d/.test(pw),
    sym: /\W/.test(pw)
  };
  Object.keys(ok).forEach(k=>{
    $('#pwChecklist [data-k="'+k+'"]').css('color', ok[k] ? '#16a34a' : '#667085')
                                      .css('text-decoration', ok[k] ? 'line-through' : 'none');
  });
}
$('#newPassword').on('input', function(){
  const val = this.value;
  const s = scorePassword(val);
  const pct = Math.max(10, Math.min(100, s));
  const bar = $('#pwStrength');
  bar.css('width', pct+'%').removeClass('bg-danger bg-warning bg-success')
     .addClass(pct<40 ? 'bg-danger' : pct<70 ? 'bg-warning' : 'bg-success');
  checklist(val);
});

/* ===== Match hint ===== */
$('#confirmPassword, #newPassword').on('input', function(){
  const show = $('#confirmPassword').val() && ($('#confirmPassword').val() !== $('#newPassword').val());
  $('#matchHint').toggle(show);
});

/* ===== Prevent multi submit + spinner ===== */
function withSpinner($btn, doing=true){
  $btn.prop('disabled', doing);
  $btn.find('.spinner-border').toggleClass('d-none', !doing);
}

/* ===== Submit DATA DIRI (native post, optional ajax) ===== */
$('#form-data').on('submit', function(){
  withSpinner($('#btn-save-data'), true);
});

/* ===== AJAX submit ubah password ===== */
$('#form-password').on('submit', function(e){
  e.preventDefault();
  if($('#confirmPassword').val() !== $('#newPassword').val()){
    toast('error','Konfirmasi password belum cocok');
    return;
  }
  const $btn = $('#btn-save-pass'); withSpinner($btn, true);
  $.ajax({
    url: "{{ route('profil.updatePassword') }}",
    type: 'POST',
    data: $(this).serialize(),
    success: function(res){
      toast('success','Berhasil', res.message);
      $('#form-password')[0].reset();
      $('#pwStrength').css('width','0%').removeClass('bg-danger bg-warning bg-success');
      $('#matchHint').hide(); checklist('');
    },
    error: function(xhr){
      if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message){
        toast('error','Gagal', xhr.responseJSON.message);
      } else {
        toast('error','Terjadi kesalahan','Silakan coba lagi.');
      }
    },
    complete: function(){ withSpinner($btn, false); }
  });
});
</script>
@endpush
