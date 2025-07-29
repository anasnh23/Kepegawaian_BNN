<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\ApprovalPimpinan;
use Illuminate\Support\Facades\Auth;

class PimpinanCutiController extends Controller
{
    /**
     * Halaman utama approval dokumen cuti
     */
    public function index()
    {
        // Ambil semua cuti yang memiliki dokumen dan belum diputuskan
        $cuti = Cuti::whereHas('approvalPimpinan', function ($query) {
                $query->whereNotNull('dokumen_path')
                      ->whereNull('status');
            })
            ->with(['pegawai', 'approvalPimpinan'])
            ->orderByDesc('tanggal_pengajuan')
            ->get();

        $breadcrumb = (object) [
            'title' => 'Persetujuan Dokumen Cuti',
            'list' => ['Dashboard', 'Approval Dokumen']
        ];

        return view('pimpinan.approval-dokumen', compact('cuti', 'breadcrumb'))
            ->with('activeMenu', 'approval-dokumen');
    }

    /**
     * Menyetujui dokumen (non-AJAX)
     */
    public function approve($id)
    {
        $cuti = Cuti::with('approvalPimpinan')->findOrFail($id);
        $approval = $cuti->approvalPimpinan;

        if (!$approval) {
            return back()->with('error', 'Data approval tidak ditemukan.');
        }

        $approval->status = 'Disetujui';
        $approval->approved_by = Auth::user()->id_user;
        $approval->updated_at = now();
        $approval->save();

        $cuti->status = 'Disetujui';
        $cuti->approved_by = Auth::user()->id_user;
        $cuti->updated_at = now();
        $cuti->save();

        return redirect()->back()->with('success', 'Dokumen berhasil disetujui.');
    }

    /**
     * Menolak dokumen (non-AJAX)
     */
    public function reject($id)
    {
        $cuti = Cuti::with('approvalPimpinan')->findOrFail($id);
        $approval = $cuti->approvalPimpinan;

        if (!$approval) {
            return back()->with('error', 'Data approval tidak ditemukan.');
        }

        $approval->status = 'Ditolak';
        $approval->approved_by = Auth::user()->id_user;
        $approval->updated_at = now();
        $approval->save();

        $cuti->status = 'Ditolak';
        $cuti->approved_by = Auth::user()->id_user;
        $cuti->updated_at = now();
        $cuti->save();

        return redirect()->back()->with('success', 'Dokumen telah ditolak.');
    }

    /**
     * Riwayat dokumen yang sudah disetujui/ditolak
     */
    public function riwayat()
    {
        $cuti = Cuti::whereHas('approvalPimpinan', function ($query) {
                $query->whereNotNull('dokumen_path')
                      ->whereNotNull('status');
            })
            ->with(['pegawai', 'approvalPimpinan'])
            ->orderByDesc('tanggal_pengajuan')
            ->get();

        $breadcrumb = (object) [
            'title' => 'Riwayat Persetujuan Dokumen',
            'list' => ['Dashboard', 'Riwayat Approval']
        ];

        return view('pimpinan.riwayat-approval', compact('cuti', 'breadcrumb'))
            ->with('activeMenu', 'riwayat-approval');
    }

    /**
     * Update status via AJAX
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:approval_pimpinan,id',
            'status' => 'required|in:Disetujui,Ditolak',
        ]);

        $approval = ApprovalPimpinan::findOrFail($request->id);

        $approval->status = $request->status;
        $approval->approved_by = Auth::user()->id_user;
        $approval->updated_at = now();
        $approval->save();

        $cuti = $approval->cuti;
        if ($cuti) {
            $cuti->status = $request->status;
            $cuti->approved_by = Auth::user()->id_user;
            $cuti->updated_at = now();
            $cuti->save();
        }

        return response()->json(['message' => 'Status berhasil diperbarui.']);
    }

    
}
