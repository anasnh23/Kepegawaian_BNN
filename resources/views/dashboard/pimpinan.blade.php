@extends('layouts.template')
@section('title', 'Dashboard Pimpinan')

@section('content')

<style>
  .card-bnn {
    background: linear-gradient(135deg, #0a2647, #144272);
    color: #fff;
    border-left: 6px solid #ffc107;
    border-radius: 12px;
    transition: all 0.3s ease-in-out;
  }
  .card-bnn:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
  .card-bnn h6 { font-weight: 600; font-size: 0.85rem; text-transform: uppercase; color: #ffc107; }
  .card-bnn h4 { font-size: 2rem; font-weight: 700; color: #fff; }
  .card-header.bnn-header { background-color: #0a2647; color: #fff; font-weight: 600; }
  canvas { background: #f8f9fa; border-radius: 8px; }
  .icon-clickable { color: #ffc107; cursor: pointer; }
  .icon-clickable:hover { color: #ffdd57; transform: scale(1.2); }
</style>

<div class="container-fluid">
  <div class="row mb-4">
    <!-- Total Pegawai -->
    <div class="col-md-3">
      <div class="card card-bnn shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6><i class="fas fa-users mr-2"></i>Total Pegawai</h6>
            <h4>{{ $totalPegawai ?? 0 }}</h4>
          </div>
          <i class="fas fa-user-friends fa-2x icon-clickable" onclick="window.location.href='{{ route('pegawai.index') }}'"></i>
        </div>
      </div>
    </div>

    <!-- Dokumen Masuk -->
    <div class="col-md-3">
      <div class="card card-bnn shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6><i class="fas fa-file-upload mr-2"></i>Dokumen Masuk</h6>
            <h4>{{ $dokumenMasuk ?? 0 }}</h4>
          </div>
          <i class="fas fa-file-alt fa-2x icon-clickable" onclick="window.location.href='{{ route('approval.dokumen') }}'"></i>
        </div>
      </div>
    </div>

    <!-- Dokumen Disetujui -->
    <div class="col-md-3">
      <div class="card card-bnn shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6><i class="fas fa-check-double mr-2"></i>Dokumen Disetujui</h6>
            <h4>{{ $dokumenSetujui ?? 0 }}</h4>
          </div>
          <i class="fas fa-history fa-2x icon-clickable" onclick="window.location.href='{{ route('riwayat.approval') }}'"></i>
        </div>
      </div>
    </div>

    <!-- Cuti Menunggu -->
    <div class="col-md-3">
      <div class="card card-bnn shadow-sm p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6><i class="fas fa-file-signature mr-2"></i>Cuti Menunggu</h6>
            <h4>{{ $cutiMenunggu ?? 0 }}</h4>
          </div>
          <i class="fas fa-hourglass-half fa-2x icon-clickable" onclick="window.location.href='{{ route('approval.dokumen') }}'"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Grafik -->
  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bnn-header"><i class="fas fa-chart-pie mr-2"></i>Distribusi Pangkat Pegawai</div>
        <div class="card-body"><canvas id="pangkatChart"></canvas></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bnn-header"><i class="fas fa-chart-bar mr-2"></i>Jenis Cuti Pegawai</div>
        <div class="card-body"><canvas id="cutiChart"></canvas></div>
      </div>
    </div>
  </div>

  <!-- Notifikasi -->
  <div class="row">
    <div class="col-md-12">
      <div class="card shadow-sm">
        <div class="card-header bnn-header"><i class="fas fa-bell mr-2"></i>Notifikasi Terbaru</div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            @forelse($notifications as $notif)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="fas fa-info-circle text-primary mr-2"></i>{{ $notif->message }}</span>
                <small class="text-muted">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</small>
              </li>
            @empty
              <li class="list-group-item text-muted text-center">Belum ada notifikasi</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('pangkatChart'), {
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
  options:{responsive:true,plugins:{legend:{display:false}}}
});

new Chart(document.getElementById('cutiChart'), {
  type: 'pie',
  data: {
    labels: {!! json_encode($jenisCuti->keys()) !!},
    datasets: [{
      data: {!! json_encode($jenisCuti->values()) !!},
      backgroundColor: ['#007bff','#28a745','#ffc107','#dc3545','#17a2b8']
    }]
  },
  options:{responsive:true,plugins:{legend:{position:'bottom'}}}
});
</script>
@endpush

@endsection
