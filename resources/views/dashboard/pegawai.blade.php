@extends('layouts.template')
@section('title', 'Dashboard Pegawai')

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
    text-transform: uppercase;
    font-size: 0.9rem;
    color: #ffc107;
  }

  .card-bnn h4 {
    font-size: 2rem;
    font-weight: 700;
    color: #fff;
  }

  .card-header.bnn-theme {
    background: #0a2647;
    color: #fff;
    font-weight: 600;
    letter-spacing: 1px;
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
    <!-- Hadir -->
    <div class="col-md-4">
      <div class="card card-bnn shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6><i class="fas fa-user-check mr-2"></i>Hadir Bulan Ini</h6>
            <h4>{{ $presensiStats['hadir'] ?? 0 }}</h4>
          </div>
          <div>
            <i class="fas fa-calendar-check fa-2x icon-clickable"
               onclick="window.location.href='{{ route('presensi.index') }}'"
               title="Lihat Presensi"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Terlambat -->
    <div class="col-md-4">
      <div class="card card-bnn shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6><i class="fas fa-clock mr-2"></i>Terlambat Bulan Ini</h6>
            <h4>{{ $presensiStats['terlambat'] ?? 0 }}</h4>
          </div>
          <div>
            <i class="fas fa-exclamation-triangle fa-2x icon-clickable"
               onclick="window.location.href='{{ route('presensi.index') }}'"
               title="Lihat Presensi"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Cuti -->
    <div class="col-md-4">
      <div class="card card-bnn shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6><i class="fas fa-plane-departure mr-2"></i>Cuti Diajukan Bulan Ini</h6>
            <h4>{{ $cutiStats }}</h4>
          </div>
          <div>
            <i class="fas fa-calendar-alt fa-2x icon-clickable mr-2"
               onclick="window.location.href='{{ route('cuti.pegawai') }}'"
               title="Ajukan Cuti"></i>
            <i class="fas fa-history fa-2x icon-clickable"
               onclick="window.location.href='{{ url('riwayat-cuti') }}'"
               title="Riwayat Cuti"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Grafik -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bnn-theme">
      <i class="fas fa-chart-bar mr-2"></i>Grafik Presensi Bulan Ini
    </div>
    <div class="card-body">
      <canvas id="presensiChart"></canvas>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('presensiChart').getContext('2d');
  new Chart(ctx, {
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
        borderRadius: 6,
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
          ticks: {
            color: '#000',
            stepSize: 1
          }
        },
        x: {
          ticks: {
            color: '#000'
          }
        }
      }
    }
  });
</script>
@endpush

@endsection
