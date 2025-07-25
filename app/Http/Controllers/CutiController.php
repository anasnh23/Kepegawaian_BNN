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
  'jenis_cuti' => 'required|in:Tahunan,Sakit,Melahirkan,Penting,Besar,Bersama,Luar Tanggungan Negara',
  'tanggal_mulai' => 'required|date',
  'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
]);


    $user = Auth::user();
    $lama_kerja = Carbon::parse($user->tanggal_masuk)->diffInYears(now());
    $jenis = $request->jenis_cuti;
    $mulai = Carbon::parse($request->tanggal_mulai);
    $selesai = Carbon::parse($request->tanggal_selesai);
    $lama_cuti = $mulai->diffInDays($selesai) + 1;

    if ($lama_cuti < 1) {
        return response()->json(['message' => 'Minimal cuti adalah 1 hari kerja.'], 422);
    }

    // Validasi khusus
    if ($jenis == 'Cuti Tahunan' && $lama_kerja < 1) {
        return response()->json(['message' => 'Minimal 1 tahun kerja untuk mengajukan Cuti Tahunan.'], 422);
    }

    if ($jenis == 'Cuti Besar' && $lama_kerja < 5) {
        return response()->json(['message' => 'Minimal 5 tahun kerja untuk mengajukan Cuti Besar.'], 422);
    }

    if ($jenis == 'Cuti Besar' && $lama_cuti > 90) {
        return response()->json(['message' => 'Durasi maksimal Cuti Besar adalah 3 bulan.'], 422);
    }

    if ($jenis == 'Cuti Karena Alasan Penting' && $lama_cuti > 30) {
        return response()->json(['message' => 'Durasi maksimal Cuti Alasan Penting adalah 1 bulan.'], 422);
    }

    if ($jenis == 'Cuti di Luar Tanggungan Negara' && $lama_kerja < 5) {
        return response()->json(['message' => 'Minimal 5 tahun kerja untuk mengajukan Cuti di Luar Tanggungan Negara.'], 422);
    }

    // Validasi jatah cuti tahunan
    if ($jenis == 'Cuti Tahunan') {
        $hakCuti = $this->hitungHakCutiTahunan($user);
        $terpakai = Cuti::where('id_user', $user->id_user)
            ->where('jenis_cuti', 'Cuti Tahunan')
            ->where('status', 'Disetujui')
            ->whereYear('tanggal_mulai', now()->year)
            ->sum(DB::raw('DATEDIFF(tanggal_selesai, tanggal_mulai) + 1'));

        if (($terpakai + $lama_cuti) > $hakCuti) {
            return response()->json(['message' => "Hak cuti tahunan hanya $hakCuti hari. Sudah digunakan: $terpakai hari."], 422);
        }
    }

    // Simpan data cuti
    Cuti::create([
        'id_user' => $user->id_user,
        'jenis_cuti' => $jenis,
        'tanggal_pengajuan' => now(),
        'tanggal_mulai' => $mulai,
        'tanggal_selesai' => $selesai,
        'lama_cuti' => $lama_cuti,
        'keterangan' => $request->keterangan,
        'status' => 'Menunggu',
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
