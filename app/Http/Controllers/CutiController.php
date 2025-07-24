<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cuti;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class CutiController extends Controller
{
public function index()
{
    $user = Auth::user();

    $cuti = Cuti::where('id_user', $user->id_user)
                ->orderBy('tanggal_pengajuan', 'desc')
                ->get();

    $hakCuti = $this->hitungHakCutiTahunan($user);

    // ✅ Breadcrumb hanya Dashboard → Pengajuan Cuti
    $breadcrumb = (object)[
        'title' => 'Pengajuan Cuti',
        'list' => ['Dashboard', 'Pengajuan Cuti']
    ];

    return view('pegawai.cuti', compact('cuti', 'hakCuti', 'breadcrumb'))
        ->with('activeMenu', 'cuti');
}



public function store(Request $request)
{
    $request->validate([
        'jenis_cuti' => 'required',
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
    ]);

    $user = Auth::user();
    $lama_kerja = Carbon::parse($user->tanggal_masuk)->diffInYears(now());

    if ($request->jenis_cuti == 'Tahunan' && $lama_kerja < 1) {
        return response()->json(['message' => 'Anda belum memenuhi syarat pengajuan cuti tahunan (minimal 1 tahun kerja).'], 422);
    }

    $lama_cuti = Carbon::parse($request->tanggal_mulai)->diffInDays(Carbon::parse($request->tanggal_selesai)) + 1;

    if ($lama_cuti < 1) {
        return response()->json(['message' => 'Minimal cuti adalah 1 hari kerja.'], 422);
    }

    $hakCuti = $this->hitungHakCutiTahunan($user);

    $cuti_terpakai = Cuti::where('id_user', $user->id_user)
        ->where('jenis_cuti', 'Tahunan')
        ->where('status', 'Disetujui')
        ->whereYear('tanggal_mulai', now()->year)
        ->sum(DB::raw('DATEDIFF(tanggal_selesai, tanggal_mulai) + 1'));

    if ($request->jenis_cuti == 'Tahunan' && ($cuti_terpakai + $lama_cuti > $hakCuti)) {
        return response()->json([
            'message' => "Pengajuan melebihi kuota cuti. Hak tersedia: {$hakCuti} hari, sudah digunakan: {$cuti_terpakai} hari."
        ], 422);
    }

    Cuti::create([
        'id_user' => $user->id_user,
        'jenis_cuti' => $request->jenis_cuti,
        'tanggal_pengajuan' => now(),
        'tanggal_mulai' => $request->tanggal_mulai,
        'tanggal_selesai' => $request->tanggal_selesai,
        'lama_cuti' => $lama_cuti,
        'keterangan' => $request->keterangan,
        'status' => 'Menunggu'
    ]);

    return response()->json(['message' => 'Pengajuan cuti berhasil dikirim.']);
}


    private function hitungHakCutiTahunan($user)
    {
        $tahun_ini = now()->year;
        $tahun_lalu = now()->subYear()->year;
        $dua_tahun_lalu = now()->subYears(2)->year;

        // Ambil jumlah cuti yang sudah digunakan
        $cuti_tahun_lalu = Cuti::where('id_user', $user->id_user)
            ->where('jenis_cuti', 'Tahunan')
            ->whereYear('tanggal_mulai', $tahun_lalu)
            ->sum(DB::raw('DATEDIFF(tanggal_selesai, tanggal_mulai) + 1'));

        $cuti_dua_tahun_lalu = Cuti::where('id_user', $user->id_user)
            ->where('jenis_cuti', 'Tahunan')
            ->whereYear('tanggal_mulai', $dua_tahun_lalu)
            ->sum(DB::raw('DATEDIFF(tanggal_selesai, tanggal_mulai) + 1'));

        // Akumulasi hak cuti sesuai aturan
        if ($cuti_tahun_lalu < 6 && $cuti_dua_tahun_lalu == 0) {
            return 24;
        } elseif ($cuti_tahun_lalu < 6) {
            return 18;
        } else {
            return 12;
        }
    }

    public function riwayat()
{
    $user = Auth::user();

    $cuti = Cuti::with('pegawai')
                ->where('id_user', $user->id_user)
                ->orderBy('tanggal_pengajuan', 'desc')
                ->get();

    $breadcrumb = (object)[
        'title' => 'Riwayat Cuti',
        'list' => ['Dashboard', 'Riwayat Cuti']
    ];

    return view('pegawai.riwayat-cuti', compact('cuti', 'breadcrumb'))
        ->with('activeMenu', 'riwayat-cuti');
}

    

public function cetak($id)
{
    $cuti = Cuti::with('pegawai')->findOrFail($id);

    if ($cuti->status !== 'Disetujui') {
        abort(403, 'Cuti belum disetujui, tidak dapat dicetak.');
    }

    $pdf = Pdf::loadView('pegawai.cuti_pdf', compact('cuti'))
              ->setPaper('A4', 'portrait');

    return $pdf->stream('pengajuan_cuti_' . $cuti->id_cuti . '.pdf');
}




}
