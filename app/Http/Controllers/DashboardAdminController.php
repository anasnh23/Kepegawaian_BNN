<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\PresensiModel;
use App\Models\MUser;
use App\Models\Cuti;
use App\Models\Kgp;
use Carbon\Carbon;

class DashboardAdminController extends Controller
{
    public function index()
{
    $bulanIni = Carbon::now()->format('Y-m');
    
    // Statistik presensi
    $presensiStats = PresensiModel::selectRaw('status, COUNT(*) as jumlah')
        ->where('tanggal', 'like', $bulanIni.'%')
        ->groupBy('status')
        ->pluck('jumlah', 'status');

    // Statistik cuti bulan ini
    $cutiStats = Cuti::selectRaw('status, COUNT(*) as jumlah')
        ->where('tanggal_pengajuan', 'like', $bulanIni.'%')
        ->groupBy('status')
        ->pluck('jumlah', 'status');

    // Total pegawai
    $totalPegawai = MUser::count();

    // Data grafik golongan pangkat
    $golonganPangkat = DB::table('pangkat')
        ->join('ref_golongan_pangkat', 'pangkat.id_ref_pangkat', '=', 'ref_golongan_pangkat.id_ref_pangkat')
        ->select('ref_golongan_pangkat.golongan_pangkat', DB::raw('count(*) as jumlah'))
        ->groupBy('ref_golongan_pangkat.golongan_pangkat')
        ->pluck('jumlah', 'golongan_pangkat');

    // Jumlah kenaikan gaji tahun ini
    $kenaikanGaji = Kgp::whereYear('tmt', now()->year)->count();

    $breadcrumb = (object)[
        'title' => 'Dashboard Admin',
        'list' => ['Dashboard']
    ];

    $activeMenu = 'dashboard';

    return view('dashboard.admin', compact(
        'breadcrumb', 'activeMenu',
        'presensiStats', 'cutiStats',
        'totalPegawai', 'golonganPangkat',
        'kenaikanGaji'
    ));
}


}