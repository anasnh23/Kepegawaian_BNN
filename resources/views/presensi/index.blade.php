@extends('layouts.template')
@section('title', 'Presensi Pegawai')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 300px; }
</style>

<div class="container-fluid">
    <h4 class="mb-3">Presensi Pegawai</h4>

    <div id="result" style="display:none;" class="alert mt-2"></div>

    <div class="row">
        <!-- Kamera -->
        <div class="col-md-6">
            <video id="video" width="100%" height="300" autoplay></video>
            <canvas id="canvas" style="display:none;"></canvas>
        </div>

        <!-- Map & Form -->
        <div class="col-md-6">
            <div id="map" class="mb-3"></div>
            <form id="formPresensi" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <input type="hidden" name="image_data" id="image_data">
                <div id="status-lokasi" class="mb-2">Menentukan lokasi...</div>
                <button type="submit" class="btn btn-success" id="btnPresensi" disabled>
                    <i class="fas fa-check-circle"></i> Presensi Sekarang
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const imageData = document.getElementById('image_data');
    const statusLokasi = document.getElementById('status-lokasi');
    const result = document.getElementById('result');
    const formPresensi = document.getElementById('formPresensi');
    const btnPresensi = document.getElementById('btnPresensi');

    // Konfigurasi lokasi kantor
    const kantorLat = -7.809739;
    const kantorLng = 111.975466;
    const maxRadius = 0.2; // km

    // Tampilkan kamera
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => video.srcObject = stream)
        .catch(err => alert('Tidak dapat mengakses kamera: ' + err));

    // Fungsi hitung jarak
    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) ** 2 +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    // Inisialisasi map
    const map = L.map('map').setView([kantorLat, kantorLng], 18);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Marker kantor
    L.marker([kantorLat, kantorLng]).addTo(map).bindPopup("Kantor BNN Kota Kediri").openPopup();
    L.circle([kantorLat, kantorLng], {
        color: 'green', fillColor: '#aaf0d1', fillOpacity: 0.3, radius: maxRadius * 1000
    }).addTo(map);

    // Dapatkan lokasi pengguna
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

        if (distance <= maxRadius) {
            statusLokasi.innerHTML = `<span class="text-success">✅ Dalam Area Kantor (${(distance * 1000).toFixed(1)} m)</span>`;
            btnPresensi.disabled = false;
        } else {
            statusLokasi.innerHTML = `<span class="text-danger">❌ Di luar area kantor (${(distance * 1000).toFixed(1)} m)</span>`;
            btnPresensi.disabled = true;
        }

    }, err => {
        statusLokasi.innerHTML = '<span class="text-danger">❌ Gagal mendapatkan lokasi</span>';
    });

    // Submit via AJAX
    formPresensi.addEventListener('submit', function(e) {
        e.preventDefault();

        const lat = document.getElementById('latitude').value;
        const lng = document.getElementById('longitude').value;

        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        imageData.value = canvas.toDataURL('image/jpeg');

        const data = new FormData();
        data.append('latitude', lat);
        data.append('longitude', lng);
        data.append('image_data', imageData.value);
        data.append('_token', '{{ csrf_token() }}');

        btnPresensi.disabled = true;
        btnPresensi.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Menyimpan...`;

        axios.post('{{ route("presensi.store") }}', data)
            .then(res => {
                showAlert(res.data.message, 'success');
            })
            .catch(err => {
                const msg = err.response?.data?.message || 'Presensi gagal.';
                showAlert(msg, 'danger');
            })
            .finally(() => {
                btnPresensi.disabled = false;
                btnPresensi.innerHTML = `<i class="fas fa-check-circle"></i> Presensi Sekarang`;
            });
    });

    function showAlert(message, type) {
        result.className = `alert alert-${type}`;
        result.innerText = message;
        result.style.display = 'block';
        setTimeout(() => { result.style.display = 'none'; }, 5000);
    }
</script>
@endsection
