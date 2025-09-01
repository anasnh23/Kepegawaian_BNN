<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PresensiModel;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class PresensiController extends Controller
{
    /** Tampilkan halaman presensi + ringkasan hari ini & riwayat */
    public function index()
    {
        $tz    = 'Asia/Jakarta';
        $today = Carbon::now($tz)->toDateString();

        // ringkasan presensi hari ini untuk user login
        $todayPresensi = PresensiModel::where('id_user', $this->authUserId())
            ->whereDate('tanggal', $today)
            ->first();

        // riwayat 7 hari kerja terakhir (opsional untuk tabel bawah)
        $recent = PresensiModel::where('id_user', $this->authUserId())
            ->orderByDesc('tanggal')
            ->limit(7)
            ->get();

        // kirim juga info jam kerja (hanya untuk ditampilkan di view)
        $config = [
            'start' => env('ABSEN_START', '08:00:00'),
            'tol'   => env('ABSEN_TOL', '00:15:00'),
            'end'   => env('ABSEN_END', '16:00:00'),
        ];

        return view('presensi.index', compact('todayPresensi', 'recent', 'config', 'today'));
    }

    /** Simpan presensi (masuk/pulang) */
    public function store(Request $request)
    {
        $request->validate([
            'image_data' => 'required',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
        ]);

        // ---- Validasi lokasi (radius KM) ----
        $kantorLat   = -7.809739;
        $kantorLng   = 111.975466;
        $radiusMaxKm = 0.3; // 300 m

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        if ($this->distanceKm($lat, $lng, $kantorLat, $kantorLng) > $radiusMaxKm) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda berada di luar area kantor.'
            ], 403);
        }

        // ---- Waktu & aturan presensi ----
        $tz      = 'Asia/Jakarta';
        $now     = Carbon::now($tz);
        $today   = $now->toDateString();
        $jamNow  = $now->format('H:i:s');

        $startStr  = env('ABSEN_START', '08:00:00');       // mulai presensi masuk
        $tolStr    = env('ABSEN_TOL', '00:15:00');         // toleransi keterlambatan
        $endStr    = env('ABSEN_END', '16:00:00');         // mulai presensi pulang

        $start  = Carbon::parse($today.' '.$startStr, $tz);
        $tol    = CarbonInterval::createFromFormat('H:i:s', $tolStr);
        $lateAt = (clone $start)->add($tol);               // lewat ini = terlambat
        $end    = Carbon::parse($today.' '.$endStr, $tz);

        // ---- Simpan foto base64 ----
        $payload  = preg_replace('#^data:image/\w+;base64,#i', '', $request->image_data);
        $payload  = str_replace(' ', '+', $payload);
        $fileName = 'presensi_'.$this->authUserId().'_'.$now->format('Ymd_His').'.jpg';
        Storage::disk('public')->put("presensi/{$fileName}", base64_decode($payload));

        // ---- Ambil / siapkan entri presensi hari ini ----
        $userId   = $this->authUserId();
        $presensi = PresensiModel::where('id_user', $userId)
            ->whereDate('tanggal', $today)
            ->first();

        // ===== PRESENSI MASUK =====
        if (!$presensi || !$presensi->jam_masuk) {
            // Jika baris sebelumnya hasil backfill "tidak hadir", kita timpa kolomnya
            $presensi = $presensi ?: new PresensiModel();
            $presensi->id_user    = $userId;
            $presensi->tanggal    = $today;
            $presensi->jam_masuk  = $jamNow;
            $presensi->lat_masuk  = $lat;
            $presensi->long_masuk = $lng;
            $presensi->foto_masuk = $fileName;

            $presensi->status = $now->lessThanOrEqualTo($lateAt) ? 'hadir' : 'terlambat';
            $presensi->save();

            return response()->json(['status' => 'success', 'message' => 'Presensi masuk berhasil']);
        }

        // ===== PRESENSI PULANG =====
        if ($presensi->jam_pulang) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda sudah melakukan presensi pulang hari ini.'
            ], 422);
        }

        if ($now->lt($end)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Presensi pulang hanya bisa setelah '.$endStr.' WIB'
            ], 403);
        }

        $presensi->jam_pulang   = $jamNow;
        $presensi->lat_pulang   = $lat;
        $presensi->long_pulang  = $lng;
        $presensi->foto_pulang  = $fileName;
        $presensi->save();

        return response()->json(['status' => 'success', 'message' => 'Presensi pulang berhasil']);
    }

    // ===================== Helpers =====================

    /** Ambil id user yang benar (id_user pada m_user) */
    protected function authUserId(): int
    {
        $u = Auth::user();
        return (int) ($u->id_user ?? $u->id ?? 0);
    }

    /** Haversine distance in KM */
    private function distanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }
}
