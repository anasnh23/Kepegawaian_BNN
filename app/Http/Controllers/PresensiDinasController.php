<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Models\PresensiModel;
use Carbon\Carbon;

class PresensiDinasController extends Controller
{
    private string $tz = 'Asia/Jakarta';

    /** Tampilkan halaman presensi dinas luar */
    public function index()
    {
        $today = Carbon::now($this->tz)->toDateString();
        $userId = $this->authUserId();

        $todayPresensi = PresensiModel::where('id_user', $userId)
            ->whereDate('tanggal', $today)
            ->where('status', 'dinas_luar')
            ->first();

        $recent = PresensiModel::where('id_user', $userId)
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
            'image_data' => 'required|string',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
        ]);

        $now     = Carbon::now($this->tz);
        $today   = $now->toDateString();
        $jamNow  = $now->format('H:i:s');
        $userId  = $this->authUserId();

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        // ⛔ Tolak jika sudah ada presensi kantor (status != dinas_luar) di hari ini
        $existAny = PresensiModel::where('id_user', $userId)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existAny && $existAny->status !== 'dinas_luar') {
            return response()->json([
                'code'    => 422,
                'status'  => 'error',
                'message' => 'Anda sudah melakukan presensi kantor hari ini, tidak bisa presensi dinas luar.',
            ], 422);
        }

        // 📷 Validasi & decode image base64 (jpeg/png/webp saja)
        [$binary, $ext] = $this->decodeBase64ImageOrFail($request->image_data);

        // 📍 Ambil alamat (reverse geocode)
        $alamat = $this->reverseGeocode($lat, $lng) ?? sprintf('%.6f, %.6f', $lat, $lng);

        // 💾 Simpan foto ke storage publik (folder rapi per Y/m/d/user)
        $fileName = sprintf('presensi_dinas_%s_%s.%s', $userId, $now->format('Ymd_His'), $ext);
        $path     = "presensi/{$now->format('Y/m/d')}/{$userId}/{$fileName}";
        Storage::disk('public')->put($path, $binary);

        // 🔒 Transaksi + lock untuk cegah race-condition
        return DB::transaction(function () use ($userId, $today, $jamNow, $lat, $lng, $alamat, $path) {

            // Kunci baris presensi user-hari ini (jika ada)
            $presensi = PresensiModel::lockForUpdate()
                ->where('id_user', $userId)
                ->whereDate('tanggal', $today)
                ->first();

            // ===== MASUK =====
            if (!$presensi || !$presensi->jam_masuk) {
                if (!$presensi) {
                    // Buat baris baru dengan status dinas_luar
                    $presensi = new PresensiModel();
                    $presensi->id_user = $userId;
                    $presensi->tanggal = $today;
                    $presensi->status  = 'dinas_luar';
                } else {
                    // Jika baris ada tapi status bukan dinas_luar (harusnya sudah ditolak di atas),
                    // tetap amankan di sini juga:
                    if ($presensi->status !== 'dinas_luar') {
                        return response()->json([
                            'code'    => 422,
                            'status'  => 'error',
                            'message' => 'Anda sudah melakukan presensi kantor hari ini.',
                        ], 422);
                    }
                }

                $presensi->jam_masuk  = $jamNow;
                $presensi->lat_masuk  = $lat;
                $presensi->long_masuk = $lng;
                $presensi->foto_masuk = $path;
                $presensi->lokasi     = $alamat;
                $presensi->save();

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    'message' => 'Presensi dinas luar masuk berhasil',
                    'data'    => [
                        'tanggal' => $today,
                        'jam'     => $jamNow,
                        'lokasi'  => $alamat,
                    ],
                ]);
            }

            // ===== PULANG =====
            if ($presensi->jam_pulang) {
                return response()->json([
                    'code'    => 422,
                    'status'  => 'error',
                    'message' => 'Anda sudah melakukan presensi pulang dinas luar hari ini.',
                ], 422);
            }

            $presensi->jam_pulang  = $jamNow;
            $presensi->lat_pulang  = $lat;
            $presensi->long_pulang = $lng;
            $presensi->foto_pulang = $path;
            if (!$presensi->lokasi) $presensi->lokasi = $alamat;
            $presensi->save();

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Presensi dinas luar pulang berhasil',
                'data'    => [
                    'tanggal' => $today,
                    'jam'     => $jamNow,
                ],
            ]);
        });
    }

    // ===================== Helpers =====================

    protected function authUserId(): int
    {
        $u = Auth::user();
        return (int) ($u->id_user ?? $u->id ?? 0);
    }

    /** Decode data URL base64 ke biner + validasi MIME & size */
    private function decodeBase64ImageOrFail(string $dataUrl): array
    {
        if (!preg_match('#^data:image/(jpeg|jpg|png|webp);base64,#i', $dataUrl, $m)) {
            throw ValidationException::withMessages([
                'image_data' => 'Format gambar harus JPEG/PNG/WEBP (base64 data URL).'
            ]);
        }
        $ext = strtolower($m[1] === 'jpg' ? 'jpeg' : $m[1]);

        $base64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
        $base64 = str_replace(' ', '+', $base64);
        $binary = base64_decode($base64, true);

        if ($binary === false) {
            throw ValidationException::withMessages([
                'image_data' => 'Gagal decode gambar base64.'
            ]);
        }

        // Batas 5MB
        if (strlen($binary) > 5 * 1024 * 1024) {
            throw ValidationException::withMessages([
                'image_data' => 'Ukuran gambar terlalu besar (maks 5MB).'
            ]);
        }

        return [$binary, $ext];
    }

    /** Reverse geocoding (lat,long → alamat readable) via OSM Nominatim */
    private function reverseGeocode(float $lat, float $lng): ?string
    {
        try {
            $resp = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => config('app.name', 'PresensiApp') . '/1.0 (admin@example.com)',
                ])->get('https://nominatim.openstreetmap.org/reverse', [
                    'format'         => 'json',
                    'lat'            => $lat,
                    'lon'            => $lng,
                    'zoom'           => 18,
                    'addressdetails' => 1,
                ]);

            if ($resp->successful()) {
                return $resp->json('display_name');
            }
        } catch (\Throwable $e) {
            // log jika perlu
        }
        return null;
    }
}
