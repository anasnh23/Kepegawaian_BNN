<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\MUser;
use App\Models\Cuti;
use App\Models\ApprovalPimpinan;
use App\Models\Notification;

class DashboardPimpinanController extends Controller
{
    public function index()
    {
        // === Kartu ringkasan ===
        $totalPegawai   = MUser::where('id_level', 2)->count();

        $dokumenMasuk   = ApprovalPimpinan::whereNotNull('dokumen_path')->count();
        $dokumenSetujui = ApprovalPimpinan::where('status', 'Disetujui')->count();

        $cutiMenunggu   = Cuti::where('status', 'Menunggu')->count();

        // === Data chart pangkat (golongan) ===
        $golonganPangkat = DB::table('pangkat')
            ->join(
                'ref_golongan_pangkat',
                'pangkat.id_ref_pangkat',
                '=',
                'ref_golongan_pangkat.id_ref_pangkat'
            )
            ->select(
                'ref_golongan_pangkat.golongan_pangkat',
                DB::raw('count(*) as jumlah')
            )
            ->groupBy('ref_golongan_pangkat.golongan_pangkat')
            ->pluck('jumlah', 'golongan_pangkat');

        // === Data chart jenis cuti ===
        $jenisCuti = Cuti::select('jenis_cuti', DB::raw('count(*) as total'))
            ->groupBy('jenis_cuti')
            ->pluck('total', 'jenis_cuti');

        // === Notifikasi terbaru untuk pimpinan login ===
        // Berisi notifikasi cuti, dokumen, maupun KGP (karena kita sudah tambahkan di PengajuanKgpController)
        $notifications = Notification::where('id_user', Auth::id())
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // === Breadcrumb untuk tampilan ===
        $breadcrumb = (object)[
            'title' => 'Dashboard Pimpinan',
            'list'  => ['Dashboard']
        ];

        $activeMenu = 'dashboard';

        return view('dashboard.pimpinan', compact(
            'breadcrumb',
            'activeMenu',
            'totalPegawai',
            'dokumenMasuk',
            'dokumenSetujui',
            'cutiMenunggu',
            'golonganPangkat',
            'jenisCuti',
            'notifications'
        ));
    }
}
