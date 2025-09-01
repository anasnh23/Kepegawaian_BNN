@extends('layouts.template')
@section('title', 'Dashboard Pegawai')

@section('content')
@php
  $stats   = $presensiStats ?? [];
  $hadir   = (int) data_get($stats, 'hadir', 0);
  $telat   = (int) data_get($stats, 'terlambat', 0);
  $absen   = (int) data_get($stats, 'tidak hadir', 0);
  $cutiBln = (int) ($cutiStats ?? 0);
  $sisa    = (int) ($sisaCuti ?? 0);
  $label   = $labelPeriode ?? 'Bulan Ini';
  $riwayat = $riwayat ?? [];
  $nama    = data_get(auth()->user(), 'nama', 'Pegawai');
  $periode = $periode ?? 'bulan_ini';
  $masaKerjaTahun = $masaKerjaTahun ?? 0;
  $masaKerjaBulan = $masaKerjaBulan ?? 0;
@endphp

<style>
  :root{ --bnn-navy:#003366; --bnn-navy-2:#0b2f5e; --bnn-blue:#144272; --bnn-gold:#f0ad4e;
         --soft:#eef3fb; --ink:#0f172a; --line:#e6edf6; --green:#28a745; --yellow:#ffc107; --red:#dc3545; }
  .dash-hero{ background:linear-gradient(135deg,var(--bnn-navy),#012148 60%,var(--bnn-navy-2));
              color:#fff;border-radius:16px;padding:16px 18px;position:relative;overflow:hidden;
              box-shadow:0 12px 28px rgba(0,33,72,.22); }
  .dash-hero::after{ content:"";position:absolute;right:-60px;top:-60px;width:200px;height:200px;opacity:.08;
                     background:url('{{ asset('images/bnn-watermark.svg') }}') no-repeat center/contain; }
  .kpi{ background:linear-gradient(135deg,var(--bnn-navy),var(--bnn-blue)); color:#fff;border-left:6px solid var(--bnn-gold);
        border-radius:14px; box-shadow:0 10px 26px rgba(0,33,72,.18); transition:.2s; cursor:pointer; }
  .kpi:hover{ transform:translateY(-3px); box-shadow:0 14px 28px rgba(0,33,72,.26); }
  .kpi-title{ font-weight:800; font-size:.9rem; color:var(--bnn-gold); text-transform:uppercase; letter-spacing:.4px; }
  .kpi-value{ font-weight:800; font-size:1.6rem; color:#fff; }
  .badge-soft{ background:#fff3d6; color:#7a5600; padding:.25rem .5rem; border-radius:8px; font-weight:700; }
  .card-bnn{ border:1px solid var(--line); border-radius:14px; overflow:hidden; box-shadow:0 8px 24px rgba(16,24,40,.06); }
  .card-bnn .card-header{ background:var(--bnn-navy); color:#fff; font-weight:700; border:0; }
  .table thead th{ background:#0f1f39; color:#eaf2ff; border-color:#0f1f39; font-weight:700; }
  .period-tabs .btn{ min-width:110px; }
</style>

<div class="container-fluid">

  {{-- ===== Hero ===== --}}
  <div class="dash-hero mb-3 d-flex align-items-center justify-content-between">
    <div>
      <h4 class="m-0"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard Pegawai</h4>
      <div class="sub">Halo, <strong>{{ $nama }}</strong>. Ringkasan ({{ $label }})</div>
    </div>
    <div class="d-none d-md-block">
      <span class="badge-soft"><i class="fas fa-umbrella-beach mr-1"></i> Sisa Cuti: <strong>{{ $sisa }}</strong> hari</span>
    </div>
  </div>

  {{-- ===== Filter Periode ===== --}}
  <div class="mb-3 d-flex align-items-center period-tabs" style="gap:.5rem;">
    <a href="{{ route('dashboard.pegawai', ['periode'=>'bulan_ini']) }}"
       class="btn btn-sm {{ $periode==='bulan_ini' ? 'btn-primary' : 'btn-outline-primary' }}">
      Bulan Ini
    </a>
    <a href="{{ route('dashboard.pegawai', ['periode'=>'30_hari']) }}"
       class="btn btn-sm {{ $periode==='30_hari' ? 'btn-primary' : 'btn-outline-primary' }}">
      30 Hari
    </a>
    <a href="{{ route('dashboard.pegawai', ['periode'=>'semua']) }}"
       class="btn btn-sm {{ $periode==='semua' ? 'btn-primary' : 'btn-outline-primary' }}">
      Semua
    </a>
    <span class="ml-2 text-muted">Periode: <strong>{{ $label }}</strong></span>
  </div>

  {{-- ===== KPI ===== --}}
  <div class="row mb-4">
    <div class="col-md-3 mb-3">
      <div class="kpi p-3 h-100" onclick="location.href='{{ route('presensi.index') }}'">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="kpi-title"><i class="fas fa-user-check mr-2"></i>Hadir ({{ $label }})</div>
            <div class="kpi-value">{{ number_format($hadir) }}</div>
          </div>
          <i class="fas fa-calendar-check fa-2x text-warning"></i>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="kpi p-3 h-100" onclick="location.href='{{ route('presensi.index') }}'">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="kpi-title"><i class="fas fa-clock mr-2"></i>Terlambat ({{ $label }})</div>
            <div class="kpi-value">{{ number_format($telat) }}</div>
          </div>
          <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="kpi p-3 h-100" onclick="location.href='{{ route('cuti.pegawai') }}'">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="kpi-title"><i class="fas fa-plane-departure mr-2"></i>Cuti Diajukan ({{ $label }})</div>
            <div class="kpi-value">{{ number_format($cutiBln) }}</div>
          </div>
          <i class="fas fa-calendar-alt fa-2x text-warning"></i>
        </div>
      </div>
    </div>
    {{-- ✅ KPI Masa Kerja --}}
    <div class="col-md-3 mb-3">
      <div class="kpi p-3 h-100">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="kpi-title"><i class="fas fa-user-clock mr-2"></i>Masa Kerja</div>
            <div class="kpi-value">
              @if($masaKerjaTahun == 0 && $masaKerjaBulan == 0)
                <span style="font-size:1rem;">Baru Bergabung</span>
              @else
                {{ $masaKerjaTahun }}<sup style="font-size:14px;"> th</sup>
                {{ $masaKerjaBulan }}<sup style="font-size:14px;"> bln</sup>
              @endif
            </div>
          </div>
          <i class="fas fa-briefcase fa-2x text-warning"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== Charts ===== --}}
  <div class="row">
    <div class="col-lg-6 mb-4">
      <div class="card card-bnn h-100">
        <div class="card-header"><i class="fas fa-chart-bar mr-2"></i>Statistik Presensi ({{ $label }})</div>
        <div class="card-body">
          <canvas id="presensiBar" height="220"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-6 mb-4">
      <div class="card card-bnn h-100">
        <div class="card-header"><i class="fas fa-chart-pie mr-2"></i>Komposisi Presensi</div>
        <div class="card-body">
          <canvas id="presensiDonut" height="220"></canvas>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== Riwayat ===== --}}
  @if(collect($riwayat)->isNotEmpty())
  <div class="card card-bnn mb-4">
    <div class="card-header"><i class="fas fa-stream mr-2"></i>Riwayat Presensi Terbaru</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover text-sm m-0">
          <thead class="text-center">
            <tr>
              <th style="width:60px">#</th>
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
                  <span class="badge {{ $row->status=='hadir'?'badge-success':($row->status=='terlambat'?'badge-warning':'badge-danger') }}">
                    {{ ucfirst($row->status) }}
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
  const DATA = { hadir: {{ $hadir }}, terlambat: {{ $telat }}, absen: {{ $absen }} };
  const C = { green:'#28a745', yellow:'#ffc107', red:'#dc3545' };

  new Chart(document.getElementById('presensiBar'), {
    type:'bar',
    data:{
      labels:['Hadir','Terlambat','Tidak Hadir'],
      datasets:[{ data:[DATA.hadir, DATA.terlambat, DATA.absen], backgroundColor:[C.green, C.yellow, C.red], borderRadius:8 }]
    },
    options:{ plugins:{legend:{display:false}}, responsive:true, scales:{ y:{beginAtZero:true} } }
  });

  new Chart(document.getElementById('presensiDonut'), {
    type:'doughnut',
    data:{
      labels:['Hadir','Terlambat','Tidak Hadir'],
      datasets:[{ data:[DATA.hadir, DATA.terlambat, DATA.absen], backgroundColor:[C.green, C.yellow, C.red]}]
    },
    options:{ plugins:{legend:{position:'bottom'}}, cutout:'60%', responsive:true }
  });
})();
</script>
@endpush
