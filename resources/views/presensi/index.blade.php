@extends('layouts.template')
@section('title', 'Presensi Pegawai')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>#map { height: 300px; }</style>

<div class="container-fluid">
    <h4 class="mb-3">Presensi Pegawai</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <!-- Kamera -->
        <div class="col-md-6">
            <video id="video" width="100%" height="300" autoplay></video>
            <canvas id="canvas" style="display:none;"></canvas>
        </div>

        <!-- Map dan Form -->
        <div class="col-md-6">
            <div id="map" class="mb-3"></div>
            <form method="POST" action="{{ route('presensi.store') }}" onsubmit="return validateLocation()" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <input type="hidden" name="image_data" id="image_data">
                <div class="mb-2" id="status-lokasi">Menentukan lokasi...</div>
                <button type="submit" class="btn btn-success" id="btnPresensi" disabled>
                    <i class="fas fa-check-circle"></i> Presensi Sekarang
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Kamera
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const imageData = document.getElementById('image_data');

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => video.srcObject = stream)
        .catch(err => alert('Tidak dapat mengakses kamera: ' + err));

    // Koordinat kantor BNN Kota Kediri
    const kantorLat = -7.827925;
    const kantorLng = 112.005873;
    const maxRadius = 0.2; // km (200 meter)

    // Hitung jarak Haversine
    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) ** 2 +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    // Inisialisasi peta
    const map = L.map('map').setView([kantorLat, kantorLng], 18);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Marker kantor dan radius
    L.marker([kantorLat, kantorLng]).addTo(map).bindPopup("Kantor BNN Kota Kediri").openPopup();
    L.circle([kantorLat, kantorLng], {
        color: 'green',
        fillColor: '#aaf0d1',
        fillOpacity: 0.3,
        radius: maxRadius * 1000
    }).addTo(map);

    // Lokasi pengguna
    navigator.geolocation.getCurrentPosition(pos => {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        const marker = L.marker([lat, lng], {
            icon: L.icon({ iconUrl: 'https://cdn-icons-png.flaticon.com/512/149/149060.png', iconSize: [30, 30] })
        }).addTo(map).bindPopup("Lokasi Anda").openPopup();

        const distance = getDistance(lat, lng, kantorLat, kantorLng);
        const status = document.getElementById('status-lokasi');

        if (distance <= maxRadius) {
            status.innerHTML = `<span class="text-success">✅ Dalam Area Kantor (${(distance * 1000).toFixed(1)} m)</span>`;
            document.getElementById('btnPresensi').disabled = false;
        } else {
            status.innerHTML = `<span class="text-danger">❌ Di luar area kantor (${(distance * 1000).toFixed(1)} m)</span>`;
            document.getElementById('btnPresensi').disabled = true;
        }

    }, err => {
        document.getElementById('status-lokasi').innerHTML = '<span class="text-danger">❌ Gagal mendapatkan lokasi</span>';
    });

    // Ambil gambar
    function validateLocation() {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        imageData.value = canvas.toDataURL('image/jpeg');
        return true;
    }
</script>
@endsection