<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\PresensiModel;
use App\Models\Cuti;
use Carbon\Carbon;

class DashboardPegawaiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $bulanIni = Carbon::now()->format('Y-m');

        // Statistik presensi pegawai bulan ini
        $presensiStats = PresensiModel::selectRaw('status, COUNT(*) as jumlah')
            ->where('id_user', $user->id_user)
            ->where('tanggal', 'like', $bulanIni . '%')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        // Statistik cuti pegawai bulan ini
        $cutiStats = Cuti::where('id_user', $user->id_user)
            ->where('tanggal_pengajuan', 'like', $bulanIni . '%')
            ->count();

        $breadcrumb = (object)[
            'title' => 'Dashboard Pegawai',
            'list' => ['Dashboard']
        ];

        $activeMenu = 'dashboard';

        return view('dashboard.pegawai', compact(
            'breadcrumb',
            'activeMenu',
            'presensiStats',
            'cutiStats'
        ));
    }
}
