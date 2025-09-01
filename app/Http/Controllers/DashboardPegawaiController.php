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
        $start = null;   // Carbon|null  (start date inclusive)
        $end   = null;   // Carbon|null  (end date inclusive)
        $labelPeriode = '';

        switch ($periode) {
            case '30_hari':
                $start = $now->copy()->subDays(29)->startOfDay(); // 30 hari terakhir termasuk hari ini
                $end   = $now->copy()->endOfDay();
                $labelPeriode = '30 Hari Terakhir';
                break;

            case 'semua':
                // tidak ada batas tanggal
                $labelPeriode = 'Semua Periode';
                break;

            case 'bulan_ini':
            default:
                $start = $now->copy()->startOfMonth();
                $end   = $now->copy()->endOfMonth();
                $labelPeriode = $now->translatedFormat('F Y'); // contoh: "Agustus 2025"
                $periode = 'bulan_ini';
                break;
        }

        // Helper untuk apply date filter pada query "tanggal" (kolom DATE)
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

        // Normalisasi status -> jumlah
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
        // Catatan: kolom referensi tanggal cuti bisa berbeda-beda.
        // Di sini saya pakai tanggal_pengajuan, menyesuaikan dengan kode sebelumnya.
        $cutiQuery = Cuti::where('id_user', $user->id_user);

        if ($periode === 'bulan_ini') {
            $cutiQuery->whereBetween('tanggal_pengajuan', [
                $start->toDateString(), $end->toDateString()
            ]);
        } elseif ($periode === '30_hari') {
            $cutiQuery->whereBetween('tanggal_pengajuan', [
                $start->toDateString(), $end->toDateString()
            ]);
        } // 'semua' -> tanpa filter tanggal

        $cutiStats = (int) $cutiQuery->count();

        // =========================
        // 3) Sisa Cuti (opsional, tetap tahunan)
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
        // 5) View
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
            'periode' // penting untuk menandai tab aktif di Blade
        ));
    }
}
