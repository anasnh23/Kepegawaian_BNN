@extends('layouts.template')
@section('title', 'Presensi Dinas Luar')

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

  video {
    width: 100%; height: 280px; border-radius: 8px;
    border: 1px solid #dee2e6; object-fit: cover;
  }

  .btn-presensi {
    width: 100%; font-size: 16px; padding: 10px; border-radius: 8px; font-weight: 600;
    background-color: #003366; color: #fff; border: none; transition: background .3s ease;
  }
  .btn-presensi:hover { background-color: #002244; }

  .thead-bnn th{ background:#0f1f39; color:#eaf2ff; border-color:#0f1f39; }
  .thumb { width:48px; height:48px; object-fit:cover; border-radius:8px; border:1px solid #e6edf6; }

  .badge-dinas{ background:#e6f0ff; color:#003380; border:1px solid #adcfff; padding:.35rem .55rem; border-radius:10px; font-weight:700; }
</style>

<div class="container-fluid">
  {{-- ====== Kartu Kamera & Lokasi ====== --}}
  <div class="card card-presensi mb-4">
    <div class="card-header card-header-bnn">
      <i class="fas fa-map-marked-alt mr-2"></i> Presensi Dinas Luar
    </div>
    <div class="card-body">
      @if(!empty($blockDinas) && $blockDinas)
        <div class="alert alert-warning mb-3">
          Anda sudah melakukan presensi kantor hari ini, tidak bisa presensi dinas luar.
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
          <form id="formPresensiDinas">
            @csrf
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="image_data" id="image_data">

            <button type="submit" class="btn btn-presensi mt-3" id="btnPresensi" {{ !empty($blockDinas) && $blockDinas ? 'disabled' : '' }}>
              <i class="fas fa-check-circle"></i> Presensi Sekarang
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- ====== RINGKASAN PRESENSI HARI INI ====== --}}
  <div class="card card-presensi mb-3">
    <div class="card-header card-header-bnn">
      <i class="fas fa-clipboard-check mr-2"></i> Presensi Dinas Luar Hari Ini — {{ \Carbon\Carbon::parse($today ?? now())->translatedFormat('d M Y') }}
    </div>

    @if(!empty($todayPresensi))
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-bordered m-0 text-center align-middle">
            <thead class="thead-bnn">
              <tr>
                <th>Jam Masuk</th>
                <th>Foto Masuk</th>
                <th>Jam Pulang</th>
                <th>Foto Pulang</th>
                <th>Lokasi</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{{ $todayPresensi->jam_masuk ?? '-' }}</td>
                <td>
                  @if($todayPresensi->foto_masuk)
                    <a href="{{ asset('storage/'.$todayPresensi->foto_masuk) }}" target="_blank">
                      <img src="{{ asset('storage/'.$todayPresensi->foto_masuk) }}" class="thumb">
                    </a>
                  @else - @endif
                </td>
                <td>{{ $todayPresensi->jam_pulang ?? '-' }}</td>
                <td>
                  @if($todayPresensi->foto_pulang)
                    <a href="{{ asset('storage/'.$todayPresensi->foto_pulang) }}" target="_blank">
                      <img src="{{ asset('storage/'.$todayPresensi->foto_pulang) }}" class="thumb">
                    </a>
                  @else - @endif
                </td>
                <td class="text-truncate" style="max-width:200px" title="{{ $todayPresensi->lokasi ?? '-' }}">
                  {{ $todayPresensi->lokasi ?? '-' }}
                </td>
                <td><span class="badge-dinas">Dinas Luar</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    @else
      <div class="card-body">
        <div class="alert alert-info mb-0">
          Belum ada presensi dinas luar hari ini.
        </div>
      </div>
    @endif
  </div>

  {{-- ====== Riwayat ====== --}}
  @if(!empty($recent) && $recent->count())
  <div class="card card-presensi">
    <div class="card-header" style="background:#f4f6fa">
      <strong><i class="fas fa-history mr-2"></i>Riwayat Dinas Luar (7 Terakhir)</strong>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-sm table-striped m-0">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Masuk</th>
              <th>Pulang</th>
              <th>Lokasi</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recent as $r)
              <tr>
                <td>{{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d M Y') }}</td>
                <td>{{ $r->jam_masuk ?? '-' }}</td>
                <td>{{ $r->jam_pulang ?? '-' }}</td>
                <td class="text-truncate" style="max-width:200px" title="{{ $r->lokasi ?? '-' }}">
                  {{ $r->lokasi ?? '-' }}
                </td>
                <td><span class="badge-dinas">Dinas Luar</span></td>
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
  const formPresensi = document.getElementById('formPresensiDinas');
  const btnPresensi = document.getElementById('btnPresensi');

  // akses kamera
  navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => video.srcObject = stream)
    .catch(err => Swal.fire('Gagal Akses Kamera', err.message, 'error'));

  // peta
  const map = L.map('map').setView([-2.5489, 118.0149], 5); // default Indonesia
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19, attribution: '&copy; OpenStreetMap contributors'
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

    map.setView([lat, lng], 15);
  });

  // submit presensi
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

    axios.post('{{ url("/presensi-dinas/store") }}', data)
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
