<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\MUser;
use App\Helpers\NotifikasiHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminCutiController extends Controller
{
    /**
     * Daftar semua pengajuan cuti untuk Admin.
     * Dukungan filter query string:
     * - ?status=Menunggu|Disetujui|Ditolak
     * - ?q=kata_kunci   (nama / NIP)
     * - ?dari=YYYY-MM-DD&hingga=YYYY-MM-DD
     */
    public function index(Request $request)
    {
        $cutiQ = Cuti::with(['pegawai:id_user,nip,nama', 'approver:id_user,nama'])
            // urutkan paling baru menurut tanggal_pengajuan; fallback created_at
            ->orderByDesc('tanggal_pengajuan')
            ->orderByDesc('created_at');

        // Filter status (opsional)
        if ($request->filled('status')) {
            $cutiQ->where('status', $request->string('status'));
        }

        // Filter kata kunci nama/NIP (opsional)
        if ($request->filled('q')) {
            $q = $request->string('q');
            $cutiQ->whereHas('pegawai', function ($qq) use ($q) {
                $qq->where('nama', 'like', "%{$q}%")
                   ->orWhere('nip', 'like', "%{$q}%");
            });
        }

        // Filter rentang tanggal pengajuan (opsional)
        if ($request->filled('dari')) {
            $cutiQ->whereDate('tanggal_pengajuan', '>=', $request->date('dari'));
        }
        if ($request->filled('hingga')) {
            $cutiQ->whereDate('tanggal_pengajuan', '<=', $request->date('hingga'));
        }

        $cuti = $cutiQ->get();

        $breadcrumb = (object) [
            'title' => 'Manajemen Cuti Pegawai',
            'list'  => ['Dashboard', 'Kepegawaian', 'Manajemen Cuti'],
        ];

        return view('cuti.admin', compact('cuti', 'breadcrumb'))
            ->with('activeMenu', 'cuti');
    }

    /**
     * Ubah status via AJAX (JSON).
     * Body: id (id_cuti), status (Disetujui|Ditolak|Menunggu)
     */
    public function setStatus(Request $request)
    {
        $request->validate([
            'id'     => 'required|exists:cuti,id_cuti',
            'status' => 'required|in:Disetujui,Ditolak,Menunggu',
        ]);

        $result = DB::transaction(function () use ($request) {
            // Kunci baris agar aman dari race condition
            $cuti = Cuti::with('pegawai')->lockForUpdate()->findOrFail($request->id);

            // Idempotent: jika status sama, tidak perlu kirim notif berulang
            $oldStatus = $cuti->status;
            $newStatus = $request->status;

            if ($oldStatus !== $newStatus) {
                $cuti->status      = $newStatus;
                $cuti->approved_by = Auth::user()->id_user;
                $cuti->updated_at  = now();
                $cuti->save();
            }

            // === Notifikasi ===
            // 1) Selalu kabari pemohon (meskipun status tidak berubah, aman)
            NotifikasiHelper::send(
                $cuti->id_user,
                'cuti',
                'Status pengajuan cuti Anda: ' . strtolower($cuti->status) . '.',
                route('cuti.riwayat')
            );

            // 2) Jika dikembalikan ke "Menunggu", kabari pimpinan untuk persetujuan
            if ($cuti->status === 'Menunggu') {
                $pimpinanIds = MUser::where('id_level', 3)->pluck('id_user');
                foreach ($pimpinanIds as $pid) {
                    NotifikasiHelper::send(
                        $pid,
                        'cuti',
                        'Butuh persetujuan: pengajuan cuti dari ' . ($cuti->pegawai->nama ?? 'pegawai'),
                        route('approval.dokumen')
                    );
                }
            }

            return [
                'id'          => $cuti->getKey(),
                'old_status'  => $oldStatus,
                'new_status'  => $cuti->status,
                'approved_by' => $cuti->approved_by,
            ];
        });

        return response()->json([
            'message' => 'Status cuti berhasil diperbarui.',
            'data'    => $result,
        ]);
    }

    /**
     * Form edit status (opsional, jika butuh halaman terpisah).
     */
    public function edit($id)
    {
        $cuti = Cuti::with(['pegawai', 'approver'])->findOrFail($id);

        $breadcrumb = (object) [
            'title' => 'Edit Status Cuti',
            'list'  => ['Dashboard', 'Kepegawaian', 'Manajemen Cuti', 'Edit Cuti'],
        ];

        return view('cuti.editcuti', compact('cuti', 'breadcrumb'))
            ->with('activeMenu', 'cuti');
    }

    /**
     * Ubah status via form (redirect).
     * Param URL: {id}
     * Body: status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak,Menunggu',
        ]);

        // Reuse ke setStatus agar satu pintu
        $proxy = new Request(array_merge($request->all(), ['id' => $id]));
        $this->setStatus($proxy);

        return redirect()
            ->route('cuti.admin')
            ->with('success', 'Status cuti berhasil diperbarui.');
    }
}
