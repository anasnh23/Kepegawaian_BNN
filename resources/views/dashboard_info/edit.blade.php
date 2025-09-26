{{-- ====== EDIT FORM SAJA (tanpa extends/sidebar) ====== --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <title>Edit Informasi</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --bnn-navy:#003366; --bnn-navy-2:#0b2f5e; --bnn-blue:#144272; --bnn-gold:#f0ad4e;
      --ink:#0f172a; --muted:#6b7280; --soft:#f6f9ff; --line:#e6edf6;
    }
    body{ background:linear-gradient(180deg,#f0f5ff 0,#ffffff 30%); color:var(--ink); }
    .wrap{ max-width:1100px; margin:32px auto; padding:0 12px; }
    .hero{
      background:linear-gradient(135deg,var(--bnn-navy),#022a5d 55%,var(--bnn-navy-2));
      color:#fff;border-radius:18px;padding:18px 20px;position:relative;overflow:hidden;
      box-shadow:0 16px 42px rgba(0,33,72,.24);
    }
    .hero::after{
      content:"";position:absolute;right:-60px;top:-60px;width:240px;height:240px;opacity:.08;
      background:url('{{ asset('images/bnn-watermark.svg') }}') no-repeat center/contain;
    }
    .card-premium{
      backdrop-filter:saturate(160%) blur(6px);
      background:linear-gradient( to bottom right, rgba(255,255,255,.94), rgba(255,255,255,.98) );
      border:1px solid var(--line);
      border-radius:16px; overflow:hidden; box-shadow:0 14px 44px rgba(16,24,40,.10);
    }
    .card-premium .card-header{
      background:linear-gradient(135deg,#f7faff,#eef5ff); border-bottom:1px solid var(--line);
      font-weight:800; color:#123a6b;
    }
    .req::after{ content:" *"; color:#dc3545; font-weight:700; }
    .hint{ color:var(--muted); font-size:.86rem; }

    /* Toolbar */
    .editor-toolbar{
      background:var(--soft); border:1px solid var(--line); border-radius:12px; padding:8px;
      display:flex; flex-wrap:wrap; gap:8px; align-items:center;
    }
    .toolbar-group{ display:flex; gap:6px; align-items:center; }
    .tb-btn{
      border:1px solid var(--line); background:#fff; border-radius:10px; padding:6px 10px; cursor:pointer;
    }
    .tb-btn.active{ outline:2px solid var(--bnn-gold); }
    .tb-sel{
      border:1px solid var(--line); background:#fff; border-radius:10px; padding:6px 10px;
    }
    .color-input{ width:36px; height:36px; border-radius:10px; border:1px solid var(--line); padding:0; }

    /* Editor area */
    .editor{
      border:1px solid var(--line); border-radius:12px; padding:14px; min-height:220px; background:#fff;
      outline:none;
    }
    .editor:focus{ box-shadow:0 0 0 .18rem rgba(0,51,102,.12); border-color:#cfe1ff; }

    .btn-bnn{
      background:linear-gradient(135deg,var(--bnn-gold),#ffd667);
      color:#1f2937; border:0; font-weight:800; border-radius:12px;
      box-shadow:0 10px 20px rgba(244,196,48,.28);
    }
    .btn-bnn:hover{ filter:brightness(1.02); color:#1f2937; }
  </style>
</head>
<body>
  <div class="wrap">

    {{-- Hero --}}
    <div class="hero mb-3 d-flex align-items-center justify-content-between">
      <div>
        <h4 class="m-0 fw-bold"><i class="fas fa-pen-to-square me-2"></i>Edit Informasi Dashboard</h4>
        <div class="opacity-75 small mt-1">Form mandiri tanpa sidebar, nuansa BNN.</div>
      </div>
      <a href="{{ route('dashboard-info.index') }}" class="btn btn-outline-light btn-sm rounded-3">
        <i class="fas fa-arrow-left me-1"></i>Kembali
      </a>
    </div>

    {{-- FORM --}}
    <form id="formEditInfo" action="{{ route('dashboard-info.update', $row->id) }}" method="post" class="card card-premium">
      @csrf @method('PUT')

      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-edit me-2"></i>Form Edit</span>
      </div>

      <div class="card-body">
        {{-- Error list --}}
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
          </div>
        @endif

        <div class="row g-3">
          <div class="col-lg-8">
            <label class="form-label req">Judul</label>
            <input type="text" name="judul" value="{{ old('judul', $row->judul) }}" class="form-control rounded-3" maxlength="150" required>
            <div class="hint mt-1">Maksimal 150 karakter.</div>
          </div>

          <div class="col-lg-4">
            <label class="form-label req">Target</label>
            @php $t = old('target', $row->target); @endphp
            <select name="target" class="form-control rounded-3" required>
              <option value="pegawai"  {{ $t=='pegawai'?'selected':'' }}>Pegawai</option>
              <option value="pimpinan" {{ $t=='pimpinan'?'selected':'' }}>Pimpinan</option>
              <option value="semua"    {{ $t=='semua'?'selected':'' }}>Semua</option>
            </select>
            <div class="hint mt-1">Pilih penerima informasi.</div>
          </div>

          {{-- ===== Toolbar Manual ===== --}}
          <div class="col-12">
            <label class="form-label req">Konten</label>

            <div class="editor-toolbar mb-2" id="toolbar">
              <div class="toolbar-group">
                <button type="button" class="tb-btn" data-cmd="bold"><i class="fas fa-bold"></i></button>
                <button type="button" class="tb-btn" data-cmd="italic"><i class="fas fa-italic"></i></button>
                <button type="button" class="tb-btn" data-cmd="underline"><i class="fas fa-underline"></i></button>
                <button type="button" class="tb-btn" data-cmd="removeFormat" title="Clear"><i class="fas fa-eraser"></i></button>
              </div>

              <div class="toolbar-group">
                <select class="tb-sel" id="fontSizeSel" title="Ukuran (px)">
                  <option value="">Ukuran</option>
                  <option>12</option><option>14</option><option>16</option><option>18</option>
                  <option>20</option><option>24</option><option>28</option><option>32</option>
                </select>

                <select class="tb-sel" id="fontWeightSel" title="Weight">
                  <option value="">Weight</option>
                  <option value="300">Light</option>
                  <option value="400">Normal</option>
                  <option value="600">SemiBold</option>
                  <option value="700">Bold</option>
                </select>

                <input type="color" class="color-input" id="foreColorInput" title="Warna teks">
              </div>

              <div class="toolbar-group">
                <button type="button" class="tb-btn" data-cmd="justifyLeft"  title="Kiri"><i class="fas fa-align-left"></i></button>
                <button type="button" class="tb-btn" data-cmd="justifyCenter" title="Tengah"><i class="fas fa-align-center"></i></button>
                <button type="button" class="tb-btn" data-cmd="justifyRight" title="Kanan"><i class="fas fa-align-right"></i></button>
                <button type="button" class="tb-btn" data-cmd="justifyFull"  title="Rata kiri-kanan"><i class="fas fa-align-justify"></i></button>
              </div>

              <div class="toolbar-group">
                <button type="button" class="tb-btn" data-cmd="insertUnorderedList" title="Bullet"><i class="fas fa-list-ul"></i></button>
                <button type="button" class="tb-btn" data-cmd="insertOrderedList"   title="Number"><i class="fas fa-list-ol"></i></button>
                <button type="button" class="tb-btn" id="linkBtn" title="Link"><i class="fas fa-link"></i></button>
              </div>
            </div>

            <div id="editor" class="editor" contenteditable="true">{!! old('konten', $row->konten) !!}</div>
            <input type="hidden" name="konten" id="konten">
            <div class="hint mt-1">Pilih teks lalu atur gaya (Bold/Italic/Ukuran/Align, dll).</div>
          </div>

          {{-- Urutan selalu terlihat --}}
          <div class="col-md-3">
            <label class="form-label">Urutan</label>
            <input type="number" name="urutan" value="{{ old('urutan', $row->urutan ?? 0) }}" class="form-control rounded-3" min="0">
            <div class="hint mt-1">Angka kecil tampil lebih atas.</div>
          </div>

          {{-- Toggle Selamanya --}}
          @php
            $mulaiOld = old('mulai', $row->mulai);
            $selesaiOld = old('selesai', $row->selesai);
            $isSelamanya = old('selamanya', ($mulaiOld=='' && $selesaiOld=='')); // bila dua-duanya kosong -> selamanya
          @endphp

          <div class="col-12">
            <input type="hidden" name="selamanya" value="0">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="selamanya" name="selamanya" value="1" {{ $isSelamanya ? 'checked' : '' }}>
              <label class="form-check-label fw-semibold" for="selamanya">Tampilkan selamanya (tanpa tanggal mulai & selesai)</label>
            </div>
            <div class="hint">Jika dinonaktifkan, silakan isi periode berlaku di bawah.</div>
          </div>

          <div id="rangeTanggal" class="row g-3 mx-0 px-0 {{ $isSelamanya ? 'd-none':'' }}">
            <div class="col-md-4">
              <label class="form-label">Mulai</label>
              <input type="date" name="mulai" value="{{ $mulaiOld }}" class="form-control rounded-3" id="mulaiField">
            </div>

            <div class="col-md-4">
              <label class="form-label">Selesai</label>
              <input type="date" name="selesai" value="{{ $selesaiOld }}" class="form-control rounded-3" id="selesaiField">
            </div>
          </div>

          <div class="col-md-3 d-flex align-items-end">
            {{-- boolean fix: kirim 0 saat unchecked --}}
            <input type="hidden" name="aktif" value="0">
            <div class="form-check">
              <input type="checkbox" name="aktif" id="aktif" class="form-check-input" value="1" {{ old('aktif', $row->aktif)?'checked':'' }}>
              <label class="form-check-label" for="aktif">Aktif</label>
            </div>
          </div>
        </div>
      </div>

      <div class="card-footer d-flex justify-content-between align-items-center">
        <a href="{{ route('dashboard-info.index') }}" class="btn btn-outline-secondary rounded-3">
          <i class="fas fa-times me-1"></i>Batal
        </a>
        <button type="submit" class="btn btn-bnn rounded-3">
          <i class="fas fa-save me-1"></i>Update
        </button>
      </div>
    </form>
  </div>

  <script src="https://kit.fontawesome.com/a2e0da9c6d.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const editor = document.getElementById('editor');

    function exec(cmd, val=null){
      document.execCommand(cmd, false, val);
      editor.focus();
    }

    // Basic buttons
    document.querySelectorAll('.tb-btn[data-cmd]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        exec(btn.getAttribute('data-cmd'));
      });
    });

    // Font size (px) -> bungkus selection dengan span style
    document.getElementById('fontSizeSel').addEventListener('change', function(){
      const px = this.value; if(!px) return;
      wrapSelectionWithStyle(`font-size:${px}px`);
      this.value = '';
    });

    // Font weight
    document.getElementById('fontWeightSel').addEventListener('change', function(){
      const w = this.value; if(!w) return;
      wrapSelectionWithStyle(`font-weight:${w}`);
      this.value = '';
    });

    // Fore color
    document.getElementById('foreColorInput').addEventListener('input', function(){
      exec('foreColor', this.value);
    });

    // Link
    document.getElementById('linkBtn').addEventListener('click', function(){
      const url = prompt('Masukkan URL (contoh: https://contoh.go.id)');
      if(url){ exec('createLink', url); }
    });

    // Helper: bungkus selection dengan <span style="...">
    function wrapSelectionWithStyle(styleStr){
      const sel = window.getSelection();
      if(!sel.rangeCount) return;
      const range = sel.getRangeAt(0);
      if(range.collapsed) return;
      const span = document.createElement('span');
      span.setAttribute('style', styleStr);
      try { range.surroundContents(span); }
      catch(e){
        document.execCommand('insertHTML', false, `<span style="${styleStr}">${range.toString()}</span>`);
      }
      editor.focus();
    }

    // Toggle "Selamanya"
    const selamanya = document.getElementById('selamanya');
    const rangeTanggal = document.getElementById('rangeTanggal');
    const mulaiEl = document.getElementById('mulaiField');
    const selesaiEl = document.getElementById('selesaiField');

    function applySelamanya(){
      const on = selamanya.checked;
      if(on){
        rangeTanggal.classList.add('d-none');
        if(mulaiEl){ mulaiEl.value = ''; mulaiEl.disabled = true; }
        if(selesaiEl){ selesaiEl.value = ''; selesaiEl.disabled = true; }
      }else{
        rangeTanggal.classList.remove('d-none');
        if(mulaiEl){ mulaiEl.disabled = false; }
        if(selesaiEl){ selesaiEl.disabled = false; }
      }
    }
    selamanya.addEventListener('change', applySelamanya);
    applySelamanya();

    // Submit: kirim HTML editor ke input hidden "konten" + validasi tanggal (jika perlu)
    document.getElementById('formEditInfo').addEventListener('submit', function(e){
      if(!selamanya.checked && mulaiEl && selesaiEl && mulaiEl.value && selesaiEl.value && selesaiEl.value < mulaiEl.value){
        e.preventDefault();
        alert('Tanggal selesai tidak boleh sebelum tanggal mulai.');
        return;
      }
      document.getElementById('konten').value = editor.innerHTML;
    });
  </script>
</body>
</html>
