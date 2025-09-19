<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\MUser;
use App\Helpers\NotifikasiHelper;
use App\Helpers\MasaKerja;

class PengajuanKgpController extends Controller
{
    /** ================= Halaman Pengajuan KGP (Pegawai) ================= */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $userId = (int)($user->id_user ?? $user->id);
        $today  = Carbon::today();

        // ✅ Ambil TMT awal via helper
        $tmt = $this->getTmtAwal($userId);

        // ✅ Hitung masa kerja konsisten
        [$mkYears, $mkMonths] = MasaKerja::yearsMonths($userId);
        $masaKerjaTahun  = (int) $mkYears;
        $masaKerjaLabel  = sprintf('%d tahun %d bulan', $mkYears, $mkMonths);

        // ✅ Tiap 4 tahun = 1 tahap
        $tahapSeharusnya = intdiv($masaKerjaTahun, 4);

        // ✅ Pengajuan disetujui & pending
        $pengajuanApproved = DB::table('kgp')
            ->where('id_user', $userId)
            ->where('status', 'Disetujui')
            ->count();

        $adaPending = DB::table('kgp')
            ->where('id_user', $userId)
            ->where('status', 'Menunggu')
            ->exists();

        // ✅ Hanya boleh ajukan kalau sudah layak & tidak ada pending
        $bolehAjukan  = ($tahapSeharusnya > $pengajuanApproved) && !$adaPending;
        $tahapBerikut = $pengajuanApproved + 1;
        $estimasiTanggalLayak = (clone $tmt)->addYears(4 * $tahapBerikut);

        // ✅ Riwayat
        $riwayat = DB::table('kgp')
            ->where('id_user', $userId)
            ->orderByDesc('created_at')
            ->get();

        $totalPengajuan = DB::table('kgp')->where('id_user', $userId)->count();

        $breadcrumb = (object)[
            'title' => 'Pengajuan KGP',
            'list'  => ['Dashboard', 'Kepegawaian', 'Pengajuan KGP'],
        ];

        return view('kgp.pengajuan', compact(
            'breadcrumb',
            'tmt',
            'today',
            'masaKerjaTahun',
            'masaKerjaLabel',
            'tahapSeharusnya',
            'totalPengajuan',
            'pengajuanApproved',
            'adaPending',
            'bolehAjukan',
            'tahapBerikut',
            'estimasiTanggalLayak',
            'riwayat'
        ))->with('activeMenu', 'pengajuan-kgp');
    }

    /** ================= Simpan Pengajuan KGP (Pegawai) ================= */
    public function store(Request $request)
    {
        $user   = Auth::user();
        $userId = (int)($user->id_user ?? $user->id);

        $tmt = $this->getTmtAwal($userId);

        [$mkYears] = MasaKerja::yearsMonths($userId);
        $masaKerjaTahun  = (int) $mkYears;
        $tahapSeharusnya = intdiv($masaKerjaTahun, 4);

        $approvedCount = DB::table('kgp')
            ->where('id_user', $userId)
            ->where('status', 'Disetujui')
            ->count();

        $hasPending = DB::table('kgp')
            ->where('id_user', $userId)
            ->where('status', 'Menunggu')
            ->exists();

        if ($hasPending) {
            return redirect()->route('kgp.pengajuan')
                ->with('error', 'Pengajuan sebelumnya masih menunggu persetujuan. Harap tunggu keputusan.');
        }

        // 🚫 Tolak kalau belum layak (masa kerja < 4 thn atau tahap <= approved)
        if ($tahapSeharusnya <= $approvedCount) {
            $tahapBerikut = $approvedCount + 1;
            $eligibleAt   = (clone $tmt)->addYears(4 * $tahapBerikut);
            return redirect()->route('kgp.pengajuan')
                ->with('error', 'Anda belum memenuhi syarat pengajuan KGP tahap berikutnya. Estimasi layak: ' . $eligibleAt->translatedFormat('d F Y') . '.');
        }

        // ✅ Simpan pengajuan
        DB::table('kgp')->insert([
            'id_user'        => $userId,
            'tahun_kgp'      => (int) now()->format('Y'),
            'status'         => 'Menunggu',
            'disetujui_oleh' => null,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // ✅ Kirim notifikasi ke semua pimpinan
        $pimpinanList = MUser::where('id_level', 3)->get();
        foreach ($pimpinanList as $pimpinan) {
            NotifikasiHelper::send(
                $pimpinan->id_user,
                'kgp',
                'Ada pengajuan KGP baru dari ' . ($user->nama ?? 'Pegawai'),
                route('approval.kgp')
            );
        }

        return redirect()->route('kgp.pengajuan')
            ->with('success', 'Pengajuan KGP berhasil dikirim dan menunggu persetujuan.');
    }

    /** ================= Riwayat KGP ================= */
    public function riwayat()
    {
        $userId = (int)(Auth::user()->id_user ?? Auth::id());

        $riwayat = DB::table('kgp')
            ->where('id_user', $userId)
            ->orderByDesc('created_at')
            ->get();

        $breadcrumb = (object)[
            'title' => 'Riwayat KGP',
            'list'  => ['Dashboard', 'Kepegawaian', 'Riwayat KGP'],
        ];

        return view('kgp.riwayat', compact('riwayat', 'breadcrumb'))
            ->with('activeMenu', 'pengajuan-kgp');
    }

    // ===================== Helpers =====================
    private function getTmtAwal(int $userId): Carbon
    {
        $start = MasaKerja::tanggalMulai($userId);
        return $start ? $start->copy()->startOfDay() : Carbon::today();
    }
}
