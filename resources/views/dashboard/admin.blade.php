  @extends('layouts.template')
  @section('title', 'Dashboard Admin')

  @section('content')
  @php
    // ==== Normalisasi dari controller ====
    $total = (int)($totalPegawai ?? 0);

    $hadir = (int) data_get($presensiStats ?? [], 'hadir', 0);
    $telat = (int) data_get($presensiStats ?? [], 'terlambat', 0);
    $tidak = (int) data_get($presensiStats ?? [], 'tidak hadir', 0);
    $dinas = (int) data_get($presensiStats ?? [], 'dinas_luar', 0); // ✅ Tambahan Dinas Luar

    $kenaikan = (int) ($kenaikanGaji ?? 0);
    $totalCuti = is_iterable($cutiStats ?? null)
        ? array_sum((array) ($cutiStats->toArray() ?? []))
        : (int) ($cutiStats ?? 0);

    $lk = (int) ($jumlahLaki ?? 0);
    $pr = (int) ($jumlahPerempuan ?? 0);

    // Pangkat dari controller: $pangkatLabels (array), $pangkatValues (array angka)
    $labelsPangkat = $pangkatLabels ?? [];
    $valuesPangkat = array_map('intval', $pangkatValues ?? []);
    $lkPct = $total ? round($lk / $total * 100) : 0;
    $prPct = $total ? round($pr / $total * 100) : 0;

    // Favorit pangkat utk kartu
    $fav = '-';
    if (!empty($valuesPangkat)) {
        $maxVal = max($valuesPangkat);
        $idx = array_search($maxVal, $valuesPangkat);
        $fav = $labelsPangkat[$idx] ?? '-';
    }
  @endphp

  <style>
    :root{
      --bnn-navy:#003366; --bnn-navy-2:#0b2f5e; --bnn-blue:#144272;
      --bnn-gold:#f0ad4e; --bnn-gold-2:#d89a2b;
      --soft:#eef3fb; --ink:#0f172a; --line:#e6edf6; --muted:#6b7a8c;
      --green:#28a745; --yellow:#ffc107; --red:#dc3545; --blue:#007bff;
    }

    .dash-header{
      background:linear-gradient(135deg,var(--bnn-navy),#012148 60%,var(--bnn-navy-2));
      color:#fff; border-radius:14px; padding:16px 18px; position:relative; overflow:hidden;
      box-shadow:0 10px 28px rgba(0,33,72,.20);
    }
    .dash-header::after{
      content:""; position:absolute; right:-60px; top:-60px; width:200px; height:200px; opacity:.08;
      background:url('{{ asset('images/bnn-watermark.svg') }}') no-repeat center/contain;
    }

    .kpi-card{
      background: linear-gradient(135deg, var(--bnn-navy), var(--bnn-blue));
      color:#fff; border-left:6px solid var(--bnn-gold); border-radius:12px;
      transition:.25s ease; box-shadow:0 10px 26px rgba(0,33,72,.18);
      cursor:pointer;
    }
    .kpi-card:hover{ transform: translateY(-3px); box-shadow:0 14px 28px rgba(0,33,72,.26); }
    .kpi-title{ font-weight:800; font-size:.86rem; letter-spacing:.5px; color:var(--bnn-gold); text-transform:uppercase; }
    .kpi-value{ font-weight:800; font-size:2rem; color:#fff; }
    .badge-soft{ background:#fff3d6; color:#7a5600; padding:.25rem .5rem; border-radius:8px; font-weight:700; }

    .card-bnn{ border:1px solid var(--line); border-radius:14px; overflow:hidden; box-shadow:0 8px 24px rgba(16,24,40,.06); cursor:pointer; }
    .card-bnn .card-header{ background: var(--bnn-navy); color:#fff; font-weight:700; border:0; }

    .gender-bar{ height:8px; background:#e9eef6; border-radius:999px; overflow:hidden; }
    .gender-bar > span{ display:block; height:100%; }
    .bar-l{ background:#1f77b4; } .bar-p{ background:#ff7f0e; }
    canvas{ background:#f8f9fa; border-radius:10px; }
    .mini-desc{ color:var(--muted); font-size:.9rem; }
  </style>

  <div class="container-fluid">

    {{-- ======= Periode Switch ======= --}}
    <div class="mb-3 d-flex align-items-center">
      <div class="btn-group btn-group-sm mr-2" role="group" aria-label="Periode">
        <a href="{{ route('dashboard.admin', ['range' => 'month']) }}" class="btn {{ ($range ?? 'month')==='month' ? 'btn-primary' : 'btn-outline-primary' }}">Bulan Ini</a>
        <a href="{{ route('dashboard.admin', ['range' => '30d']) }}" class="btn {{ ($range ?? '')==='30d' ? 'btn-primary' : 'btn-outline-primary' }}">30 Hari</a>
        <a href="{{ route('dashboard.admin', ['range' => 'all']) }}" class="btn {{ ($range ?? '')==='all' ? 'btn-primary' : 'btn-outline-primary' }}">Semua</a>
      </div>
      <small class="text-muted">Periode: {{ $labelPeriode ?? 'Bulan Ini' }}</small>
    </div>

    {{-- ======= Header ======= --}}
    <div class="dash-header mb-4 d-flex align-items-center justify-content-between">
      <div>
        <h4 class="mb-1 font-weight-bold"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard Admin</h4>
        <div class="mini-desc">Ringkasan statistik Kepegawaian BNN</div>
      </div>
      <div>
        <span class="badge-soft"><i class="fas fa-user-friends mr-1"></i> Total Pegawai: <strong>{{ number_format($total) }}</strong></span>
      </div>
    </div>

    {{-- ======= KPI Row 1 ======= --}}
    <div class="row mb-4">
      <div class="col-md-3 mb-3">
        <div class="kpi-card p-3 h-100" onclick="location.href='{{ route('pegawai.index') }}'">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="kpi-title"><i class="fas fa-users mr-2"></i>Total Pegawai</div>
              <div class="kpi-value">{{ number_format($total) }}</div>
            </div>
            <i class="fas fa-user-friends fa-2x text-warning"></i>
          </div>
          <div class="mt-2 mini-desc">L: {{ $lk }} • P: {{ $pr }}</div>
        </div>
      </div>

      <div class="col-md-3 mb-3">
        <div class="kpi-card p-3 h-100" onclick="location.href='{{ route('pegawai.index') }}?gender=L'">
          <div class="kpi-title"><i class="fas fa-male mr-2"></i>Laki-laki</div>
          <div class="d-flex justify-content-between align-items-end">
            <div class="kpi-value">{{ number_format($lk) }}</div>
            <span class="badge-soft">~ {{ $lkPct }}%</span>
          </div>
          <div class="gender-bar mt-2"><span class="bar-l" style="width: {{ $lkPct }}%"></span></div>
        </div>
      </div>

      <div class="col-md-3 mb-3">
        <div class="kpi-card p-3 h-100" onclick="location.href='{{ route('pegawai.index') }}?gender=P'">
          <div class="kpi-title"><i class="fas fa-female mr-2"></i>Perempuan</div>
          <div class="d-flex justify-content-between align-items-end">
            <div class="kpi-value">{{ number_format($pr) }}</div>
            <span class="badge-soft">~ {{ $prPct }}%</span>
          </div>
          <div class="gender-bar mt-2"><span class="bar-p" style="width: {{ $prPct }}%"></span></div>
        </div>
      </div>

      <div class="col-md-3 mb-3">
        <div class="kpi-card p-3 h-100" onclick="location.href='/kgp'">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="kpi-title"><i class="fas fa-chart-line mr-2"></i>Kenaikan Gaji Tahun Ini</div>
              <div class="kpi-value">{{ number_format($kenaikan) }}</div>
            </div>
            <i class="fas fa-money-bill-wave fa-2x text-warning"></i>
          </div>
          <div class="mini-desc mt-2">Pegawai yang mengalami KGB</div>
        </div>
      </div>
    </div>

    {{-- ======= KPI Row 2 ======= --}}
    <div class="row mb-4">
      <div class="col-md-3 mb-3">
        <div class="kpi-card p-3 h-100" onclick="location.href='{{ route('presensi.admin') }}'">
          <div class="kpi-title"><i class="fas fa-check-circle mr-2"></i>Presensi Hadir</div>
          <div class="kpi-value">{{ number_format($hadir) }}</div>
          <div class="mini-desc mt-2">Terlambat: {{ number_format($telat) }} • Tidak Hadir: {{ number_format($tidak) }}</div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="kpi-card p-3 h-100" onclick="location.href='/cutiadmin'">
          <div class="kpi-title"><i class="fas fa-plane-departure mr-2"></i>Cuti</div>
          <div class="kpi-value">{{ number_format($totalCuti) }}</div>
          <div class="mini-desc mt-2">Total pengajuan cuti dalam periode</div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="kpi-card p-3 h-100" onclick="location.href='{{ route('pegawai.index') }}'">
          <div class="kpi-title"><i class="fas fa-user-shield mr-2"></i>Golongan Pangkat Terbanyak</div>
          <div class="kpi-value" style="font-size:1.6rem">{{ $fav }}</div>
          <div class="mini-desc mt-2">Dominan di populasi pegawai</div>
        </div>
      </div>
      {{-- ✅ KPI Baru: Presensi Dinas Luar --}}
      <div class="col-md-3 mb-3">
        <div class="kpi-card p-3 h-100" onclick="location.href='{{ url("presensi-dinas") }}'">
          <div class="kpi-title"><i class="fas fa-car-side mr-2"></i>Dinas Luar</div>
          <div class="kpi-value">{{ number_format($dinas) }}</div>
          <div class="mini-desc mt-2">Total presensi dinas luar</div>
        </div>
      </div>
    </div>

    {{-- ======= Charts ======= --}}
    <div class="row">
      <div class="col-lg-4 mb-4">
        <div class="card card-bnn h-100" onclick="location.href='{{ route('pegawai.index') }}'">
          <div class="card-header"><i class="fas fa-venus-mars mr-2"></i>Komposisi Gender</div>
          <div class="card-body"><canvas id="genderChart" height="240"></canvas></div>
        </div>
      </div>
      <div class="col-lg-4 mb-4">
        <div class="card card-bnn h-100" onclick="location.href='{{ route('presensi.admin') }}'">
          <div class="card-header"><i class="fas fa-chart-bar mr-2"></i>Statistik Presensi</div>
          <div class="card-body"><canvas id="presensiChart" height="240"></canvas></div>
        </div>
      </div>
      <div class="col-lg-4 mb-4">
        <div class="card card-bnn h-100" onclick="location.href='{{ route('pegawai.index') }}'">
          <div class="card-header"><i class="fas fa-user-shield mr-2"></i>Distribusi Golongan Pangkat</div>
          <div class="card-body"><canvas id="pangkatChart" height="240"></canvas></div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
  (function(){
    const C = { navy:'#003366', gold:'#f0ad4e', green:'#28a745', yellow:'#ffc107', red:'#dc3545', blue:'#007bff' };

    const GENDER = { laki: {{ $lk }}, perempuan: {{ $pr }} };
    const PRESENSI = { hadir: {{ $hadir }}, terlambat: {{ $telat }}, tidak: {{ $tidak }}, dinas: {{ $dinas }} }; // ✅ Tambahan dinas
    const PANGKAT_LABELS = {!! json_encode(array_values($labelsPangkat), JSON_UNESCAPED_UNICODE) !!};
    const PANGKAT_VALUES = {!! json_encode(array_values($valuesPangkat), JSON_NUMERIC_CHECK) !!};

    new Chart(document.getElementById('genderChart').getContext('2d'), {
      type: 'doughnut',
      data: { labels: ['Laki-laki', 'Perempuan'], datasets: [{ data: [GENDER.laki, GENDER.perempuan], backgroundColor: [C.navy, C.gold], borderColor:'#fff', borderWidth:2 }] },
      options: { responsive:true, plugins:{ legend:{ position:'bottom', labels:{ boxWidth:14 } } }, cutout:'62%' }
    });

    new Chart(document.getElementById('presensiChart').getContext('2d'), {
      type: 'bar',
      data: { labels: ['Hadir', 'Terlambat', 'Tidak Hadir', 'Dinas Luar'], 
              datasets: [{ label:'Jumlah', data:[PRESENSI.hadir, PRESENSI.terlambat, PRESENSI.tidak, PRESENSI.dinas], 
              backgroundColor:[C.green, C.yellow, C.red, C.blue], borderRadius:8 }] },
      options: { responsive:true, plugins:{ legend:{ display:false } }, 
                scales:{ y:{ beginAtZero:true, grid:{ color:'#e9eef6' } }, 
                          x:{ grid:{ display:false } } } }
    });

    new Chart(document.getElementById('pangkatChart').getContext('2d'), {
      type: 'bar',
      data: { labels: PANGKAT_LABELS, datasets: [{ label:'Jumlah Pegawai', data: PANGKAT_VALUES, backgroundColor: C.blue, borderRadius:8 }] },
      options: { indexAxis:'y', responsive:true, plugins:{ legend:{ display:false } }, scales:{ x:{ beginAtZero:true, grid:{ color:'#e9eef6' } }, y:{ grid:{ display:false } } } }
    });
  })();
  </script>
  @endpush
  @endsection
