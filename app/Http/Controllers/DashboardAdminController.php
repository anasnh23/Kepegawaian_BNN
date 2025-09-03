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
        // ====== Periode: ?range=month|30d|all ======
        $range = request('range', 'month');
        if ($range === '30d') {
            $start = Carbon::now()->subDays(30)->startOfDay()->toDateString();
            $end   = Carbon::now()->endOfDay()->toDateString();
            $labelPeriode = '30 Hari Terakhir';
        } elseif ($range === 'all') {
            $start = null; $end = null;
            $labelPeriode = 'Semua Waktu';
        } else { // month (default)
            $start = Carbon::now()->startOfMonth()->toDateString();
            $end   = Carbon::now()->endOfMonth()->toDateString();
            $labelPeriode = 'Bulan Ini';
        }

        // ====== PRESENSI ======
        $qPres = PresensiModel::select('status', DB::raw('COUNT(*) AS jumlah'));
        if ($start && $end) {
            $qPres->whereBetween('tanggal', [$start, $end]);
        }
        $presRaw = $qPres->groupBy('status')->pluck('jumlah','status')->toArray();

        // Normalisasi key (abaikan kapital & underscore)
        $norm = [];
        foreach ($presRaw as $k => $v) {
            $key = strtolower(trim($k));
            $key = str_replace('_', ' ', $key);
            $norm[$key] = (int)$v;
        }
        $presensiStats = [
            'hadir'       => $norm['hadir']        ?? 0,
            'terlambat'   => $norm['terlambat']    ?? 0,
            'tidak hadir' => $norm['tidak hadir']  ?? 0,
            'dinas_luar'  => $norm['dinas luar']   ?? 0,   // ✅ tambahan
        ];

        // ====== CUTI (pakai tanggal_pengajuan, ganti ke tanggal_mulai bila perlu) ======
        $qCuti = Cuti::select('status', DB::raw('COUNT(*) AS jumlah'));
        if ($start && $end) {
            $qCuti->whereBetween('tanggal_pengajuan', [$start, $end]);
        }
        $cutiStats = $qCuti->groupBy('status')->pluck('jumlah', 'status');

        // ====== TOTAL PEGAWAI & GENDER ======
        $totalPegawai = (int) MUser::count();

        $genderRaw = MUser::select('jenis_kelamin', DB::raw('COUNT(*) AS jumlah'))
            ->whereIn('jenis_kelamin', ['L','P'])
            ->groupBy('jenis_kelamin')
            ->pluck('jumlah','jenis_kelamin')
            ->toArray();

        $jumlahLaki      = (int)($genderRaw['L'] ?? 0);
        $jumlahPerempuan = (int)($genderRaw['P'] ?? 0);

        // ====== DISTRIBUSI GOLONGAN PANGKAT ======
        $gp = DB::table('pangkat AS p')
            ->leftJoin('ref_golongan_pangkat AS rgp', 'rgp.id_ref_pangkat', '=', 'p.id_ref_pangkat')
            ->select(DB::raw("COALESCE(rgp.golongan_pangkat,'-') AS gol"), DB::raw('COUNT(*) AS jumlah'))
            ->groupBy('gol')
            ->orderBy('jumlah','desc')
            ->pluck('jumlah','gol')
            ->toArray();

        $golonganPangkat = $gp;                                
        $pangkatLabels   = array_values(array_keys($gp));       
        $pangkatValues   = array_values(array_map('intval',$gp));

        // ====== KENAIKAN GAJI (KGP) TAHUN INI ======
        $kenaikanGaji = (int) Kgp::whereYear('tmt', date('Y'))->count();

        // ====== UI ======
        $breadcrumb = (object)[ 'title' => 'Dashboard Admin', 'list' => ['Dashboard'] ];
        $activeMenu = 'dashboard';

        return view('dashboard.admin', compact(
            'breadcrumb','activeMenu',
            'presensiStats','cutiStats',
            'totalPegawai','kenaikanGaji',
            'jumlahLaki','jumlahPerempuan',
            'golonganPangkat','pangkatLabels','pangkatValues',
            'range','labelPeriode'
        ));
    }
}
