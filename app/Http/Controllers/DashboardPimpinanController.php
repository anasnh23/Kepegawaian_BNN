<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Cuti;
use App\Models\MUser;
use App\Models\ApprovalPimpinan;
use Carbon\Carbon;

class DashboardPimpinanController extends Controller
{
public function index()
{
    $totalPegawai = MUser::where('id_level', 2)->count();

    // Jumlah dokumen yang sudah diunggah
    $dokumenMasuk = ApprovalPimpinan::whereNotNull('dokumen_path')->count();

    // Jumlah dokumen yang sudah disetujui oleh pimpinan
    $dokumenSetujui = ApprovalPimpinan::where('status', 'Disetujui')->count();

    $golonganPangkat = DB::table('pangkat')
        ->join('ref_golongan_pangkat', 'pangkat.id_ref_pangkat', '=', 'ref_golongan_pangkat.id_ref_pangkat')
        ->select('ref_golongan_pangkat.golongan_pangkat', DB::raw('count(*) as jumlah'))
        ->groupBy('ref_golongan_pangkat.golongan_pangkat')
        ->pluck('jumlah', 'golongan_pangkat');

    $breadcrumb = (object)[
        'title' => 'Dashboard Pimpinan',
        'list' => ['Dashboard']
    ];

    $activeMenu = 'dashboard';

    return view('dashboard.pimpinan', compact(
        'breadcrumb',
        'activeMenu',
        'totalPegawai',
        'dokumenMasuk',
        'dokumenSetujui',
        'golonganPangkat'
    ));
}
}
