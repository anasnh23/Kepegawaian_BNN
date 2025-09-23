@extends('layouts.template')
@section('title','Cuti Pegawai')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@php
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\DB;
  use App\Helpers\MasaKerja;

  $user   = Auth::user();
  $tahun  = now()->year;

  // ==== HAK CUTI TAHUNAN DEFAULT (bisa di-override dari controller) ====
  $hakCuti = isset($hakCuti) ? (int)$hakCuti : (int) config('app.jatah_cuti_tahunan', 12);

  // ==== MASA KERJA: SAMA DENGAN CONTROLLER (pakai helper) ====
  $mkYears = (int) MasaKerja::years($user->id_user);

  // Normalisasi gender (sama seperti di controller.store)
  $jkRaw    = strtolower(trim((string) $user->jenis_kelamin));
  $isFemale = in_array($jkRaw, ['p','perempuan','female','wanita'], true);

  // ==== TERPAKAI (HANYA yang disetujui pimpinan) ====
  $terpakaiApproved = DB::table('cuti')
      ->leftJoin('approval_pimpinan','approval_pimpinan.id_cuti','=','cuti.id_cuti')
      ->where('cuti.id_user', $user->id_user)
      ->whereYear('cuti.tanggal_mulai', $tahun)
      ->where('approval_pimpinan.status', 'Disetujui')
      ->select('cuti.jenis_cuti', DB::raw('SUM(DATEDIFF(cuti.tanggal_selesai, cuti.tanggal_mulai) + 1) as hari'))
      ->groupBy('cuti.jenis_cuti')
      ->pluck('hari','jenis_cuti');

  $approved = collect($terpakaiApproved)->map(fn($v)=>(int)$v);

  // Angka hero (Tahunan + Bersama)
  $terpakaiTahunIni = (int)($approved['Tahunan'] ?? 0) + (int)($approved['Bersama'] ?? 0);
  $sisaCuti         = max(0, $hakCuti - $terpakaiTahunIni);

  // ==== Jatah per-jenis ====
  $jatahDefault = [
    'Tahunan'                  => $hakCuti,
    'Besar'                    => 60,
    'Sakit'                    => 0,
    'Melahirkan'               => 90,
    'Penting'                  => 30,
    'Luar Tanggungan Negara'   => 1095,
  ];

  // Terpakai per-jenis (approved)
  $terpakaiPerJenis = [
    'Tahunan'                  => (int)($approved['Tahunan'] ?? 0) + (int)($approved['Bersama'] ?? 0),
    'Besar'                    => (int)($approved['Besar'] ?? 0),
    'Sakit'                    => (int)($approved['Sakit'] ?? 0),
    'Melahirkan'               => (int)($approved['Melahirkan'] ?? 0),
    'Penting'                  => (int)($approved['Penting'] ?? 0),
    'Luar Tanggungan Negara'   => (int)($approved['Luar Tanggungan Negara'] ?? 0),
  ];

  // Kelayakan per-jenis — LOGIKA DIPERSIS DENGAN CONTROLLER
  $eligible = [
    'Tahunan'                  => $mkYears >= 1,
    'Besar'                    => $mkYears >= 5,
    'Sakit'                    => true,
    'Melahirkan'               => $isFemale,
    'Penting'                  => true,
    'Luar Tanggungan Negara'   => $mkYears >= 5,
  ];

  // Syarat / catatan
  $reason = [
    'Tahunan'                  => 'Syarat: masa kerja ≥ 1 tahun',
    'Besar'                    => 'Syarat: masa kerja ≥ 5 tahun',
    'Sakit'                    => 'Tanpa syarat khusus',
    'Melahirkan'               => 'Khusus pegawai perempuan',
    'Penting'                  => 'Tanpa syarat khusus',
    'Luar Tanggungan Negara'   => 'Syarat: masa kerja ≥ 5 tahun',
  ];

  // Susun tabel
  $rows = [];
  foreach ($terpakaiPerJenis as $jenis => $terpakai) {
      $jatah = (int) ($jatahDefault[$jenis] ?? 0);
      $sisa  = max(0, $jatah - $terpakai);

      // label status
      if ($sisa <= 0 && $jatah > 0) {
          $status = ['label'=>'Habis', 'class'=>'badge-secondary'];
      } else {
          $status = $eligible[$jenis]
              ? ['label'=>'Layak', 'class'=>'badge-success']
              : ['label'=>'Belum Memenuhi', 'class'=>'badge-warning'];
      }

      $rows[] = compact('jenis','jatah','terpakai','sisa','status');
  }
@endphp

<style>
  :root{
    --bnn-navy:#0a2a4a; --bnn-navy-2:#0f3a6a; --bnn-gold:#f0ad4e; --bnn-green:#198754;
    --bnn-sky:#eaf2ff; --ink:#0f172a; --line:#d5e2f3;
  }
  body{ background:linear-gradient(180deg,#f9fbff 0%, #f3f7fc 100%); }
  .hero{
    background:linear-gradient(120deg,var(--bnn-navy),var(--bnn-navy-2));
    color:#fff; border-radius:18px; padding:18px 22px; position:relative; overflow:hidden;
    box-shadow:0 12px 28px rgba(10,42,74,.25);
  }
  .hero::after{ content:""; position:absolute; right:-60px; top:-60px; width:200px; height:200px; opacity:.08;
    background:url('{{ asset('images/bnn-watermark.svg') }}') no-repeat center/contain; }
  .chip{display:inline-flex;align-items:center;gap:6px;border:1px solid rgba(255,255,255,.35);
        padding:.25rem .7rem;border-radius:999px;background:rgba(255,255,255,.15);font-weight:700}
  .stat{display:flex; gap:14px; align-items:center; padding:12px 14px; border-radius:14px;
        background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.25); min-width:180px;}
  .stat .v{ font-weight:900; font-size:1.3rem; } .stat .l{ font-weight:700; opacity:.9; }

  .bnn-card{ border:1px solid var(--line); border-radius:16px; box-shadow:0 10px 22px rgba(16,24,40,.08); }
  .bnn-card .card-header{
    background:var(--bnn-navy); color:#fff; border-bottom:3px solid var(--bnn-gold);
    border-radius:16px 16px 0 0; font-weight:800;
  }

  /* ===== TABEL TEBAL & TEGAS ===== */
  .table-bnn{
    border:2px solid #99b3d7;
    border-collapse:separate;
    border-spacing:0;
    overflow:hidden;
    border-radius:12px;
  }
  .table-bnn thead th{
    background:#0f1f39; color:#eaf2ff; border-top:0; border-bottom:2px solid #99b3d7; font-weight:800;
  }
  .table-bnn th, .table-bnn td{
    border-right:2px solid #99b3d7;
    border-bottom:2px solid #c9d8ef;
    vertical-align:middle;
  }
  .table-bnn th:last-child, .table-bnn td:last-child{ border-right:0; }
  .table-bnn tr:last-child td{ border-bottom:0; }
  .table-bnn tbody tr:nth-child(odd){ background:#f9fbff; }
  .table-bnn tbody tr:hover{ background:#eef5ff; }

  .pill{ border-radius:999px; padding:.2rem .6rem; font-weight:800; }
  .pill-info{ background:var(--bnn-sky); color:var(--bnn-navy); }
  .btn-bnn{ background:var(--bnn-green); border:none; color:#fff; font-weight:800; border-radius:12px; }
  .btn-outline-bnn{ border:1px solid var(--bnn-navy); color:var(--bnn-navy); font-weight:800; border-radius:12px; }
  .rule{ background:#fff; border:1px dashed var(--line); border-radius:12px; padding:.7rem .9rem; margin-bottom:.5rem }
  .small-muted{ color:#6b7280; font-size:.9rem; }
  .badge-soft{ background:#fff3d6; color:#7a5600; padding:.25rem .6rem; border-radius:8px; font-weight:800; }
</style>

<div class="container-fluid py-3" x-data>
  {{-- ===== HERO ===== --}}
  <div class="hero mb-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center">
      <div class="mb-2">
        <div class="chip mb-2"><i class="fas fa-shield-alt"></i> Sistem Kepegawaian BNN</div>
        <h3 class="mb-1">Pengajuan & Informasi Cuti</h3>
        <div class="small text-white-75">Mengacu Perka BNN No. 5 Tahun 2019.</div>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <div class="stat">
          <i class="fas fa-umbrella-beach fa-lg"></i>
          <div><div class="v">{{ $hakCuti }}</div><div class="l">Hak Tahun Ini</div></div>
        </div>
        <div class="stat">
          <i class="fas fa-check-double fa-lg"></i>
          <div><div class="v">{{ $terpakaiTahunIni }}</div><div class="l">Terpakai</div></div>
        </div>
        <div class="stat">
          <i class="fas fa-leaf fa-lg"></i>
          <div><div class="v">{{ $sisaCuti }}</div><div class="l">Sisa</div></div>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== STRIP INFO ===== --}}
  <div class="bnn-card mb-3">
    <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-2">
        <span class="pill pill-info"><i class="fas fa-info-circle"></i> Info Cuti Tahun {{ $tahun }}</span>
        <span class="small-muted">Cuti Tahunan/Bersama mengurangi kuota. Jenis lain mengikuti ketentuan khusus.</span>
      </div>
      <span class="badge-soft"><i class="far fa-calendar-check mr-1"></i> Hari ini: {{ now()->translatedFormat('d M Y') }}</span>
    </div>
  </div>

  {{-- ===== INFORMASI CUTI PER-JENIS (Sisa & Status dipisah) ===== --}}
  <div class="card bnn-card mb-3">
    <div class="card-header"><i class="fas fa-info-circle mr-1"></i> Informasi Cuti {{ $tahun }} — <b>{{ $user->nama }}</b></div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-sm table-bnn mb-0">
          <thead>
            <tr class="text-center">
              <th style="width:28%">Jenis Cuti</th>
              <th style="width:16%"><i class="far fa-calendar"></i> Jatah</th>
              <th style="width:16%"><i class="far fa-check-circle"></i> Terpakai</th>
              <th style="width:16%"><i class="far fa-lemon"></i> Sisa</th>
              <th style="width:12%"><i class="fas fa-flag"></i> Status</th>
              <th style="width:12%"><i class="fas fa-info-circle"></i> Keterangan</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $r)
              @php $hint = $reason[$r['jenis']] ?? ''; @endphp
              <tr>
                <td>{{ $r['jenis'] }}</td>
                <td class="text-center">{{ number_format($r['jatah']) }} Hari</td>
                <td class="text-center">{{ number_format($r['terpakai']) }} Hari</td>
                <td class="text-center"><b class="text-success">{{ number_format($r['sisa']) }} Hari</b></td>
                <td class="text-center">
                  <span class="badge {{ $r['status']['class'] }}">{{ $r['status']['label'] }}</span>
                </td>
                <td class="text-center small">{{ $hint }}</td>
              </tr>
            @endforeach

            @if(($approved['Bersama'] ?? 0) > 0)
              <tr class="table-light">
                <td><em>Cuti Bersama</em> <span class="small-muted">(mengurangi jatah Tahunan)</span></td>
                <td class="text-center">—</td>
                <td class="text-center">{{ number_format((int)$approved['Bersama']) }} Hari</td>
                <td class="text-center">—</td>
                <td class="text-center">—</td>
                <td class="text-center small">Bagian dari Cuti Tahunan</td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
      <div class="p-3 small-muted">
        <i class="fas fa-info-circle"></i> Kolom <b>Terpakai</b> hanya menghitung cuti yang <b>disetujui pimpinan</b>.
        Pengajuan yang masih diproses/ditolak tidak mengurangi kuota.
      </div>
    </div>
  </div>

  <div class="row">
    {{-- ===== FORM (tetap) ===== --}}
    <div class="col-lg-8">
      <div class="card bnn-card mb-3">
        <div class="card-header"><i class="fas fa-file-signature mr-1"></i> Form Pengajuan</div>
        <div class="card-body">
          <form id="formCuti" method="POST" action="{{ url('/cuti/store') }}"
                data-hak="{{ $hakCuti }}" data-sisa="{{ $sisaCuti }}">
            @csrf
            <div class="row g-3">
              <div class="col-12">
                <label class="font-weight-bold">Jenis Cuti</label>
                <select name="jenis_cuti" id="jenis_cuti" class="form-control" required>
                  <option value="">-- Pilih --</option>
                  <option value="Tahunan">Cuti Tahunan</option>
                  <option value="Bersama">Cuti Bersama</option>
                  <option value="Sakit">Cuti Sakit</option>
                  <option value="Melahirkan">Cuti Melahirkan</option>
                  <option value="Penting">Cuti Karena Alasan Penting</option>
                  <option value="Besar">Cuti Besar</option>
                  <option value="Luar Tanggungan Negara">Cuti di Luar Tanggungan Negara</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="font-weight-bold">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="font-weight-bold">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
              </div>

              <div class="col-12">
                <label class="font-weight-bold">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Tuliskan alasan/uraian singkat pengajuan" required></textarea>
              </div>

              <div class="col-12">
                <div class="font-weight-bold mb-2"><i class="fas fa-book"></i> Panduan & Persyaratan</div>
                <div id="rulesBox">
                  <div class="rule"><i class="far fa-lightbulb"></i> Pilih jenis cuti untuk menampilkan aturan ringkas.</div>
                </div>
                <div class="small-muted"><i class="fas fa-balance-scale"></i> Mengacu pada ketentuan BNN Tahun 2019. </div>
              </div>

              <div class="col-12 text-right">
                <button id="btnSubmit" type="submit" class="btn btn-bnn">
                  <span class="btn-text"><i class="fas fa-paper-plane"></i> Kirim Pengajuan</span>
                  <span class="btn-loading d-none"><i class="fas fa-spinner fa-spin"></i> Mengirim...</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- Upload dokumen (tetap) --}}
      <div class="card bnn-card">
        <div class="card-header"><i class="fas fa-upload mr-1"></i> Upload Dokumen ke Pimpinan</div>
        <div class="card-body">
          <form id="formUpload" method="POST" action="{{ url('/cuti/upload-dokumen') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
              <div class="col-md-4">
                <label class="font-weight-bold">ID Pengajuan</label>
                <input type="number" name="cuti_id" class="form-control" placeholder="Contoh: 1024" required>
              </div>
              <div class="col-md-8">
                <label class="font-weight-bold">Dokumen Pendukung (.pdf)</label>
                <input type="file" name="dokumen" class="form-control" accept="application/pdf" required>
              </div>
              <div class="col-12 small-muted">
                <i class="fas fa-paperclip"></i> Misal: surat dokter (Sakit), surat kelahiran (Melahirkan), atau dokumen alasan penting.
              </div>
              <div class="col-12 text-right">
                <button type="submit" class="btn btn-outline-bnn"><i class="fas fa-cloud-upload-alt"></i> Upload</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Ringkasan kanan (riwayat ditiadakan sesuai permintaan sebelumnya) --}}
    <div class="col-lg-4">
      <div class="card bnn-card">
        <div class="card-header"><i class="fas fa-clipboard-list mr-1"></i> Ringkasan Pengajuan</div>
        <div class="card-body">
          <div class="row"><div class="col-6 text-muted">Jenis</div><div class="col-6 text-right font-weight-bold" id="sumJenis">-</div></div>
          <div class="row"><div class="col-6 text-muted">Rentang</div><div class="col-6 text-right font-weight-bold" id="sumRange">-</div></div>
          <div class="row"><div class="col-6 text-muted">Total Hari Kerja</div><div class="col-6 text-right font-weight-bold" id="sumHari">0</div></div>
          <hr>
          <div class="small-muted">Perhitungan hari kerja mengabaikan Sabtu & Minggu. Hari libur nasional dapat ditambahkan oleh admin.</div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('adminlte/plugins/moment/moment.min.js') }}"></script>
<script>
  // ======= Hari libur nasional (bisa ditambah admin) =======
  const HOLIDAYS = []; // 'YYYY-MM-DD'

  function countWorkdays(start, end){
    const s = moment(start, 'YYYY-MM-DD', true);
    const e = moment(end,   'YYYY-MM-DD', true);
    if(!s.isValid() || !e.isValid() || e.isBefore(s)) return 0;
    let d = s.clone(), count = 0;
    while(!d.isAfter(e)){
      const isWeekend  = d.isoWeekday() >= 6;
      const isHoliday  = HOLIDAYS.includes(d.format('YYYY-MM-DD'));
      if(!isWeekend && !isHoliday) count++;
      d.add(1,'day');
    }
    return count;
  }

  function renderRules(jenis){
    const box = document.getElementById('rulesBox');
    const gender = '{{ strtolower($jkRaw) }}';
    const sisa   = parseInt(document.getElementById('formCuti').dataset.sisa || '0', 10);

    const rules = {
      'Tahunan': [
        'Minimal masa kerja 1 tahun.',
        'Mengurangi kuota tahunan. Sisa kuota saat ini: <b>'+sisa+' hari</b>.',
        'Pastikan tidak bertabrakan dengan penugasan dinas.'
      ],
      'Bersama': [
        'Bagian dari cuti tahunan sesuai kalender nasional.',
        'Mengurangi kuota tahunan.'
      ],
      'Sakit': [
        'Lampirkan surat dokter jika diminta pimpinan.',
        'Durasi mengikuti kebutuhan medis.'
      ],
      'Melahirkan': [
        (['p','perempuan','female','wanita'].includes(gender)
          ? 'Khusus pegawai perempuan; maksimal 90 hari.'
          : '<span class="text-danger">Khusus pegawai perempuan.</span>')
      ],
      'Penting': [
        'Maksimal 30 hari; lampirkan dokumen pendukung.'
      ],
      'Besar': [
        'Masa kerja ≥ 5 tahun; maksimal 90 hari.'
      ],
      'Luar Tanggungan Negara': [
        'Masa kerja ≥ 5 tahun; maksimal 3 tahun (dapat diperpanjang 1 tahun).',
        'Tidak diperhitungkan sebagai masa kerja & tidak menerima penghasilan.'
      ]
    };
    box.innerHTML = '';
    (rules[jenis] || ['Pilih jenis cuti untuk melihat aturan.']).forEach(txt => {
      box.insertAdjacentHTML('beforeend', `<div class="rule"><i class="far fa-check-circle"></i> ${txt}</div>`);
    });
  }

  function updateRingkasan(){
    const jenis = document.getElementById('jenis_cuti').value;
    const t1 = document.getElementById('tanggal_mulai').value;
    const t2 = document.getElementById('tanggal_selesai').value;

    document.getElementById('sumJenis').innerText = jenis || '-';
    if(t1 && t2){
      document.getElementById('sumRange').innerText = `${t1} s.d ${t2}`;
      document.getElementById('sumHari').innerText  = countWorkdays(t1, t2);
    }else{
      document.getElementById('sumRange').innerText = '-';
      document.getElementById('sumHari').innerText  = '0';
    }
    if (jenis) renderRules(jenis);
  }
  ['jenis_cuti','tanggal_mulai','tanggal_selesai'].forEach(id=>{
    const el = document.getElementById(id);
    if (el) el.addEventListener('change', updateRingkasan);
  });

  function setSubmitting(isSubmitting){
    const btn = document.getElementById('btnSubmit');
    btn.disabled = isSubmitting;
    btn.querySelector('.btn-text').classList.toggle('d-none', isSubmitting);
    btn.querySelector('.btn-loading').classList.toggle('d-none', !isSubmitting);
  }

  // ====== AJAX Pengajuan Cuti ======
  document.getElementById('formCuti').addEventListener('submit', async function(e){
    e.preventDefault(); setSubmitting(true);
    const fd = new FormData(this);
    try{
      const res = await fetch(this.action, {
        method:'POST',
        headers:{ 'X-CSRF-TOKEN': fd.get('_token'), 'Accept':'application/json' },
        body: fd
      });
      const data = await res.json().catch(()=> ({}));
      if(!res.ok){
        Swal.fire({ icon:'error', title:'Gagal', text:data.message || 'Pengajuan tidak dapat diproses.' });
        return;
      }
      Swal.fire({ icon:'success', title:'Berhasil!', text:data.message || 'Pengajuan cuti berhasil dikirim.' });
      this.reset(); updateRingkasan();
    }catch(_){
      Swal.fire({ icon:'error', title:'Gagal', text:'Koneksi bermasalah. Coba lagi.' });
    }finally{ setSubmitting(false); }
  });

  // ====== AJAX Upload Dokumen ======
  document.getElementById('formUpload').addEventListener('submit', async function(e){
    e.preventDefault();
    const fd = new FormData(this);
    try{
      const res = await fetch(this.action, { method:'POST', headers:{ 'X-CSRF-TOKEN': fd.get('_token') }, body: fd });
      if(!res.ok) throw new Error('Upload gagal');
      Swal.fire({ icon:'success', title:'Upload Berhasil!', text:'Dokumen berhasil diupload.' });
      this.reset();
    }catch(_){
      Swal.fire({ icon:'error', title:'Upload Gagal', text:'Periksa kembali file (PDF) dan ID pengajuan.' });
    }
  });

  // init
  updateRingkasan();
</script>
@endpush
@endsection
