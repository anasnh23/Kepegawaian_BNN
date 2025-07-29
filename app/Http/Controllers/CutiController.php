<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MUser;
use App\Models\Cuti;
use App\Models\ApprovalPimpinan;
use App\Helpers\NotifikasiHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CutiController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $cuti = Cuti::where('id_user', $user->id_user)
                    ->orderBy('tanggal_pengajuan', 'desc')
                    ->get();

        $hakCuti = $this->hitungHakCutiTahunan($user);

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

        $cuti = Cuti::create([
            'id_user' => $user->id_user,
            'jenis_cuti' => $jenis,
            'tanggal_pengajuan' => now(),
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'lama_cuti' => $lama_cuti,
            'keterangan' => $request->keterangan,
            'status' => 'Menunggu',
        ]);

        // Kirim notifikasi ke semua admin
        $adminList = MUser::where('id_level', 1)->get();
        foreach ($adminList as $admin) {
            NotifikasiHelper::send(
                $admin->id_user,
                'cuti',
                'Pengajuan cuti baru dari ' . $user->nama,
                route('cutiadmin.index')
            );
        }

        return response()->json(['message' => 'Pengajuan cuti berhasil dikirim.']);
    }

  public function uploadDokumen(Request $request)
{
    $request->validate([
        'cuti_id' => 'required|exists:cuti,id_cuti',
        'dokumen' => 'required|file|mimes:pdf|max:2048',
    ]);

    $cuti = Cuti::with('pegawai')->findOrFail($request->cuti_id); // tambahkan relasi pegawai

    $file = $request->file('dokumen');
    $filename = time() . '_' . $file->getClientOriginalName();
    $path = $file->storeAs('dokumen_cuti', $filename, 'public');

    $approval = ApprovalPimpinan::firstOrNew(['id_cuti' => $cuti->id_cuti]);
    $approval->dokumen_path = $path;
    $approval->status = null;
    $approval->approved_by = null;
    $approval->save();

    // ✅ Kirim notifikasi ke pimpinan
    $pimpinanList = MUser::where('id_level', 3)->get();
    foreach ($pimpinanList as $pimpinan) {
        NotifikasiHelper::send(
            $pimpinan->id_user,
            'cuti',
            'Dokumen cuti dari ' . $cuti->pegawai->nama . ' telah diunggah dan menunggu persetujuan.',
            route('approval.dokumen')
        );
    }

    return back()->with('success', 'Dokumen cuti berhasil diupload ke pimpinan.');
}


    private function hitungHakCutiTahunan($user)
    {
        $tahun_ini = now()->year;
        $tahun_lalu = now()->subYear()->year;
        $dua_tahun_lalu = now()->subYears(2)->year;

        $cuti_tahun_lalu = Cuti::where('id_user', $user->id_user)
            ->where('jenis_cuti', 'Tahunan')
            ->whereYear('tanggal_mulai', $tahun_lalu)
            ->sum(DB::raw('DATEDIFF(tanggal_selesai, tanggal_mulai) + 1'));

        $cuti_dua_tahun_lalu = Cuti::where('id_user', $user->id_user)
            ->where('jenis_cuti', 'Tahunan')
            ->whereYear('tanggal_mulai', $dua_tahun_lalu)
            ->sum(DB::raw('DATEDIFF(tanggal_selesai, tanggal_mulai) + 1'));

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

        $cuti = Cuti::where('id_user', $user->id_user)
                    ->with(['pegawai', 'approvalPimpinan'])
                    ->orderByDesc('tanggal_pengajuan')
                    ->get();

        $breadcrumb = (object)[
            'title' => 'Riwayat Cuti',
            'list' => ['Dashboard', 'Riwayat Cuti']
        ];

        return view('pegawai.riwayat-cuti', compact('cuti', 'breadcrumb'))
            ->with('activeMenu', 'riwayat-cuti');
    }

    public function riwayatPersetujuanPimpinan()
    {
        $user = Auth::user();

        $cuti = Cuti::where('id_user', $user->id_user)
            ->whereHas('approvalPimpinan', function ($query) {
                $query->whereNotNull('dokumen_path');
            })
            ->with('approvalPimpinan')
            ->orderByDesc('tanggal_pengajuan')
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
