<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PresensiModel;
use Carbon\Carbon;

class PresensiDinasController extends Controller
{
    /** Tampilkan halaman presensi dinas luar */
    public function index()
    {
        $tz    = 'Asia/Jakarta';
        $today = Carbon::now($tz)->toDateString();

        // presensi hari ini untuk user login
        $todayPresensi = PresensiModel::where('id_user', $this->authUserId())
            ->whereDate('tanggal', $today)
            ->where('status', 'dinas_luar')
            ->first();

        // riwayat presensi dinas luar terakhir
        $recent = PresensiModel::where('id_user', $this->authUserId())
            ->where('status', 'dinas_luar')
            ->orderByDesc('tanggal')
            ->limit(7)
            ->get();

        return view('presensi.dinas.index', compact('todayPresensi', 'recent', 'today'));
    }

    /** Simpan presensi dinas luar (masuk/pulang) */
    public function store(Request $request)
    {
        $request->validate([
            'image_data' => 'required',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
        ]);

        $tz    = 'Asia/Jakarta';
        $now   = Carbon::now($tz);
        $today = $now->toDateString();
        $jamNow = $now->format('H:i:s');

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;
        $alamat = $this->getAddressFromCoordinates($lat, $lng) ?? ($lat.', '.$lng);

        $userId   = $this->authUserId();
        $presensi = PresensiModel::where('id_user', $userId)
            ->whereDate('tanggal', $today)
            ->where('status', 'dinas_luar')
            ->first();

        // simpan foto
        $payload  = preg_replace('#^data:image/\w+;base64,#i', '', $request->image_data);
        $payload  = str_replace(' ', '+', $payload);
        $fileName = 'presensi_dinas_'.$userId.'_'.$now->format('Ymd_His').'.jpg';
        Storage::disk('public')->put("presensi/{$fileName}", base64_decode($payload));

        // ===== PRESENSI MASUK =====
        if (!$presensi || !$presensi->jam_masuk) {
            $presensi = $presensi ?: new PresensiModel();
            $presensi->id_user    = $userId;
            $presensi->tanggal    = $today;
            $presensi->jam_masuk  = $jamNow;
            $presensi->lat_masuk  = $lat;
            $presensi->long_masuk = $lng;
            $presensi->foto_masuk = $fileName;
            $presensi->status     = 'dinas_luar';
            $presensi->lokasi     = $alamat;
            $presensi->save();

            return response()->json(['status'=>'success','message'=>'Presensi dinas luar masuk berhasil']);
        }

        // ===== PRESENSI PULANG =====
        if ($presensi->jam_pulang) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda sudah melakukan presensi pulang dinas luar hari ini.'
            ], 422);
        }

        $presensi->jam_pulang   = $jamNow;
        $presensi->lat_pulang   = $lat;
        $presensi->long_pulang  = $lng;
        $presensi->foto_pulang  = $fileName;
        // simpan alamat hanya kalau kosong
        if (!$presensi->lokasi) {
            $presensi->lokasi = $alamat;
        }
        $presensi->save();

        return response()->json(['status'=>'success','message'=>'Presensi dinas luar pulang berhasil']);
    }

    // ===================== Helpers =====================

    protected function authUserId(): int
    {
        $u = Auth::user();
        return (int) ($u->id_user ?? $u->id ?? 0);
    }

    /** Reverse geocoding (lat,long → alamat readable) */
    private function getAddressFromCoordinates(float $lat, float $lng): ?string
    {
        try {
            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=18&addressdetails=1";
            $opts = [
                "http" => [
                    "header" => "User-Agent: BNN-Presensi-App/1.0\r\n"
                ]
            ];
            $context = stream_context_create($opts);
            $response = @file_get_contents($url, false, $context);
            if (!$response) return null;

            $json = json_decode($response, true);
            return $json['display_name'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
