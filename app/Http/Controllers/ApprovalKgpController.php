<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Kgp;              
use App\Models\MUser;
use App\Models\RiwayatGajiModel;
use Carbon\Carbon;

class ApprovalKgpController extends Controller
{
    /**
     * Tampilkan daftar usulan KGP (Kenaikan Gaji Berkala)
     */
    public function index()
    {
        $kgp = Kgp::with('pegawai')
            ->where('status', 'Menunggu')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pimpinan.approval-kgp.index', compact('kgp'));
    }

    /**
     * Pimpinan menyetujui usulan KGP
     */
    public function approve($id, Request $request)
    {
        try {
            DB::transaction(function () use ($id) {
                $kgp = Kgp::findOrFail($id);
                $pegawai = MUser::with('pangkat')->findOrFail($kgp->id_user);

                $masaKerja   = Carbon::parse($pegawai->tmt_masuk)->diffInYears(now());
                $tunjanganMK = floor($masaKerja / 4) * 1000000;
                $gajiPokok   = $pegawai->pangkat->gaji_pokok ?? 0;

                $kgp->update([
                    'status'         => 'Disetujui',
                    'disetujui_oleh' => Auth::id(),
                    'disetujui_at'   => now(),
                ]);

                RiwayatGajiModel::create([
                    'id_user'         => $pegawai->id_user,
                    'tanggal_berlaku' => $kgp->tmt ?? now()->toDateString(),
                    'gaji_pokok'      => $gajiPokok,
                    'tunjangan_mk'    => $tunjanganMK,
                    'gaji_total'      => $gajiPokok + $tunjanganMK,
                    'keterangan'      => 'KGP periode ke-' . ($kgp->periode_ke ?? '-'),
                ]);
            });

            // Jika request Ajax → JSON
            if ($request->ajax()) {
                return response()->json(['message' => 'KGP berhasil disetujui.']);
            }

            // Jika bukan Ajax → redirect biasa
            return redirect()->back()->with('success', 'KGP berhasil disetujui.');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Pimpinan menolak usulan KGP
     */
    public function reject($id, Request $request)
    {
        try {
            $kgp = Kgp::findOrFail($id);

            $kgp->update([
                'status'         => 'Ditolak',
                'catatan'        => $request->catatan ?? 'Ditolak pimpinan',
                'disetujui_oleh' => Auth::id(),
                'disetujui_at'   => now(),
            ]);

            if ($request->ajax()) {
                return response()->json(['message' => 'KGP ditolak.']);
            }

            return redirect()->back()->with('error', 'KGP ditolak.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
