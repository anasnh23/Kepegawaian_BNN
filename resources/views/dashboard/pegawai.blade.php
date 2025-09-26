@extends('layouts.template')
@section('title', 'Dashboard Pegawai')

@section('content')
@php
  // ====== Data dasar dari controller ======
  $stats   = $presensiStats ?? [];
  $hadir   = (int) data_get($stats, 'hadir', 0);
  $telat   = (int) data_get($stats, 'terlambat', 0);
  $absen   = (int) data_get($stats, 'tidak hadir', 0);
  $dinas   = (int) data_get($stats, 'dinas_luar', 0);
  $cutiBln = (int) ($cutiStats ?? 0);
  $label   = $labelPeriode ?? 'Bulan Ini';
  $riwayat = $riwayat ?? [];
  $nama    = data_get(auth()->user(), 'nama', 'Pegawai');
  $nip     = data_get(auth()->user(), 'nip', '-');
  $periode = $periode ?? 'bulan_ini';

  // ====== Profil & masa kerja ======
  $mkTh   = (int) ($masaKerjaTahun ?? 0);
  $mkBln  = (int) ($masaKerjaBulan ?? 0);
  $mkText = trim(($mkTh ? $mkTh.' th ' : '').($mkBln ? $mkBln.' bln' : ''));
  $masaKerjaLabel = ($masaKerjaLabel ?? '') !== '' ? $masaKerjaLabel : ($mkText !== '' ? $mkText : 'Baru Bergabung');

  // ====== Rekap mingguan untuk chart ======
  $rekap = collect($rekapMingguan ?? []);
  $mingguLabels = $rekap->map(function($r){
      $str = (string) $r->tahun_minggu; // contoh 202538
      $yr  = substr($str, 0, 4);
      $wk  = substr($str, 4);
      return 'W'.$wk.'/'.$yr;
  })->values();
  $mingguHadir     = $rekap->pluck('hadir')->map(fn($v)=>(int)$v)->values();
  $mingguTerlambat = $rekap->pluck('terlambat')->map(fn($v)=>(int)$v)->values();
  $mingguAbsen     = $rekap->pluck('tidak_hadir')->map(fn($v)=>(int)$v)->values();
  $mingguDinas     = $rekap->pluck('dinas_luar')->map(fn($v)=>(int)$v)->values();

  // ====== Notifikasi & Pengumuman ======
  $listPengumuman = collect($pengumumanHr ?? []);
  $listNotif      = collect($notifikasiTerbaru ?? []);
@endphp

<style>
  :root{
    --bnn-navy:#003366; --bnn-navy-2:#0b2f5e; --bnn-blue:#144272; --bnn-gold:#f0ad4e;
    --soft:#eef3fb; --ink:#0f172a; --line:#e6edf6;
    --green:#28a745; --yellow:#ffc107; --red:#dc3545; --blue:#007bff;
  }
  .dash-hero{
    background:linear-gradient(135deg,var(--bnn-navy),#012148 60%,var(--bnn-navy-2));
    color:#fff;border-radius:16px;padding:16px 18px;position:relative;overflow:hidden;
    box-shadow:0 12px 28px rgba(0,33,72,.22);
  }
  .dash-hero::after{
    content:"";position:absolute;right:-60px;top:-60px;width:200px;height:200px;opacity:.08;
    background:url('{{ asset('images/bnn-watermark.svg') }}') no-repeat center/contain;
  }
  .kpi{
    background:linear-gradient(135deg,var(--bnn-navy),var(--bnn-blue)); color:#fff;border-left:6px solid var(--bnn-gold);
    border-radius:14px; box-shadow:0 10px 26px rgba(0,33,72,.18);
  }
  .kpi-title{ font-weight:800;font-size:.9rem;color:var(--bnn-gold);text-transform:uppercase;letter-spacing:.4px; }
  .kpi-value{ font-weight:800;font-size:1.6rem;color:#fff; }
  .badge-soft{ background:#fff3d6;color:#7a5600;padding:.25rem .5rem;border-radius:8px;font-weight:700; }
  .card-bnn{ border:1px solid var(--line); border-radius:14px; overflow:hidden; box-shadow:0 8px 24px rgba(16,24,40,.06); }
  .card-bnn .card-header{ background:var(--bnn-navy);color:#fff;font-weight:700;border:0; }
  .table thead th{ background:#0f1f39;color:#eaf2ff;border-color:#0f1f39;font-weight:700;vertical-align:middle; }
  .table td,.table th{ border-color:#e7eef3; }
  .list-compact li{ margin-bottom:.35rem; }

  /* ===== Papan Pengumuman (tanpa tabel) ===== */
  .announce-wrap{ padding:18px; }
  .announce-item{
    border:1px dashed var(--line); border-radius:14px; background:#f6f9ff; padding:18px 18px 16px;
    box-shadow:0 6px 18px rgba(0,33,72,.06); margin-bottom:14px;
  }
  .announce-title{
    font-weight:800; color:#0b3d91; margin:0 0 .35rem 0; display:flex; align-items:center; gap:.5rem;
  }
  .announce-title .icon{
    background:linear-gradient(135deg,var(--bnn-gold),#ffd77a); color:#172554;
    width:28px;height:28px;border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:.95rem;
    box-shadow:0 4px 10px rgba(244,196,48,.35);
  }
  .announce-body{ color:#0f172a; white-space:pre-wrap; }
</style>

<div class="container-fluid">

  {{-- ===== Hero ===== --}}
  <div class="dash-hero mb-3 d-flex align-items-center justify-content-between">
    <div>
      <h4 class="m-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Pegawai</h4>
      <div class="sub">Halo, <strong>{{ $nama }}</strong>. Ringkasan ({{ $label }})</div>
      <div class="small mt-1">NIP: <strong>{{ $nip }}</strong></div>
    </div>
  </div>

  {{-- ===== Filter Periode ===== --}}
  <form class="mb-3" method="get" action="{{ url()->current() }}">
    <div class="d-flex align-items-end flex-wrap" style="gap:.75rem;">
      <div>
        <label class="form-label mb-1">Dari</label>
        <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
      </div>
      <div>
        <label class="form-label mb-1">Sampai</label>
        <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
      </div>
      <div class="pb-1">
        <button class="btn btn-dark btn-sm">Terapkan</button>
      </div>
      <div class="ms-auto text-muted pb-1">Periode: <strong>{{ $label }}</strong></div>
    </div>
  </form>

  {{-- ===== KPI utama ===== --}}
  <div class="row mb-4">
    <div class="col-md-3 mb-3">
      <div class="kpi p-3 h-100">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="kpi-title"><i class="fas fa-user-check me-2"></i>Hadir</div>
            <div class="kpi-value">{{ number_format($hadir) }}</div>
          </div>
          <i class="fas fa-calendar-check fa-2x text-warning"></i>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="kpi p-3 h-100">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="kpi-title"><i class="fas fa-clock me-2"></i>Terlambat</div>
            <div class="kpi-value">{{ number_format($telat) }}</div>
          </div>
          <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="kpi p-3 h-100">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="kpi-title"><i class="fas fa-plane-departure me-2"></i>Pengajuan Cuti (Periode)</div>
            <div class="kpi-value">{{ number_format($cutiBln) }}</div>
          </div>
          <i class="fas fa-calendar-alt fa-2x text-warning"></i>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="kpi p-3 h-100">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="kpi-title"><i class="fas fa-car-side me-2"></i>Dinas Luar</div>
            <div class="kpi-value">{{ number_format($dinas) }}</div>
          </div>
          <i class="fas fa-car fa-2x text-info"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== KPI Baris 2: Masa Kerja ===== --}}
  <div class="row mb-4">
    <div class="col-md-3 mb-3">
      <div class="kpi p-3 h-100">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="kpi-title"><i class="fas fa-user-clock me-2"></i>Masa Kerja</div>
            <div class="kpi-value">{{ $masaKerjaLabel }}</div>
          </div>
          <i class="fas fa-briefcase fa-2x text-warning"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== Cuti & Donut ===== --}}
  <div class="row mb-4">
    <div class="col-lg-6 mb-4">
      <div class="card card-bnn h-100">
        <div class="card-header"><i class="fas fa-plane me-2"></i>Cuti Tahun {{ now()->year }}</div>
        <div class="card-body">
          <div>Total digunakan: <span class="badge-soft">{{ $totalCutiTahunIni }} hari</span></div>
          @if($cutiTerakhir)
            <hr>
            <div class="text-muted mb-1">
              Pengajuan Terakhir: {{ \Carbon\Carbon::parse($cutiTerakhir->tanggal_pengajuan)->translatedFormat('d M Y') }}
            </div>
            <ul class="list-unstyled list-compact mb-0">
              <li>Jenis: <strong>{{ $cutiTerakhir->jenis_cuti }}</strong></li>
              <li>Periode:
                <strong>{{ \Carbon\Carbon::parse($cutiTerakhir->tanggal_mulai)->translatedFormat('d M Y') }}</strong>
                – <strong>{{ \Carbon\Carbon::parse($cutiTerakhir->tanggal_selesai)->translatedFormat('d M Y') }}</strong>
              </li>
              <li>Status:
                <span class="badge bg-{{ $cutiTerakhir->status === 'Disetujui' ? 'success' :
                     ($cutiTerakhir->status === 'Ditolak' ? 'danger' : 'warning') }}">
                  {{ $cutiTerakhir->status }}
                </span>
              </li>
            </ul>
          @else
            <div class="text-muted mt-2">Belum ada pengajuan cuti.</div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-6 mb-4">
      <div class="card card-bnn h-100">
        <div class="card-header"><i class="fas fa-chart-pie me-2"></i>Komposisi Presensi ({{ $label }})</div>
        <div class="card-body">
          <canvas id="presensiDonut" height="220"></canvas>
          <div class="small text-muted mt-2">
            Hadir: {{ $hadir }} • Terlambat: {{ $telat }} • Tidak Hadir: {{ $absen }} • Dinas Luar: {{ $dinas }}
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== Pengumuman HR/Admin: KOLOM BESAR TANPA TABEL ===== --}}
  <div class="row mb-4">
    <div class="col-lg-12">
      <div class="card card-bnn">
        <div class="card-header"><i class="fas fa-bullhorn me-2"></i>Pengumuman HR/Admin</div>
        <div class="announce-wrap">
          @forelse($listPengumuman as $p)
            <div class="announce-item">
              <h5 class="announce-title">
                <span class="icon"><i class="fas fa-thumbtack"></i></span>
                {{ $p->judul ?? 'Pengumuman' }}
              </h5>
              <div class="announce-body">
                {{-- tampilkan apa adanya: HTML akan dirender, teks biasa dip-<br> secara otomatis --}}
                @if(isset($p->konten) && strip_tags($p->konten) !== $p->konten)
                  {!! $p->konten !!}
                @else
                  {!! nl2br(e($p->konten ?? '-')) !!}
                @endif
              </div>
            </div>
          @empty
            <div class="text-muted">Belum ada pengumuman.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  {{-- ===== Rekap Mingguan & Riwayat ===== --}}
  <div class="row">
    <div class="col-lg-6 mb-4">
      <div class="card card-bnn h-100">
        <div class="card-header"><i class="fas fa-chart-bar me-2"></i>Rekap Mingguan ({{ $label }})</div>
        <div class="card-body">
          <canvas id="rekapMingguanBar" height="220"></canvas>
          @if($mingguLabels->isEmpty())
            <div class="text-muted small mt-2">Belum ada data untuk periode ini.</div>
          @endif
        </div>
      </div>
    </div>

    @if(collect($riwayat)->isNotEmpty())
    <div class="col-lg-6 mb-4">
      <div class="card card-bnn h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div><i class="fas fa-stream me-2"></i>Riwayat Presensi Terbaru</div>
          <a href="{{ route('presensi.index') }}" class="btn btn-outline-light btn-sm">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover text-sm m-0">
              <thead class="text-center">
                <tr>
                  <th>#</th>
                  <th>Tanggal</th>
                  <th>Jam Masuk</th>
                  <th>Jam Pulang</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($riwayat as $i => $row)
                  <tr>
                    <td class="text-center">{{ $i+1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}</td>
                    <td>{{ $row->jam_masuk ?? '-' }}</td>
                    <td>{{ $row->jam_pulang ?? '-' }}</td>
                    <td class="text-center">
                      @php
                        $st  = strtolower((string)$row->status);
                        $cls = $st === 'hadir' ? 'success' : ($st === 'terlambat' ? 'warning' : ($st === 'dinas_luar' ? 'info' : 'danger'));
                      @endphp
                      <span class="badge bg-{{ $cls }}">{{ ucfirst(str_replace('_',' ', $st)) }}</span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
  const C = { green:'#28a745', yellow:'#ffc107', red:'#dc3545', blue:'#007bff' };

  // Donut Komposisi Presensi
  const donutEl = document.getElementById('presensiDonut');
  if (donutEl) {
    new Chart(donutEl, {
      type:'doughnut',
      data:{
        labels:['Hadir','Terlambat','Tidak Hadir','Dinas Luar'],
        datasets:[{
          data:[{{ $hadir }}, {{ $telat }}, {{ $absen }}, {{ $dinas }}],
          backgroundColor:[C.green, C.yellow, C.red, C.blue]
        }]
      },
      options:{ plugins:{legend:{position:'bottom'}}, cutout:'60%', responsive:true }
    });
  }

  // Bar Rekap Mingguan
  const mingguanLabels = {!! $mingguLabels->toJson(JSON_UNESCAPED_UNICODE) !!};
  const dataHadir      = {!! $mingguHadir->toJson() !!};
  const dataTelat      = {!! $mingguTerlambat->toJson() !!};
  const dataAbsen      = {!! $mingguAbsen->toJson() !!};
  const dataDinas      = {!! $mingguDinas->toJson() !!};

  const barEl = document.getElementById('rekapMingguanBar');
  if (barEl) {
    new Chart(barEl, {
      type:'bar',
      data:{
        labels: mingguanLabels,
        datasets:[
          { label:'Hadir',        data:dataHadir,     backgroundColor:C.green,  borderRadius:6, maxBarThickness:28 },
          { label:'Terlambat',    data:dataTelat,     backgroundColor:C.yellow, borderRadius:6, maxBarThickness:28 },
          { label:'Tidak Hadir',  data:dataAbsen,     backgroundColor:C.red,    borderRadius:6, maxBarThickness:28 },
          { label:'Dinas Luar',   data:dataDinas,     backgroundColor:C.blue,   borderRadius:6, maxBarThickness:28 },
        ]
      },
      options:{
        responsive:true,
        plugins:{ legend:{ position:'bottom' } },
        scales:{ y:{ beginAtZero:true, ticks:{ precision:0 } } }
      }
    });
  }
})();
</script>
@endpush
