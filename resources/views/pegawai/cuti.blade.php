@extends('layouts.template')

@section('content')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@php
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\DB;

  $user = Auth::user();
  // total cuti tahunan yg sudah disetujui thn berjalan (termasuk cuti bersama)
  $terpakaiTahunIni = \App\Models\Cuti::where('id_user', $user->id_user)
      ->whereIn('jenis_cuti', ['Tahunan','Bersama'])
      ->where('status','Disetujui')
      ->whereYear('tanggal_mulai', now()->year)
      ->sum(DB::raw('DATEDIFF(tanggal_selesai, tanggal_mulai) + 1'));

  $sisaCuti = max(0, ($hakCuti ?? 12) - $terpakaiTahunIni);
@endphp

<style>
  :root{
    --bnn-navy:#0a2a4a;
    --bnn-navy-2:#0f3a6a;
    --bnn-gold:#ffc107;
    --bnn-green:#1e8858;
    --bnn-green-2:#198754;
    --bnn-sky:#e9f3ff;
  }
  body{background:linear-gradient(180deg,#f8fbff 0%,#f3f7fc 100%);}
  .bnn-hero{
    background:linear-gradient(120deg,var(--bnn-navy),var(--bnn-navy-2));
    color:#fff;border-radius:18px;padding:18px 22px;
    box-shadow:0 10px 24px rgba(10,42,74,.25);
  }
  .bnn-hero .chip{background:rgba(255,193,7,.18);color:#ffe083;border:1px solid rgba(255,193,7,.35);border-radius:999px;padding:.25rem .75rem;font-weight:600}
  .bnn-card{border:0;border-radius:16px;box-shadow:0 10px 22px rgba(0,0,0,.06);}
  .bnn-card .card-header{background:var(--bnn-navy);color:#fff;border-bottom:3px solid var(--bnn-gold);border-radius:16px 16px 0 0;font-weight:700}
  .bnn-label{font-weight:700;color:var(--bnn-navy)}
  .btn-bnn{background:var(--bnn-green-2);border:0;border-radius:12px;font-weight:700;padding:.6rem 1rem}
  .btn-bnn[disabled]{opacity:.7;cursor:not-allowed}
  .btn-outline-bnn{border-color:var(--bnn-navy);color:var(--bnn-navy);font-weight:700;border-radius:12px}
  .badge-pill{border-radius:999px}
  .rule{background:#fff;border:1px dashed #cfd9e6;border-radius:12px;padding:.7rem .9rem;margin-bottom:.5rem}
  .rule i{color:var(--bnn-gold)}
  .summary-item{display:flex;justify-content:space-between;margin-bottom:.25rem}
  .kuota{background:var(--bnn-sky);border-radius:12px;padding:.75rem}
  .small-muted{color:#6c7a8a;font-size:.85rem}
</style>

<div class="container-fluid py-3" x-data>
  {{-- HERO --}}
  <div class="bnn-hero mb-3 d-flex flex-wrap justify-content-between align-items-center">
    <div class="mb-2">
      <div class="chip mb-2"><i class="fas fa-shield-alt me-1"></i> Sistem Kepegawaian BNN</div>
      <h3 class="mb-1">Pengajuan Cuti Pegawai</h3>
      <div class="small text-white-50">Ikuti ketentuan Peraturan Kepala BNN No. 5 Tahun 2019</div>
    </div>
    <div class="text-end">
      <div class="badge bg-light text-dark badge-pill mb-2">
        <i class="fas fa-calendar-day me-1"></i> {{ now()->translatedFormat('d M Y') }}
      </div>
      <div class="kuota">
        <div class="fw-bold mb-1"><i class="fas fa-umbrella-beach me-1"></i> Kuota Cuti Tahunan</div>
        <div class="summary-item"><span>Hak Tahun Ini</span><span class="fw-bold">{{ $hakCuti }} hari</span></div>
        <div class="summary-item"><span>Terpakai</span><span class="fw-bold">{{ $terpakaiTahunIni }} hari</span></div>
        <div class="summary-item"><span>Sisa</span><span class="fw-bold text-success">{{ $sisaCuti }} hari</span></div>
      </div>
    </div>
  </div>

  <div class="card bnn-card">
    <div class="card-header"><i class="fas fa-clipboard-check me-1"></i> Layanan Cuti BNN</div>
    <div class="card-body">
      <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab-form"><i class="fas fa-file-signature me-1"></i> Form Pengajuan</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-upload"><i class="fas fa-upload me-1"></i> Upload ke Pimpinan</a></li>
      </ul>

      <div class="row">
        {{-- FORM --}}
        <div class="col-lg-8">
          <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-form">
              <form id="formCuti" method="POST" action="{{ url('/cuti/store') }}" data-hak="{{ $hakCuti }}" data-sisa="{{ $sisaCuti }}">
                @csrf

                <div class="row g-3">
                  <div class="col-12">
                    <label class="bnn-label">Jenis Cuti</label>
                    <select name="jenis_cuti" id="jenis_cuti" class="form-control" required>
                      <option value="">-- Pilih Jenis Cuti --</option>
                      <option value="Tahunan">Cuti Tahunan</option>
                      <option value="Bersama">Cuti Bersama (mengurangi kuota tahunan)</option>
                      <option value="Sakit">Cuti Sakit</option>
                      <option value="Melahirkan">Cuti Melahirkan</option>
                      <option value="Penting">Cuti Karena Alasan Penting</option>
                      <option value="Besar">Cuti Besar</option>
                      <option value="Luar Tanggungan Negara">Cuti di Luar Tanggungan Negara</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="bnn-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label class="bnn-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
                  </div>

                  <div class="col-12">
                    <label class="bnn-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Tuliskan alasan/uraian singkat pengajuan" required></textarea>
                    <div class="small-muted mt-1"><i class="fas fa-info-circle me-1"></i> Pastikan tanggal tidak bertabrakan dengan dinas luar / penugasan.</div>
                  </div>

                  {{-- Panduan per-jenis (dinamis) --}}
                  <div class="col-12">
                    <div class="bnn-label mb-2"><i class="fas fa-book me-1"></i> Panduan & Persyaratan</div>
                    <div id="rulesBox">
                      <div class="rule"><i class="far fa-lightbulb me-1"></i> Pilih jenis cuti di atas untuk menampilkan aturan singkatnya.</div>
                    </div>
                  </div>

                  <div class="col-12 text-end">
                    <button id="btnSubmit" type="submit" class="btn btn-bnn">
                      <span class="btn-text"><i class="fas fa-paper-plane me-1"></i> Kirim Pengajuan</span>
                      <span class="btn-loading d-none"><i class="fas fa-spinner fa-spin me-1"></i> Mengirim...</span>
                    </button>
                  </div>
                </div>
              </form>
            </div>

            {{-- UPLOAD --}}
            <div class="tab-pane fade" id="tab-upload">
              <form id="formUpload" method="POST" action="{{ url('/cuti/upload-dokumen') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="bnn-label">ID Pengajuan</label>
                    <input type="number" name="cuti_id" class="form-control" placeholder="Contoh: 1024" required>
                  </div>
                  <div class="col-md-6">
                    <label class="bnn-label">Dokumen Pendukung (.pdf)</label>
                    <input type="file" name="dokumen" class="form-control" accept="application/pdf" required>
                  </div>
                  <div class="col-12 small-muted">
                    <i class="fas fa-paperclip me-1"></i> Misal: surat dokter (Cuti Sakit), surat keterangan kelahiran (Melahirkan), atau dokumen alasan penting.
                  </div>
                  <div class="col-12 text-end">
                    <button type="submit" class="btn btn-outline-bnn"><i class="fas fa-upload me-1"></i> Upload ke Pimpinan</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        {{-- RINGKASAN --}}
        <div class="col-lg-4">
          <div class="card bnn-card mb-3">
            <div class="card-header"><i class="fas fa-clipboard-list me-1"></i> Ringkasan</div>
            <div class="card-body">
              <div class="summary-item"><span>Jenis Cuti</span><span id="sumJenis" class="fw-bold">-</span></div>
              <div class="summary-item"><span>Rentang</span><span id="sumRange" class="fw-bold">-</span></div>
              <div class="summary-item"><span>Total Hari Kerja</span><span id="sumHari" class="fw-bold">0</span></div>
              <hr>
              <div class="small-muted"><i class="fas fa-info-circle me-1"></i> Perhitungan hari kerja mengabaikan Sabtu & Minggu. Hari libur nasional dapat ditambahkan oleh admin.</div>
            </div>
          </div>

          <div class="card bnn-card">
            <div class="card-header"><i class="fas fa-question-circle me-1"></i> FAQ Singkat</div>
            <div class="card-body small">
              <p><b>Cuti Tahunan/Bersama</b> mengurangi kuota tahunan. Minimal masa kerja 1 tahun.</p>
              <p><b>Cuti Besar</b> butuh masa kerja ≥ 5 tahun, maksimal 90 hari.</p>
              <p><b>Cuti Melahirkan</b> khusus pegawai perempuan, maksimal 90 hari.</p>
              <p><b>Cuti Penting</b> maksimal 30 hari. <b>CTLN</b> butuh masa kerja ≥ 5 tahun, maksimal 3 tahun.</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('adminlte/plugins/moment/moment.min.js') }}"></script>
<script>
  // --- Konfigurasi (admin bisa tambahkan hari libur nasional di sini) ---
  const HOLIDAYS = []; // format 'YYYY-MM-DD'

  // Hitung hari kerja (exclude Sabtu, Minggu, dan HOLIDAYS)
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

  // Tampilkan aturan per jenis cuti
  function renderRules(jenis) {
    const box = document.getElementById('rulesBox');
    const gender = '{{ strtolower($user->jenis_kelamin ?? "") }}';
    const sisa = parseInt(document.getElementById('formCuti').dataset.sisa || '0', 10);

    const rules = {
      'Tahunan': [
        'Minimal masa kerja 1 tahun.',
        'Mengurangi kuota tahunan. Sisa kuota saat ini: <b>'+sisa+' hari</b>.',
        'Pastikan tanggal tidak bentrok dengan penugasan.'
      ],
      'Bersama': [
        'Mengurangi kuota tahunan (bagian dari Cuti Tahunan).',
        'Ikuti kebijakan kalender cuti bersama nasional.'
      ],
      'Sakit': [
        'Lampirkan surat dokter jika diminta pimpinan.',
        'Durasi menyesuaikan kebutuhan medis.'
      ],
      'Melahirkan': [
        (gender === 'perempuan' ? 'Khusus pegawai perempuan.' : '<span class="text-danger">Khusus pegawai perempuan.</span>'),
        'Maksimal 90 hari. Lampirkan dokumen kelahiran.'
      ],
      'Penting': [
        'Maksimal 30 hari.',
        'Lampirkan dokumen pendukung (mis. surat keluarga/instansi).'
      ],
      'Besar': [
        'Minimal masa kerja 5 tahun.',
        'Maksimal 90 hari.'
      ],
      'Luar Tanggungan Negara': [
        'Minimal masa kerja 5 tahun.',
        'Maksimal 3 tahun (1.095 hari).',
        'Status kepegawaian mengikuti ketentuan CTLN.'
      ]
    };

    box.innerHTML = '';
    (rules[jenis] || ['Pilih jenis cuti untuk melihat aturan.']).forEach(txt => {
      box.insertAdjacentHTML('beforeend',
        `<div class="rule"><i class="far fa-check-circle me-1"></i> ${txt}</div>`);
    });
  }

  // Update ringkasan
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
    document.getElementById(id).addEventListener('change', updateRingkasan);
  });

  // Helper tombol loading
  function setSubmitting(isSubmitting){
    const btn = document.getElementById('btnSubmit');
    btn.disabled = isSubmitting;
    btn.querySelector('.btn-text').classList.toggle('d-none', isSubmitting);
    btn.querySelector('.btn-loading').classList.toggle('d-none', !isSubmitting);
  }

  // === AJAX Pengajuan (dengan cek status HTTP yang benar) ===
  document.getElementById('formCuti').addEventListener('submit', async function(e){
    e.preventDefault();
    setSubmitting(true);

    const form = this;
    const fd = new FormData(form);

    try {
      const res = await fetch(form.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': fd.get('_token'), 'Accept':'application/json' },
        body: fd
      });

      const data = await res.json().catch(()=> ({}));

      if (!res.ok) {
        // 4xx/5xx → tampilkan error
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: data.message || 'Pengajuan tidak dapat diproses.',
          confirmButtonColor: '#d33'
        });
        return;
      }

      // 2xx → sukses
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: data.message || 'Pengajuan cuti berhasil dikirim.',
        confirmButtonColor: '#198754'
      });
      form.reset();
      updateRingkasan();

    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: 'Koneksi ke server bermasalah. Coba lagi.',
        confirmButtonColor: '#d33'
      });
    } finally {
      setSubmitting(false);
    }
  });

  // === AJAX Upload Dokumen ===
  document.getElementById('formUpload').addEventListener('submit', async function(e){
    e.preventDefault();
    const fd = new FormData(this);
    try{
      const res = await fetch(this.action, {
        method:'POST',
        headers:{ 'X-CSRF-TOKEN': fd.get('_token') },
        body: fd
      });
      if(!res.ok) throw new Error();
      Swal.fire({ icon:'success', title:'Upload Berhasil!', text:'Dokumen berhasil diupload ke pimpinan.', confirmButtonColor:'#198754' });
      this.reset();
    }catch(_){
      Swal.fire({ icon:'error', title:'Upload Gagal', text:'Periksa kembali file (PDF, maks 2MB) dan ID pengajuan.', confirmButtonColor:'#d33' });
    }
  });

  // init UI
  updateRingkasan();
</script>
@endpush

@endsection
