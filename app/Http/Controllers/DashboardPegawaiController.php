<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\PresensiModel;
use App\Models\Cuti;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardPegawaiController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        Carbon::setLocale('id');
        $now     = Carbon::now();
        $periode = request('periode', 'bulan_ini'); // bulan_ini | 30_hari | semua

        /* =========================
         * Tentukan rentang tanggal
         * ========================= */
        $start = null;
        $end   = null;
        $labelPeriode = '';

        switch ($periode) {
            case '30_hari':
                $start = $now->copy()->subDays(29)->startOfDay();
                $end   = $now->copy()->endOfDay();
                $labelPeriode = '30 Hari Terakhir';
                break;

            case 'semua':
                $firstPresensi = PresensiModel::where('id_user', $user->id_user)
                    ->orderBy('tanggal', 'asc')
                    ->value('tanggal');
                $start = $firstPresensi ? Carbon::parse($firstPresensi)->startOfDay() : $now->copy()->startOfYear();
                $end   = $now->copy()->endOfDay();
                $labelPeriode = 'Semua Periode';
                break;

            case 'bulan_ini':
            default:
                $start = $now->copy()->startOfMonth();
                $end   = $now->copy()->endOfMonth();
                $labelPeriode = $now->translatedFormat('F Y');
                $periode = 'bulan_ini';
                break;
        }

        // Helper filter tanggal untuk kolom 'tanggal'
        $applyTanggalFilter = function ($query) use ($start, $end) {
            if ($start && $end) {
                $query->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);
            }
            return $query;
        };

        /* =========================
         * 1) Statistik Presensi
         * ========================= */
        $presensiQuery = PresensiModel::selectRaw('status, COUNT(*) as jumlah')
            ->where('id_user', $user->id_user);

        $presensiQuery = $applyTanggalFilter($presensiQuery);

        $rawPresensi = $presensiQuery
            ->groupBy('status')
            ->pluck('jumlah', 'status')
            ->toArray();

        $presensiStats = [
            'hadir'        => 0,
            'terlambat'    => 0,
            'tidak hadir'  => 0,
            'dinas_luar'   => 0,
        ];

        foreach ($rawPresensi as $status => $count) {
            $key = strtolower(trim((string)$status));
            if ($key === 'hadir') {
                $presensiStats['hadir'] += (int)$count;
            } elseif ($key === 'terlambat' || $key === 'telat') {
                $presensiStats['terlambat'] += (int)$count;
            } elseif (in_array($key, ['tidak hadir','tidak_hadir','alpha','absen'])) {
                $presensiStats['tidak hadir'] += (int)$count;
            } elseif ($key === 'dinas_luar') {
                $presensiStats['dinas_luar'] += (int)$count;
            }
        }

        /* =========================
         * 2) Statistik Cuti (periode)
         * ========================= */
        $cutiQuery = Cuti::where('id_user', $user->id_user)
            ->when(in_array($periode, ['bulan_ini', '30_hari', 'semua']), function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_pengajuan', [$start->toDateString(), $end->toDateString()]);
            });

        $cutiStats = (int)$cutiQuery->count();

        // Ringkasan cuti tahun berjalan (total hari terpakai)
        $totalCutiTahunIni = (int) Cuti::where('id_user', $user->id_user)
            ->whereYear('tanggal_mulai', $now->year)
            ->where('status', 'Disetujui')
            ->sum(DB::raw('COALESCE(lama_cuti, 0)'));

        /* =========================
         * 3) Riwayat Presensi Singkat
         * ========================= */
        $riwayatQuery = PresensiModel::where('id_user', $user->id_user);
        $riwayatQuery = $applyTanggalFilter($riwayatQuery);

        $riwayat = $riwayatQuery
            ->orderByDesc('tanggal')
            ->orderByDesc('jam_masuk')
            ->limit(10)
            ->get();

        /* =========================
         * 4) Masa Kerja
         * ========================= */
        $masaKerjaTahun = 0;
        $masaKerjaBulan = 0;

        $riwayatAwal = DB::table('riwayat_jabatan')
            ->where('id_user', $user->id_user)
            ->orderBy('tmt_mulai', 'asc')
            ->first();

        $tmt = $riwayatAwal ? Carbon::parse($riwayatAwal->tmt_mulai) : null;

        if (!$tmt) {
            $kgpAwal = DB::table('kgp')
                ->where('id_user', $user->id_user)
                ->orderBy('tmt', 'asc')
                ->first();
            $tmt = $kgpAwal ? Carbon::parse($kgpAwal->tmt) : null;
        }

        if ($tmt) {
            $diff = $tmt->diff(Carbon::now());
            $masaKerjaTahun = $diff->y;
            $masaKerjaBulan = $diff->m;
        }

        /* =========================
         * 5) Tambahan Metrik
         * ========================= */

        // 5.a Cuti terakhir
        $cutiTerakhir = Cuti::where('id_user', $user->id_user)
            ->orderByDesc('tanggal_pengajuan')
            ->select('jenis_cuti', 'tanggal_mulai', 'tanggal_selesai', 'status', 'tanggal_pengajuan')
            ->first();

        // 5.b Rekap mingguan (periode)
        $rekapMingguan = PresensiModel::selectRaw("
                YEARWEEK(tanggal, 3) as tahun_minggu,
                SUM(CASE WHEN LOWER(status)='hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN LOWER(status) IN ('terlambat','telat') THEN 1 ELSE 0 END) as terlambat,
                SUM(CASE WHEN LOWER(status) IN ('tidak hadir','tidak_hadir','alpha','absen') THEN 1 ELSE 0 END) as tidak_hadir,
                SUM(CASE WHEN LOWER(status)='dinas_luar' THEN 1 ELSE 0 END) as dinas_luar
            ")
            ->where('id_user', $user->id_user)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->groupBy('tahun_minggu')
            ->orderBy('tahun_minggu', 'asc')
            ->get();

        // 5.c Notifikasi terbaru (opsional)
        $notifikasiTerbaru = DB::table('notifications')
            ->where('id_user', $user->id_user)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // 5.d Pengumuman HR/Admin dari dashboard_info (khusus target pegawai/semua)
        $pengumumanHr = DashboardInfoController::getForDashboard('pegawai', 5);

        /* =========================
         * 6) Profil Singkat (Nama & NIP)
         * ========================= */
        $userRow = DB::table('m_user')->where('id_user', $user->id_user)->select('nama','nip')->first();
        $profilSingkat = (object)[
            'nama' => $userRow->nama ?? '-',
            'nip'  => $userRow->nip  ?? '-',
        ];

        /* =========================
         * 7) View
         * ========================= */
        $breadcrumb = (object)[
            'title' => 'Dashboard Pegawai',
            'list'  => ['Dashboard']
        ];
        $activeMenu = 'dashboard';

        return view('dashboard.pegawai', compact(
            'breadcrumb',
            'activeMenu',
            'presensiStats',
            'cutiStats',
            'labelPeriode',
            'riwayat',
            'periode',
            'masaKerjaTahun',
            'masaKerjaBulan',
            'cutiTerakhir',
            'rekapMingguan',
            'notifikasiTerbaru',
            'pengumumanHr',
            'profilSingkat',
            'totalCutiTahunIni'
        ));
    }
}
