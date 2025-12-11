<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PresensiModel;
use App\Models\MUser;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresensiExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PresensiAdminController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        // =========================
        // 1) Tentukan range tanggal
        // =========================
        $tipe         = $request->filter;     // harian | mingguan | bulanan
        $tanggalParam = null;
        $dates        = [];

        if ($tipe === 'harian' && $request->filled('tanggal')) {

            $d = Carbon::parse($request->tanggal, 'Asia/Jakarta');
            $tanggalParam = $d->toDateString();
            if ($this->isWorkday($d)) $dates[] = $tanggalParam;

        } elseif ($tipe === 'mingguan' && $request->filled('minggu')) {

            $start = Carbon::parse($request->minggu)->startOfWeek(Carbon::MONDAY);
            $end   = $start->copy()->addDays(4); // Senin–Jumat
            $tanggalParam = $start->toDateString();
            $dates = $this->workdaysBetween($start, $end);

        } elseif ($tipe === 'bulanan' && $request->filled('bulan')) {

            $c = Carbon::parse($request->bulan);
            $tanggalParam = $request->bulan;
            $dates = $this->workdaysBetween($c->copy()->startOfMonth(), $c->copy()->endOfMonth());

        } else {

            // DEFAULT = Hari ini
            $today = Carbon::today('Asia/Jakarta');
            $tipe = 'harian';
            $tanggalParam = $today->toDateString();
            if ($this->isWorkday($today)) $dates[] = $tanggalParam;
        }

        // ==========================================================
        // 2) Backfill realtime untuk semua pegawai
        // ==========================================================
        $this->backfillTidakHadirRealtime($dates);

        // =========================
        // 3) Query data presensi
        // =========================
        $q = PresensiModel::with('user')->orderBy('tanggal', 'desc');

        if ($tipe === 'harian') {

            $q->whereDate('tanggal', $tanggalParam);

        } elseif ($tipe === 'mingguan' && $request->filled('minggu')) {

            $start = Carbon::parse($request->minggu)->startOfWeek(Carbon::MONDAY);
            $end   = $start->copy()->addDays(4);
            $q->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);

        } elseif ($tipe === 'bulanan' && $request->filled('bulan')) {

            $c = Carbon::parse($request->bulan);
            $q->whereMonth('tanggal', $c->month)->whereYear('tanggal', $c->year);
        }

        $data = $q->get();

        // =========================
        // 3b) Konversi koordinat → alamat untuk tampilan
        // =========================
        foreach ($data as $row) {

            $lokasi = trim((string) $row->lokasi);

            // Deteksi apakah lokasi kosong atau formatnya "lat,long"
            $isCoordFormat = false;
            if ($lokasi !== '') {
                $isCoordFormat = (bool) preg_match(
                    '/^-?\d+(\.\d+)?\s*,\s*-?\d+(\.\d+)?$/',
                    $lokasi
                );
            }

            if ( ($lokasi === '' || $isCoordFormat) && $row->lat_masuk && $row->long_masuk ) {

                // Coba reverse geocode
                $alamat = $this->getAddressFromCoordinates(
                    (float) $row->lat_masuk,
                    (float) $row->long_masuk
                );

                if ($alamat) {
                    $row->lokasi = $alamat;
                    // Kalau mau sekalian simpan ke DB, bisa buka komentar ini:
                    // PresensiModel::where('id_presensi', $row->id_presensi ?? $row->id)->update(['lokasi' => $alamat]);
                } else {
                    // Kalau masih gagal, jangan pakai koordinat mentah
                    if ($lokasi === '' || $isCoordFormat) {
                        $row->lokasi = 'Lokasi tidak diketahui';
                    }
                }
            }
        }

        // =========================
        // 4) Export
        // =========================
        if ($request->has('export')) {

            if ($request->export === 'excel') {
                return Excel::download(new PresensiExport($tipe, $tanggalParam), 'Presensi_Export.xlsx');
            }

            if ($request->export === 'pdf') {
                $pdf = Pdf::loadView('presensi.export-pdf', compact('data'))
                    ->setPaper('a4', 'landscape');
                return $pdf->download('Presensi_Export.pdf');
            }
        }

        // =========================
        // 5) View
        // =========================
        $breadcrumb = (object)[
            'title' => 'Data Presensi',
            'list'  => ['Dashboard', 'Kepegawaian', 'Data Presensi'],
        ];

        return view('presensi.admin', compact('data', 'breadcrumb'))
            ->with('activeMenu', 'presensi-admin');
    }


    /**
     * BACKFILL REALTIME TANPA CUTOFF
     * - Selalu buat record "tidak hadir" untuk pegawai yang belum presensi
     * - Termasuk tanggal hari ini
     */
    protected function backfillTidakHadirRealtime(array $dates): void
    {
        if (empty($dates)) return;

        $userIds = MUser::pluck('id_user')->all();
        if (empty($userIds)) return;

        foreach (array_chunk($dates, 31) as $datesChunk) {
            foreach (array_chunk($userIds, 500) as $usersChunk) {

                $existingPairs = PresensiModel::whereIn('tanggal', $datesChunk)
                    ->whereIn('id_user', $usersChunk)
                    ->get(['id_user', 'tanggal'])
                    ->map(fn ($r) => $r->id_user . '|' . Carbon::parse($r->tanggal)->toDateString())
                    ->all();

                $existingSet = array_flip($existingPairs);

                $rows = [];
                $nowCreated = now('Asia/Jakarta');

                foreach ($datesChunk as $d) {
                    foreach ($usersChunk as $uid) {
                        $key = $uid . '|' . $d;

                        if (!isset($existingSet[$key])) {
                            $rows[] = [
                                'id_user'     => $uid,
                                'tanggal'     => $d,
                                'status'      => 'tidak hadir',
                                'jam_masuk'   => null,
                                'jam_pulang'  => null,
                                'foto_masuk'  => null,
                                'foto_pulang' => null,
                                'lat_masuk'   => null,
                                'long_masuk'  => null,
                                'lat_pulang'  => null,
                                'long_pulang' => null,
                                'created_at'  => $nowCreated,
                            ];
                        }
                    }
                }

                if (!empty($rows)) {
                    DB::table('presensi')->insert($rows);
                }
            }
        }
    }


    // =========================
    // Helpers
    // =========================

    /** Hari kerja Senin–Jumat */
    protected function isWorkday(Carbon $date): bool
    {
        return in_array($date->dayOfWeekIso, [1, 2, 3, 4, 5], true);
    }

    /** Daftar tanggal kerja dalam rentang [start..end] */
    protected function workdaysBetween(Carbon $start, Carbon $end): array
    {
        $dates = [];
        foreach (CarbonPeriod::create($start, $end) as $d) {
            if ($this->isWorkday($d)) $dates[] = $d->toDateString();
        }
        return $dates;
    }

    /** Reverse geocoding dari lat/lon ke alamat */
    private function getAddressFromCoordinates(float $lat, float $lng): ?string
    {
        try {
            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=18&addressdetails=1";
            $opts = [
                "http" => [
                    "header" => "User-Agent: BNN-Presensi-App/1.0\r\n"
                ]
            ];
            $context  = stream_context_create($opts);
            $response = @file_get_contents($url, false, $context);
            if (!$response) return null;

            $json = json_decode($response, true);
            return $json['display_name'] ?? null;

        } catch (\Exception $e) {
            return null;
        }
    }
}
