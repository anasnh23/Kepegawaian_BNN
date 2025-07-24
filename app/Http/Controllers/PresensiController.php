<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PresensiModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function index()
    {
        return view('presensi.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image_data' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;

        // Lokasi kantor
        $kantorLat = -7.809739;
        $kantorLng = 111.975466;
        $radiusMax = 0.3;

        // Validasi lokasi
        $distance = $this->calculateDistance($latitude, $longitude, $kantorLat, $kantorLng);
        if ($distance > $radiusMax) {
            return response()->json(['status' => 'error', 'message' => 'Anda berada di luar area kantor.'], 403);
        }

        // Simpan gambar
        $imageData = $request->image_data;
        $image = str_replace('data:image/jpeg;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $imageName = 'presensi_' . Auth::id() . '_' . now()->format('Ymd_His') . '.jpg';
        Storage::disk('public')->put("presensi/{$imageName}", base64_decode($image));

        // Presensi
        $userId = Auth::id();
        $tanggal = now()->format('Y-m-d');
        $waktu = Carbon::now('Asia/Jakarta');
        $jam = $waktu->format('H:i:s');

        $existing = PresensiModel::where('id_user', $userId)->whereDate('tanggal', $tanggal)->first();

        $presensi = $existing ?: new PresensiModel();
        $presensi->id_user = $userId;
        $presensi->tanggal = $tanggal;

        if (!$existing || !$existing->jam_masuk) {
            $presensi->jam_masuk = $jam;
            $presensi->lat_masuk = $latitude;
            $presensi->long_masuk = $longitude;
            $presensi->foto_masuk = $imageName;

            // Tentukan status
            $presensi->status = $waktu->format('H:i') > '08:00' ? 'terlambat' : 'hadir';

            $presensi->save();
            return response()->json(['status' => 'success', 'message' => 'Presensi masuk berhasil']);
        } else {
            if ($waktu->lt(Carbon::createFromTime(16, 0, 0, 'Asia/Jakarta'))) {
                return response()->json(['status' => 'error', 'message' => 'Presensi pulang hanya bisa setelah jam 16:00 WIB'], 403);
            }

            $presensi->jam_pulang = $jam;
            $presensi->lat_pulang = $latitude;
            $presensi->long_pulang = $longitude;
            $presensi->foto_pulang = $imageName;

            $presensi->save();
            return response()->json(['status' => 'success', 'message' => 'Presensi pulang berhasil']);
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return $miles * 1.609344;
    }
}
