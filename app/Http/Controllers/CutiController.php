<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\MUser;
use App\Models\UserModel; // jika guard kamu pakai model ini
use App\Models\Cuti;
use App\Models\ApprovalPimpinan;
use App\Helpers\NotifikasiHelper;
use App\Helpers\MasaKerja;          // ⬅️ pakai helper masa kerja yang sama
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CutiController extends Controller
{
    /** Halaman form & daftar pengajuan milik pegawai */
    public function index()
    {
        $auth = Auth::user();
        $user = $this->asMUser($auth);

        $cuti = Cuti::where('id_user', $user->id_user)
            ->orderByDesc('tanggal_pengajuan')
            ->get();

        $hakCuti = $this->hitungHakCutiTahunan($user);

        $breadcrumb = (object)[
            'title' => 'Pengajuan Cuti',
            'list'  => ['Dashboard', 'Pengajuan Cuti'],
        ];

        return view('pegawai.cuti', compact('cuti', 'hakCuti', 'breadcrumb'))
            ->with('activeMenu', 'cuti');
    }

    /** Simpan pengajuan cuti (semua jenis) dengan validasi persyaratan */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti'      => 'required|in:Tahunan,Sakit,Melahirkan,Penting,Besar,Bersama,Luar Tanggungan Negara',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'nullable|string',
        ]);

        $auth  = Auth::user();
        $user  = $this->asMUser($auth);
        $jenis = $request->jenis_cuti;

        $mulai    = Carbon::parse($request->tanggal_mulai);
        $selesai  = Carbon::parse($request->tanggal_selesai);
        $lamaHari = $mulai->diffInDays($selesai) + 1;
        if ($lamaHari < 1) {
            return response()->json(['message' => 'Minimal cuti adalah 1 hari kerja.'], 422);
        }

        // ✅ Masa kerja konsisten (helper)
        $masaKerjaYears = MasaKerja::years($user->id_user);

        // ===== VALIDASI PER-JENIS =====
        if (in_array($jenis, ['Tahunan', 'Bersama'], true)) {
            if ($masaKerjaYears < 1) {
                return response()->json(['message' => 'Minimal 1 tahun kerja untuk Cuti Tahunan/Bersama.'], 422);
            }

            $hak   = $this->hitungHakCutiTahunan($user); // sudah include Bersama
            $pakai = Cuti::where('id_user', $user->id_user)
                ->whereIn('jenis_cuti', ['Tahunan', 'Bersama'])
                ->where('status', 'Disetujui')
                ->whereYear('tanggal_mulai', now()->year)
                ->sum(DB::raw('DATEDIFF(tanggal_selesai, tanggal_mulai) + 1'));

            if (($pakai + $lamaHari) > $hak) {
                return response()->json([
                    'message' => "Sisa kuota tidak cukup. Hak: $hak hari, terpakai: $pakai hari."
                ], 422);
            }
        }

        if ($jenis === 'Besar') {
            if ($masaKerjaYears < 5)
                return response()->json(['message' => 'Minimal 5 tahun kerja untuk Cuti Besar.'], 422);
            if ($lamaHari > 90)
                return response()->json(['message' => 'Durasi maksimal Cuti Besar 90 hari.'], 422);
        }

        /* ==========================
           ✅ FIX VALIDASI MELAHIRKAN
           ========================== */
        if ($jenis === 'Melahirkan') {
            // Normalisasi berbagai kemungkinan nilai field jenis_kelamin
            $jk = strtolower(trim((string) $user->jenis_kelamin));
            // terima: "p", "perempuan", "female", "wanita"
            if (!in_array($jk, ['p', 'perempuan', 'female', 'wanita'], true)) {
                return response()->json(['message' => 'Cuti Melahirkan hanya untuk pegawai perempuan.'], 422);
            }
            if ($lamaHari > 90) {
                return response()->json(['message' => 'Durasi Cuti Melahirkan maksimal 90 hari.'], 422);
            }
        }

        if ($jenis === 'Penting' && $lamaHari > 30) {
            return response()->json(['message' => 'Durasi maksimal Cuti Karena Alasan Penting 30 hari.'], 422);
        }

        if ($jenis === 'Luar Tanggungan Negara') {
            if ($masaKerjaYears < 5)
                return response()->json(['message' => 'Minimal 5 tahun kerja untuk CTLN.'], 422);
            if ($lamaHari > 1095)
                return response()->json(['message' => 'Durasi maksimal CTLN 3 tahun (1095 hari).'], 422);
        }

        // ===== SIMPAN =====
        $cuti = Cuti::create([
            'id_user'           => $user->id_user,
            'jenis_cuti'        => $jenis,
            'tanggal_pengajuan' => now(),
            'tanggal_mulai'     => $mulai->toDateString(),
            'tanggal_selesai'   => $selesai->toDateString(),
            'lama_cuti'         => $lamaHari,
            'keterangan'        => $request->keterangan,
            'status'            => 'Menunggu',
        ]);

        // ===== NOTIFIKASI (Admin, Pimpinan, Pemohon) =====
        $this->notifyAdminPimpinanDanPemohon($user, $cuti);

        return response()->json([
            'message' => 'Pengajuan cuti berhasil dikirim dan menunggu persetujuan.',
            'id_cuti' => $cuti->id_cuti,
        ], 201);
    }

    /** Upload dokumen pendukung → kirim ke pimpinan untuk approval */
    public function uploadDokumen(Request $request)
    {
        $request->validate([
            'cuti_id' => 'required|exists:cuti,id_cuti',
            'dokumen' => 'required|file|mimes:pdf|max:2048',
        ]);

        $cuti = Cuti::with('pegawai')->findOrFail($request->cuti_id);

        $file = $request->file('dokumen');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('dokumen_cuti', $filename, 'public');

        $approval = ApprovalPimpinan::firstOrNew(['id_cuti' => $cuti->id_cuti]);
        $approval->dokumen_path = $path;
        $approval->status       = null;
        $approval->approved_by  = null;
        $approval->save();

        $linkApproval = $this->safeRoute(['approval.dokumen','dokumen.index'], '/');
        MUser::where('id_level', 3)->pluck('id_user')->each(function ($uid) use ($cuti, $linkApproval) {
            NotifikasiHelper::send(
                $uid,
                'cuti',
                'Dokumen cuti dari ' . ($cuti->pegawai->nama ?? 'pegawai') . ' telah diunggah dan menunggu persetujuan.',
                $linkApproval
            );
        });

        return back()->with('success', 'Dokumen cuti berhasil diupload ke pimpinan.');
    }

    /** Riwayat pengajuan milik pegawai */
    public function riwayat()
    {
        $auth = Auth::user();
        $user = $this->asMUser($auth);

        $cuti = Cuti::where('id_user', $user->id_user)
            ->with(['pegawai', 'approvalPimpinan'])
            ->orderByDesc('tanggal_pengajuan')
            ->get();

        $breadcrumb = (object)[
            'title' => 'Riwayat Cuti',
            'list'  => ['Dashboard', 'Riwayat Cuti'],
        ];

        return view('pegawai.riwayat-cuti', compact('cuti', 'breadcrumb'))
            ->with('activeMenu', 'riwayat-cuti');
    }

    /** Riwayat yang sudah kirim dokumen ke pimpinan */
    public function riwayatPersetujuanPimpinan()
    {
        $auth = Auth::user();
        $user = $this->asMUser($auth);

        $cuti = Cuti::where('id_user', $user->id_user)
            ->whereHas('approvalPimpinan', fn ($q) => $q->whereNotNull('dokumen_path'))
            ->with('approvalPimpinan')
            ->orderByDesc('tanggal_pengajuan')
            ->get();

        $breadcrumb = (object)[
            'title' => 'Riwayat Cuti',
            'list'  => ['Dashboard', 'Riwayat Cuti'],
        ];

        return view('pegawai.riwayat-cuti', compact('cuti', 'breadcrumb'))
            ->with('activeMenu', 'riwayat-cuti');
    }

    /** Cetak pengajuan yang sudah disetujui */
    public function cetak($id)
    {
        $cuti = Cuti::with('pegawai')->findOrFail($id);
        if ($cuti->status !== 'Disetujui') {
            abort(403, 'Cuti belum disetujui, tidak dapat dicetak.');
        }

        $pdf = Pdf::loadView('pegawai.cuti_pdf', compact('cuti'))->setPaper('A4', 'portrait');
        return $pdf->stream('pengajuan_cuti_' . $cuti->id_cuti . '.pdf');
    }

    /* ========================= Helpers ========================= */

    /**
     * Normalisasi objek user login menjadi MUser.
     * - Jika sudah MUser → kembalikan apa adanya.
     * - Jika UserModel (atau lain) → cari pasangannya di tabel m_user.
     */
    private function asMUser($authUser): MUser
    {
        if ($authUser instanceof MUser) return $authUser;

        if ($authUser && isset($authUser->id_user)) {
            if ($m = MUser::find($authUser->id_user)) return $m;
        }
        if (isset($authUser->username) && $authUser->username) {
            if ($m = MUser::where('username', $authUser->username)->first()) return $m;
        }
        if (isset($authUser->email) && $authUser->email) {
            if ($m = MUser::where('email', $authUser->email)->first()) return $m;
        }

        throw new \RuntimeException('Tidak bisa memetakan user login ke MUser. Pastikan data sinkron.');
    }

    /** Total cuti tahunan (termasuk cuti bersama) yang disetujui di tahun berjalan */
    private function totalCutiTahunanTerpakaiTahunIni(int $userId): int
    {
        return (int) Cuti::where('id_user', $userId)
            ->whereIn('jenis_cuti', ['Tahunan', 'Bersama'])
            ->where('status', 'Disetujui')
            ->whereYear('tanggal_mulai', now()->year)
            ->sum(DB::raw('DATEDIFF(tanggal_selesai, tanggal_mulai) + 1'));
    }

    /**
     * Hitung hak cuti tahunan:
     * - Default 12.
     * - Jika tahun lalu pemakaian < 6 hari dan 2 tahun lalu 0 hari → 24.
     * - Jika tahun lalu < 6 hari → 18.
     * (Cuti Bersama ikut mengurangi kuota)
     */
    private function hitungHakCutiTahunan(MUser $user): int
    {
        $tahunLalu    = now()->subYear()->year;
        $duaTahunLalu = now()->subYears(2)->year;

        $pakaiThnLalu = (int) Cuti::where('id_user', $user->id_user)
            ->whereIn('jenis_cuti', ['Tahunan', 'Bersama'])
            ->whereYear('tanggal_mulai', $tahunLalu)
            ->where('status', 'Disetujui')
            ->sum(DB::raw('DATEDIFF(tanggal_selesai, tanggal_mulai) + 1'));

        $pakai2ThnLalu = (int) Cuti::where('id_user', $user->id_user)
            ->whereIn('jenis_cuti', ['Tahunan', 'Bersama'])
            ->whereYear('tanggal_mulai', $duaTahunLalu)
            ->where('status', 'Disetujui')
            ->sum(DB::raw('DATEDIFF(tanggal_selesai, tanggal_mulai) + 1'));

        if ($pakaiThnLalu < 6 && $pakai2ThnLalu == 0) return 24;
        if ($pakaiThnLalu < 6) return 18;
        return 12;
    }

    /** Kirim notifikasi ke Admin, Pimpinan, dan Pemohon */
    private function notifyAdminPimpinanDanPemohon(MUser $pemohon, Cuti $cuti): void
    {
        $linkAdmin    = $this->safeRoute(['cuti.admin','cutiadmin.index'], '/');
        $linkApproval = $this->safeRoute(['approval.dokumen','dokumen.index'], '/');
        $linkRiwayat  = $this->safeRoute(['cuti.riwayat'], '/');

        // === Kirim ke ADMIN (id_level = 1) ===
        $adminIds = MUser::where('id_level', 1)->pluck('id_user')->all();
        foreach ($adminIds as $uid) {
            NotifikasiHelper::send(
                $uid,
                'cuti',
                'Pengajuan cuti baru dari '.$pemohon->nama,
                $linkAdmin
            );
        }

        // === Kirim ke PIMPINAN (id_level = 3) ===
        $pimpinanIds = MUser::where('id_level', 3)->pluck('id_user')->all();
        foreach ($pimpinanIds as $uid) {
            NotifikasiHelper::send(
                $uid,
                'cuti',
                'Butuh persetujuan: cuti dari '.$pemohon->nama,
                $linkApproval
            );
        }

        // === Konfirmasi ke PEMOHON ===
        NotifikasiHelper::send(
            $pemohon->id_user,
            'cuti',
            'Pengajuan cuti kamu berhasil dibuat.',
            $linkRiwayat
        );
    }

    /** Ambil URL route yang ada; bila tidak ada, fallback ke path default */
    private function safeRoute(array $names, string $default = '/'): string
    {
        foreach ($names as $n) {
            if (Route::has($n)) return route($n);
        }
        return url($default);
    }
}
