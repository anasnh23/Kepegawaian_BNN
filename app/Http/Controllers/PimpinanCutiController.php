<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Helpers\NotifikasiHelper;
use App\Models\ApprovalPimpinan;
use Illuminate\Support\Facades\Auth;

class PimpinanCutiController extends Controller
{
    /**
     * Halaman utama approval dokumen cuti (yang masih menunggu).
     */
    public function index()
    {
        $cuti = Cuti::whereHas('approvalPimpinan', function ($query) {
                $query->whereNotNull('dokumen_path')
                      ->whereNull('status');
            })
            ->with(['pegawai', 'approvalPimpinan'])
            ->orderByDesc('tanggal_pengajuan')
            ->get();

        $breadcrumb = (object) [
            'title' => 'Persetujuan Dokumen Cuti',
            'list'  => ['Dashboard', 'Approval Dokumen']
        ];

        return view('pimpinan.approval-dokumen', compact('cuti', 'breadcrumb'))
            ->with('activeMenu', 'approval-dokumen');
    }

    /**
     * Form edit status approval pimpinan.
     */
    public function edit($id)
    {
        $approval = ApprovalPimpinan::with('cuti.pegawai')->findOrFail($id);

        $breadcrumb = (object) [
            'title' => 'Ubah Status Approval',
            'list'  => ['Dashboard', 'Approval Dokumen', 'Ubah Status']
        ];

        return view('pimpinan.edit-approval', compact('approval', 'breadcrumb'))
            ->with('activeMenu', 'approval-dokumen');
    }

    /**
     * Menyetujui dokumen cuti.
     */
    public function approve($id)
    {
        $cuti = Cuti::with('approvalPimpinan')->findOrFail($id);
        $approval = $cuti->approvalPimpinan;

        if (!$approval) {
            return back()->with('error', 'Data approval tidak ditemukan.');
        }

        $this->applyStatus($approval, 'Disetujui');

        // 🔔 Notifikasi ke pegawai
        NotifikasiHelper::send(
            $cuti->id_user,
            'cuti',
            'Dokumen cuti Anda telah <strong>disetujui</strong> oleh pimpinan.',
            route('cuti.riwayat')
        );

        return redirect()->back()->with('success', 'Dokumen berhasil disetujui.');
    }

    /**
     * Menolak dokumen cuti.
     */
    public function reject($id)
    {
        $cuti = Cuti::with('approvalPimpinan')->findOrFail($id);
        $approval = $cuti->approvalPimpinan;

        if (!$approval) {
            return back()->with('error', 'Data approval tidak ditemukan.');
        }

        $this->applyStatus($approval, 'Ditolak');

        // 🔔 Notifikasi ke pegawai
        NotifikasiHelper::send(
            $cuti->id_user,
            'cuti',
            'Dokumen cuti Anda telah <strong>ditolak</strong> oleh pimpinan.',
            route('cuti.riwayat')
        );

        return redirect()->back()->with('success', 'Dokumen telah ditolak.');
    }

    /**
     * Riwayat approval dokumen cuti (yang sudah diproses).
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
            'list'  => ['Dashboard', 'Riwayat Approval']
        ];

        return view('pimpinan.riwayat-approval', compact('cuti', 'breadcrumb'))
            ->with('activeMenu', 'riwayat-approval');
    }

    /**
     * Update status approval via form (edit) — URL: PUT /approval-dokumen/{id}/update-status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Menunggu,Disetujui,Ditolak',
        ]);

        $approval = ApprovalPimpinan::findOrFail($id);
        $this->applyStatus($approval, $request->status, true);

        return redirect()->route('approval.dokumen')->with('success', 'Status cuti berhasil diperbarui.');
    }

    /**
     * 🔸 Tambahan: Update status melalui AJAX (tanpa {id} di URL)
     * URL: POST /approval-dokumen/update-status
     * Body: { id: <approval_id>, status: Menunggu|Disetujui|Ditolak }
     */
    public function updateStatusAjax(Request $request)
    {
        $request->validate([
            'id'     => 'required|integer|exists:approval_pimpinan,id',
            'status' => 'required|in:Menunggu,Disetujui,Ditolak',
        ]);

        $approval = ApprovalPimpinan::findOrFail($request->id);
        $this->applyStatus($approval, $request->status, true);

        return response()->json([
            'ok'      => true,
            'message' => 'Status cuti berhasil diperbarui.',
        ]);
    }

    /* ===================== UTIL ===================== */

    /**
     * Terapkan status ke ApprovalPimpinan & sinkron ke tabel cuti + kirim notifikasi.
     */
    private function applyStatus(ApprovalPimpinan $approval, string $status, bool $sendNotif = false): void
    {
        $approval->status      = $status;
        $approval->approved_by = Auth::user()->id_user ?? Auth::id();
        $approval->updated_at  = now();
        $approval->save();

        // Sinkron ke tabel cuti
        $cuti = $approval->cuti()->first();
        if ($cuti) {
            $cuti->status      = $status;
            $cuti->approved_by = Auth::user()->id_user ?? Auth::id();
            $cuti->updated_at  = now();
            $cuti->save();

            if ($sendNotif) {
                $pesan = match($status) {
                    'Disetujui' => 'Dokumen cuti Anda telah <strong>disetujui</strong> oleh pimpinan.',
                    'Ditolak'   => 'Dokumen cuti Anda telah <strong>ditolak</strong> oleh pimpinan.',
                    default     => 'Dokumen cuti Anda masih <strong>menunggu persetujuan</strong>.'
                };

                NotifikasiHelper::send(
                    $cuti->id_user,
                    'cuti',
                    $pesan,
                    route('cuti.riwayat')
                );
            }
        }
    }
}
