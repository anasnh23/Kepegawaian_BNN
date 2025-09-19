<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // optional, kalau mau cek file
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
        $tanggalParam = null;                 // utk export
        $dates        = [];                   // array Y-m-d

        if ($tipe === 'harian' && $request->filled('tanggal')) {
            $d = Carbon::parse($request->tanggal);
            $tanggalParam = $d->toDateString();
            if ($this->isWorkday($d)) {
                $dates[] = $d->toDateString();
            }

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
            // Default: hari ini (jika hari kerja)
            $today = Carbon::today();
            $tipe  = 'harian';
            $tanggalParam = $today->toDateString();
            if ($this->isWorkday($today)) {
                $dates[] = $today->toDateString();
            }
        }

        // ==========================================================
        // 2) Backfill "Tidak hadir" utk semua pegawai (anti-duplikasi)
        // ==========================================================
        $this->backfillTidakHadir($dates);

        // =========================
        // 3) Query data presensi
        // =========================
        $q = PresensiModel::with('user')->latest();

        if ($tipe === 'harian' && $tanggalParam) {
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
        // 3b) Konversi koordinat ke alamat (opsional)
        // =========================
        foreach ($data as $row) {
            if (!$row->lokasi && $row->lat_masuk && $row->long_masuk) {
                $row->lokasi = $this->getAddressFromCoordinates($row->lat_masuk, $row->long_masuk)
                               ?? ($row->lat_masuk.', '.$row->long_masuk);
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
     * Backfill presensi "Tidak hadir"
     */
    protected function backfillTidakHadir(array $dates): void
    {
        if (empty($dates)) return;

        $tz        = 'Asia/Jakarta';
        $now       = \Carbon\Carbon::now($tz);
        $today     = $now->toDateString();
        $cutoffStr = env('PRESENSI_CUTOFF', '17:00:00');
        $cutoff    = \Carbon\Carbon::parse($today . ' ' . $cutoffStr, $tz);

        $userIds = \App\Models\MUser::pluck('id_user')->all();
        if (empty($userIds)) return;

        foreach (array_chunk($dates, 31) as $datesChunk) {
            foreach (array_chunk($userIds, 500) as $usersChunk) {
                $effectiveDates = array_values(array_filter($datesChunk, function ($d) use ($today, $now, $cutoff) {
                    if ($d === $today && $now->lt($cutoff)) {
                        return false;
                    }
                    return true;
                }));
                if (empty($effectiveDates)) continue;

                $existingPairs = \App\Models\PresensiModel::whereIn('tanggal', $effectiveDates)
                    ->whereIn('id_user', $usersChunk)
                    ->get(['id_user', 'tanggal'])
                    ->map(fn($r) => $r->id_user . '|' . \Carbon\Carbon::parse($r->tanggal)->toDateString())
                    ->all();

                $existingSet = array_flip($existingPairs);

                $rows = [];
                $nowCreated = now();

                foreach ($effectiveDates as $d) {
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
        return in_array($date->dayOfWeekIso, [1,2,3,4,5], true);
    }

    /** Sudah lewat cut-off hari ini? */
    protected function deadlinePassed(): bool
    {
        $cutoff = env('PRESENSI_CUTOFF', '17:00:00');
        return now()->format('H:i:s') >= $cutoff;
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
