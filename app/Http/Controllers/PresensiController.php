<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PresensiModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        // Koordinat BNN Kota Kediri
        $kantorLatitude = -7.827925;
        $kantorLongitude = 112.005873;
        $radiusMax = 0.3; // km

        $distance = $this->calculateDistance($latitude, $longitude, $kantorLatitude, $kantorLongitude);
        if ($distance > $radiusMax) {
            return back()->with('error', 'Anda berada di luar area kantor. Presensi dibatalkan.');
        }

        // Simpan gambar ke folder (opsional)
        $imageData = $request->image_data;
        $image = str_replace('data:image/jpeg;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $imageName = 'presensi_' . Auth::id() . '_' . now()->format('Ymd_His') . '.jpg';
        Storage::disk('public')->put("presensi/{$imageName}", base64_decode($image));

        // Cek apakah sudah presensi masuk hari ini
        $existing = PresensiModel::where('id_user', Auth::id())
                    ->whereDate('tanggal', now()->format('Y-m-d'))
                    ->first();

        $presensi = $existing ?: new PresensiModel();
        $presensi->id_user = Auth::id();
        $presensi->tanggal = now()->format('Y-m-d');
        $presensi->latitude = $latitude;
        $presensi->longitude = $longitude;
        $presensi->foto = $imageName;

        if (!$existing || !$existing->jam_masuk) {
            $presensi->jam_masuk = now()->format('H:i:s');
        } else {
            $presensi->jam_pulang = now()->format('H:i:s');
        }

        $presensi->save();

        return back()->with('success', 'Presensi berhasil disimpan.');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return $miles * 1.609344; // convert to kilometers
    }
}
