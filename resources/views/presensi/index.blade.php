@extends('layouts.template')
@section('title', 'Presensi Pegawai')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  #map { height: 300px; border-radius: 10px; }

  .card-presensi {
    background: #f9fafb;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  }

  .card-header-bnn {
    background-color: #1c2f48;
    color: #ffffff;
    font-weight: 600;
    border-bottom: 3px solid #ffc107;
  }

  .status-info { font-size: 14px; font-weight: 500; margin-top: 8px; }
  .status-success { color: #198754; }
  .status-danger  { color: #dc3545; }

  video {
    width: 100%; height: 280px; border-radius: 8px;
    border: 1px solid #dee2e6; object-fit: cover;
  }

  .btn-presensi {
    width: 100%; font-size: 16px; padding: 10px; border-radius: 8px; font-weight: 600;
    background-color: #003366; color: #fff; border: none; transition: background .3s ease;
  }
  .btn-presensi:hover { background-color: #002244; }

  /* Ringkasan Hari Ini */
  .thead-bnn th{ background:#0f1f39; color:#eaf2ff; border-color:#0f1f39; }
  .badge-status{ font-weight:700; border-radius:10px; padding:.35rem .55rem; }
  .badge-hadir{ background:#e7f6ec; color:#146c2e; border:1px solid #bfe5c8; }
  .badge-terlambat{ background:#fff7e6; color:#7a4d00; border:1px solid #ffe0ad; }
  .badge-dinas{ background:#e6f0ff; color:#003380; border:1px solid #adcfff; }
  .badge-absen{ background:#fdeaea; color:#842029; border:1px solid #f8c2c7; }
  .thumb { width:48px; height:48px; object-fit:cover; border-radius:8px; border:1px solid #e6edf6; }
</style>

<div class="container-fluid">
  {{-- ====== Kartu Kamera & Lokasi ====== --}}
  <div class="card card-presensi mb-4">
    <div class="card-header card-header-bnn">
      <i class="fas fa-fingerprint mr-2"></i> Presensi Pegawai BNN
    </div>
    <div class="card-body">
      @if(!empty($blockKantor) && $blockKantor)
        <div class="alert alert-warning mb-3">
          Anda sudah melakukan presensi dinas luar hari ini, tidak bisa presensi kantor.
        </div>
      @endif
      <div class="row">
        <!-- Kamera -->
        <div class="col-md-6 mb-3">
          <video id="video" autoplay muted></video>
          <canvas id="canvas" style="display:none;"></canvas>
        </div>

        <!-- Peta & Form -->
        <div class="col-md-6">
          <div id="map" class="mb-3"></div>
          <form id="formPresensi">
            @csrf
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="image_data" id="image_data">
            <div id="status-lokasi" class="status-info text-muted">📍 Menentukan lokasi Anda...</div>
            <button type="submit" class="btn btn-presensi mt-3" id="btnPresensi" {{ !empty($blockKantor) && $blockKantor ? 'disabled' : '' }}>
              <i class="fas fa-check-circle"></i> Presensi Sekarang
            </button>
          </form>
          @isset($config)
            <div class="small text-muted mt-2">
              Jam masuk: <b>{{ $config['start'] ?? '08:00:00' }}</b>,
              toleransi: <b>{{ $config['tol'] ?? '00:15:00' }}</b>,
              pulang mulai: <b>{{ $config['end'] ?? '16:00:00' }}</b>.
            </div>
          @endisset
        </div>
      </div>
    </div>
  </div>

  {{-- ====== RINGKASAN PRESENSI HARI INI ====== --}}
  <div class="card card-presensi mb-3">
    <div class="card-header card-header-bnn">
      <i class="fas fa-clipboard-check mr-2"></i> Presensi Hari Ini — {{ \Carbon\Carbon::parse($today ?? now())->translatedFormat('d M Y') }}
    </div>

    @if(!empty($todayPresensi))
      @php
        $st = strtolower($todayPresensi->status ?? '-');
        $badgeClass = match($st) {
            'hadir'      => 'badge-hadir',
            'terlambat'  => 'badge-terlambat',
            'dinas_luar' => 'badge-dinas',
            default      => 'badge-absen'
        };
        $label = $st === 'tidak hadir' ? 'Tidak Hadir' : ucfirst($st);
      @endphp
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-bordered m-0">
            <thead class="text-center thead-bnn">
              <tr>
                <th>Jam Masuk</th>
                <th>Foto Masuk</th>
                <th>Jam Pulang</th>
                <th>Foto Pulang</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody class="text-center align-middle">
              <tr>
                <td>{{ $todayPresensi->jam_masuk ?? '-' }}</td>
                <td>
                  @if($todayPresensi->foto_masuk)
                    <a href="{{ asset('storage/'.$todayPresensi->foto_masuk) }}" target="_blank">
                      <img src="{{ asset('storage/'.$todayPresensi->foto_masuk) }}" class="thumb" alt="Masuk">
                    </a>
                  @else - @endif
                </td>
                <td>{{ $todayPresensi->jam_pulang ?? '-' }}</td>
                <td>
                  @if($todayPresensi->foto_pulang)
                    <a href="{{ asset('storage/'.$todayPresensi->foto_pulang) }}" target="_blank">
                      <img src="{{ asset('storage/'.$todayPresensi->foto_pulang) }}" class="thumb" alt="Pulang">
                    </a>
                  @else - @endif
                </td>
                <td><span class="badge-status {{ $badgeClass }}">{{ $label }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    @else
      <div class="card-body">
        <div class="alert alert-info mb-0">
          Belum ada presensi hari ini. Silakan lakukan presensi masuk.
        </div>
      </div>
    @endif
  </div>

  {{-- ====== (Opsional) Riwayat 7 Hari Terakhir ====== --}}
  @if(!empty($recent) && $recent->count())
  <div class="card card-presensi">
    <div class="card-header" style="background:#f4f6fa">
      <strong><i class="fas fa-history mr-2"></i>Riwayat 7 Hari Terakhir</strong>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-sm table-striped m-0">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Masuk</th>
              <th>Pulang</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recent as $r)
              @php
                $st = strtolower($r->status ?? '-');
                $badge = match($st) {
                    'hadir'      => 'badge badge-success',
                    'terlambat'  => 'badge badge-warning',
                    'dinas_luar' => 'badge badge-primary',
                    default      => 'badge badge-danger'
                };
                $label = $st === 'tidak hadir' ? 'Tidak Hadir' : ucfirst($st);
              @endphp
              <tr>
                <td>{{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d M Y') }}</td>
                <td>{{ $r->jam_masuk ?? '-' }}</td>
                <td>{{ $r->jam_pulang ?? '-' }}</td>
                <td><span class="{{ $badge }}">{{ $label }}</span></td>
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const video = document.getElementById('video');
  const canvas = document.getElementById('canvas');
  const imageData = document.getElementById('image_data');
  const statusLokasi = document.getElementById('status-lokasi');
  const formPresensi = document.getElementById('formPresensi');
  const btnPresensi = document.getElementById('btnPresensi');

  const kantorLat = -7.809739;
  const kantorLng = 111.975466;
  const maxRadius = 0.3; // km

  // Akses kamera
  navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => video.srcObject = stream)
    .catch(err => Swal.fire('Gagal Akses Kamera', err.message, 'error'));

  // Hitung jarak (Haversine sederhana)
  function getDistance(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2)**2 +
              Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) *
              Math.sin(dLon/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); // km
  }

  // Peta
  const map = L.map('map').setView([kantorLat, kantorLng], 18);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19, attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  L.marker([kantorLat, kantorLng]).addTo(map).bindPopup("Kantor BNN").openPopup();
  L.circle([kantorLat, kantorLng], {
    color: '#003366', fillColor: '#aaf0d1', fillOpacity: 0.2, radius: maxRadius * 1000
  }).addTo(map);

  navigator.geolocation.getCurrentPosition(pos => {
    const lat = pos.coords.latitude;
    const lng = pos.coords.longitude;
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;

    L.marker([lat, lng], {
      icon: L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/149/149060.png',
        iconSize: [30, 30]
      })
    }).addTo(map).bindPopup("Lokasi Anda").openPopup();

    const distance = getDistance(lat, lng, kantorLat, kantorLng);
    const meter = (distance * 1000).toFixed(1);

    if (distance <= maxRadius) {
      statusLokasi.innerHTML = `<span class="status-success">✅ Anda dalam area kantor (${meter} m)</span>`;
      if(!btnPresensi.hasAttribute('disabled')) {
        btnPresensi.disabled = false;
      }
    } else {
      statusLokasi.innerHTML = `<span class="status-danger">❌ Anda di luar area kantor (${meter} m)</span>`;
      btnPresensi.disabled = true;
    }
  }, err => {
    statusLokasi.innerHTML = `<span class="status-danger">❌ Gagal mendapatkan lokasi</span>`;
  });

  // Submit Presensi
  formPresensi.addEventListener('submit', function(e) {
    e.preventDefault();

    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    imageData.value = canvas.toDataURL('image/jpeg');

    const data = new FormData(this);
    btnPresensi.disabled = true;
    btnPresensi.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Mengirim...`;

    axios.post('{{ route("presensi.store") }}', data)
      .then(res => {
        Swal.fire({ icon: 'success', title: 'Presensi Berhasil', text: res.data.message, showConfirmButton: false, timer: 2500 })
          .then(() => window.location.reload());
      })
      .catch(err => {
        Swal.fire('Presensi Gagal', err.response?.data?.message || 'Terjadi kesalahan.', 'error');
      })
      .finally(() => {
        btnPresensi.disabled = false;
        btnPresensi.innerHTML = `<i class="fas fa-check-circle"></i> Presensi Sekarang`;
      });
  });
</script>
@endpush
