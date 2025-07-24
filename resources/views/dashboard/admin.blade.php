@extends('layouts.template')
@section('title', 'Dashboard Admin')

@section('content')

<style>
  .card-bnn {
    background: linear-gradient(135deg, #0a2647, #144272);
    color: #fff;
    border-left: 6px solid #ffc107;
    border-radius: 12px;
    transition: all 0.3s ease-in-out;
  }

  .card-bnn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
  }

  .card-bnn h6 {
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    color: #ffc107;
  }

  .card-bnn h4 {
    font-size: 2rem;
    font-weight: 700;
    color: #fff;
  }

  .card-header.bnn-header {
    background-color: #0a2647;
    color: #fff;
    font-weight: 600;
  }

  canvas {
    background: #f8f9fa;
    border-radius: 8px;
  }

  .icon-clickable {
    color: #ffc107;
    cursor: pointer;
  }

  .icon-clickable:hover {
    color: #ffdd57;
    transform: scale(1.2);
  }
</style>

<div class="container-fluid">
  <div class="row mb-4">
    <!-- Total Pegawai -->
    <div class="col-md-3">
      <div class="card card-bnn shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6><i class="fas fa-users mr-2"></i>Total Pegawai</h6>
            <h4>{{ $totalPegawai }}</h4>
          </div>
          <div>
            <i class="fas fa-user-friends fa-2x icon-clickable"
               title="Lihat Data Pegawai"
               onclick="window.location.href='{{ route('pegawai.index') }}'"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Kenaikan Gaji -->
    <div class="col-md-3">
      <div class="card card-bnn shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6><i class="fas fa-chart-line mr-2"></i>Kenaikan Gaji Tahun Ini</h6>
            <h4>{{ $kenaikanGaji }}</h4>
          </div>
          <div>
            <i class="fas fa-money-bill-wave fa-2x icon-clickable"
               title="Kenaikan Gaji Berkala"
               onclick="window.location.href='/kgp'"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Presensi Hadir -->
    <div class="col-md-3">
      <div class="card card-bnn shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6><i class="fas fa-check-circle mr-2"></i>Presensi Hadir</h6>
            <h4>{{ $presensiStats['hadir'] ?? 0 }}</h4>
          </div>
          <div>
            <i class="fas fa-calendar-check fa-2x icon-clickable"
               title="Data Presensi"
               onclick="window.location.href='{{ route('presensi.admin') }}'"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Pengajuan Cuti -->
    <div class="col-md-3">
      <div class="card card-bnn shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6><i class="fas fa-plane-departure mr-2"></i>Cuti Bulan Ini</h6>
            <h4>{{ array_sum($cutiStats->toArray()) }}</h4>
          </div>
          <div>
            <i class="fas fa-suitcase fa-2x icon-clickable"
               title="Kelola Pengajuan Cuti"
               onclick="window.location.href='/cutiadmin'"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- GRAFIK --}}
  <div class="row">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bnn-header">
          <i class="fas fa-chart-bar mr-2"></i>Statistik Presensi Bulan Ini
        </div>
        <div class="card-body">
          <canvas id="presensiChart"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bnn-header">
          <i class="fas fa-user-shield mr-2"></i>Distribusi Golongan Pangkat
        </div>
        <div class="card-body">
          <canvas id="pangkatChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const presensiChart = document.getElementById('presensiChart').getContext('2d');
  new Chart(presensiChart, {
    type: 'bar',
    data: {
      labels: ['Hadir', 'Terlambat', 'Tidak Hadir'],
      datasets: [{
        label: 'Jumlah Hari',
        data: [
          {{ $presensiStats['hadir'] ?? 0 }},
          {{ $presensiStats['terlambat'] ?? 0 }},
          {{ $presensiStats['tidak hadir'] ?? 0 }}
        ],
        backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#0a2647',
          titleColor: '#fff',
          bodyColor: '#fff'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { color: '#000' }
        },
        x: {
          ticks: { color: '#000' }
        }
      }
    }
  });

  const pangkatChart = document.getElementById('pangkatChart').getContext('2d');
  new Chart(pangkatChart, {
    type: 'bar',
    data: {
      labels: {!! json_encode($golonganPangkat->keys()) !!},
      datasets: [{
        label: 'Jumlah Pegawai',
        data: {!! json_encode($golonganPangkat->values()) !!},
        backgroundColor: '#007bff',
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { color: '#000' }
        },
        x: {
          ticks: { color: '#000' }
        }
      }
    }
  });
</script>
@endpush

@endsection
