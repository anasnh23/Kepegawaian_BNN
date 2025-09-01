<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PresensiModel;
use App\Models\Cuti;
use Carbon\Carbon;

class DashboardPegawaiController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        Carbon::setLocale('id');
        $now      = Carbon::now();
        $periode  = request('periode', 'bulan_ini'); // bulan_ini | 30_hari | semua

        // =========================
        // Tentukan rentang tanggal
        // =========================
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

        // Helper untuk filter tanggal
        $applyTanggalFilter = function ($query) use ($start, $end) {
            if ($start && $end) {
                $query->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);
            }
            return $query;
        };

        // =========================
        // 1) Statistik Presensi
        // =========================
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
        ];

        foreach ($rawPresensi as $status => $count) {
            $key = strtolower(trim((string)$status));
            if ($key === 'hadir') {
                $presensiStats['hadir'] += (int) $count;
            } elseif ($key === 'terlambat' || $key === 'telat') {
                $presensiStats['terlambat'] += (int) $count;
            } elseif (in_array($key, ['tidak hadir','tidak_hadir','alpha','absen'])) {
                $presensiStats['tidak hadir'] += (int) $count;
            }
        }

        // =========================
        // 2) Statistik Cuti
        // =========================
        $cutiQuery = Cuti::where('id_user', $user->id_user);

        if (in_array($periode, ['bulan_ini', '30_hari'])) {
            $cutiQuery->whereBetween('tanggal_pengajuan', [
                $start->toDateString(), $end->toDateString()
            ]);
        }

        $cutiStats = (int) $cutiQuery->count();

        // =========================
        // 3) Sisa Cuti Tahunan
        // =========================
        $jatahTahunan = (int) (config('app.jatah_cuti_tahunan', 12));
        $cutiTerpakaiTahunIni = Cuti::where('id_user', $user->id_user)
            ->whereYear('tanggal_mulai', $now->year)
            ->where('status', 'Disetujui')
            ->sum(DB::raw('COALESCE(lama_cuti, 0)'));
        $sisaCuti = max(0, $jatahTahunan - (int) $cutiTerpakaiTahunIni);

        // =========================
        // 4) Riwayat Presensi Singkat
        // =========================
        $riwayatQuery = PresensiModel::where('id_user', $user->id_user);
        $riwayatQuery = $applyTanggalFilter($riwayatQuery);

        $riwayat = $riwayatQuery
            ->orderByDesc('tanggal')
            ->orderByDesc('jam_masuk')
            ->limit(10)
            ->get();

        // =========================
        // 5) Masa Kerja
        // =========================
        $masaKerjaTahun = 0;
        $masaKerjaBulan = 0;

        // Cari tmt dari riwayat_jabatan
        $riwayatAwal = DB::table('riwayat_jabatan')
            ->where('id_user', $user->id_user)
            ->orderBy('tmt_mulai', 'asc')
            ->first();

        $tmt = $riwayatAwal ? Carbon::parse($riwayatAwal->tmt_mulai) : null;

        // fallback kalau tidak ada
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

        // =========================
        // 6) View
        // =========================
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
            'sisaCuti',
            'riwayat',
            'periode',
            'masaKerjaTahun',
            'masaKerjaBulan'
        ));
    }
}
