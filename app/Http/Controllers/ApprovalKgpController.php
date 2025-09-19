<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Kgp;
use App\Models\MUser;
use App\Models\RiwayatGajiModel;
use App\Helpers\NotifikasiHelper;
use App\Helpers\MasaKerja;               // ✅ konsisten dengan dashboard
use Carbon\Carbon;

class ApprovalKgpController extends Controller
{
    /**
     * Daftar usulan KGP yang masih "Menunggu" + riwayat keputusan.
     * Setiap baris KGP diperkaya properti terhitung:
     * - calc_tmt_awal   : TMT awal pegawai (yyyy-mm-dd)
     * - calc_mk_years   : masa kerja (tahun, integer)
     * - calc_mk_label   : masa kerja "X th Y bln"
     * - calc_naik_rp    : kenaikan (Rp) untuk usulan ini
     */
    public function index()
    {
        $stepYears = (int) env('KGP_STEP_YEARS', 4);               // default 4 tahun
        $perStep   = (int) env('KGP_NAIK_PER_4_TAHUN', 1000000);   // default 1 jt

        // Usulan menunggu
        $kgp = Kgp::with(['pegawai.jabatan', 'pegawai.pangkat.refPangkat'])
            ->where('status', 'Menunggu')
            ->orderByDesc('id_kgp')
            ->get();

        // Lengkapi nilai terhitung untuk UI (tanpa mengubah DB)
        $kgp->transform(function (Kgp $row) use ($stepYears, $perStep) {
            $peg = $row->pegawai;

            // TMT awal & masa kerja (helper yang sama dengan dashboard)
            $tmtAwal = MasaKerja::tanggalMulai((int) $peg->id_user);
            [$mkYears, $mkMonths] = MasaKerja::yearsMonths((int) $peg->id_user);

            // Tahap yang seharusnya sudah berhak (berdasarkan masa kerja)
            $eligibleStages = intdiv((int) $mkYears, $stepYears);

            // Tahap yang SUDAH disetujui
            $approved = Kgp::where('id_user', $peg->id_user)
                ->where('status', 'Disetujui')
                ->count();

            // Kenaikan untuk usulan menunggu ini
            $additionalStages = max(0, $eligibleStages - $approved);
            $naik = $additionalStages * $perStep;

            // simpan untuk dipakai blade
            $row->calc_tmt_awal = $tmtAwal ? $tmtAwal->toDateString() : null;
            $row->calc_mk_years = (int) $mkYears;
            $row->calc_mk_label = $mkYears.' th '.$mkMonths.' bln';
            $row->calc_naik_rp  = (int) $naik;

            return $row;
        });

        // Riwayat keputusan
        $riwayat = Kgp::with(['pegawai', 'disetujuiOleh.jabatan'])
            ->whereIn('status', ['Disetujui', 'Ditolak'])
            ->orderByDesc(DB::raw('COALESCE(disetujui_at, id_kgp)'))
            ->limit(50)
            ->get();

        return view('pimpinan.approval-kgp.index', compact('kgp', 'riwayat'))
            ->with('activeMenu', 'approval-kgp');
    }

    /**
     * SETUJUI usulan KGP.
     */
    public function approve($id, Request $request)
    {
        try {
            DB::transaction(function () use ($id) {

                /** @var \App\Models\Kgp $kgp */
                $kgp = Kgp::whereKey($id)->lockForUpdate()->firstOrFail();

                if (strtolower($kgp->status) !== 'menunggu') {
                    abort(422, 'Pengajuan sudah diproses.');
                }

                /** @var \App\Models\MUser $pegawai */
                $pegawai = MUser::with(['pangkat.refPangkat'])->findOrFail($kgp->id_user);

                // ---- Parameter umum
                $stepYears  = (int) env('KGP_STEP_YEARS', 4);
                $per4Tahun  = (int) env('KGP_NAIK_PER_4_TAHUN', 1000000);
                $now        = Carbon::now('Asia/Jakarta');

                // ---- TMT awal (pakai helper + fallback)
                $tmtStart =
                    MasaKerja::tanggalMulai((int) $pegawai->id_user) ?:
                    ($kgp->tmt ? Carbon::parse($kgp->tmt) : null) ?:
                    (!empty($pegawai->tanggal_masuk) ? Carbon::parse($pegawai->tanggal_masuk) : null) ?:
                    (!empty($pegawai->created_at) ? Carbon::parse($pegawai->created_at) : null);

                if (!$tmtStart) {
                    abort(422, 'TMT awal pegawai tidak ditemukan. Lengkapi salah satu: riwayat_jabatan.tmt_mulai / kgp.tmt / riwayat_gaji.tanggal_berlaku / presensi.tanggal / m_user.tanggal_masuk.');
                }

                // ---- Hitung tahap berikut = (jumlah KGP Disetujui + 1)
                $approvedCount = Kgp::where('id_user', $pegawai->id_user)
                    ->where('status', 'Disetujui')
                    ->count();
                $nextStage = $approvedCount + 1;

                // ---- Tanggal teoritis tahap berikut
                $targetDate = $tmtStart->copy()->addYears($nextStage * $stepYears);
                if ($now->lt($targetDate)) {
                    abort(422, 'Belum saatnya (eligible mulai '.$targetDate->translatedFormat('d F Y').').');
                }

                // ---- Gaji pokok dari referensi pangkat
                $gajiPokok = (int) ($pegawai->pangkat?->refPangkat?->gaji_pokok ?? 0);
                if ($gajiPokok <= 0) {
                    $idRef = optional($pegawai->pangkat)->id_ref_pangkat;
                    if ($idRef) {
                        $gajiPokok = (int) DB::table('ref_golongan_pangkat')
                            ->where('id_ref_pangkat', $idRef)
                            ->value('gaji_pokok') ?: 0;
                    }
                }

                // ---- Tunjangan masa kerja (pakai helper years agar tidak 0)
                $mkYears    = MasaKerja::years((int) $pegawai->id_user);
                $tunjanganMK = intdiv($mkYears, 4) * $per4Tahun;
                $gajiTotal   = $gajiPokok + $tunjanganMK;

                // ---- Update status pengajuan + simpan TMT tahap ini
                $kgp->update([
                    'status'         => 'Disetujui',
                    'tmt'            => $targetDate->toDateString(),
                    'disetujui_oleh' => Auth::user()->id_user,
                    'disetujui_at'   => $now,
                ]);

                // ---- Simpan ke riwayat gaji pada tanggal teoritis
                $dataRiwayat = [
                    'id_user'         => $pegawai->id_user,
                    'tanggal_berlaku' => $targetDate->toDateString(),
                    'gaji_pokok'      => $gajiPokok,
                    'keterangan'      => 'KGP disetujui (tahap '.$nextStage.', masa kerja: '.$mkYears.' th)',
                ];

                if (Schema()->hasColumn('riwayat_gaji', 'tunjangan_mk')) {
                    $dataRiwayat['tunjangan_mk'] = $tunjanganMK;
                }
                if (Schema()->hasColumn('riwayat_gaji', 'gaji_total')) {
                    $dataRiwayat['gaji_total'] = $gajiTotal;
                }

                RiwayatGajiModel::create($dataRiwayat);

                // ---- Notifikasi
                NotifikasiHelper::send(
                    $pegawai->id_user,
                    'kgp',
                    'Pengajuan KGP Anda DISETUJUI oleh '.$this->currentPimpinanName().'.',
                    route('kgp.riwayat')
                );
            });

            return $request->ajax()
                ? response()->json(['message' => 'KGP berhasil disetujui.'])
                : back()->with('success', 'KGP berhasil disetujui.');

        } catch (\Throwable $e) {
            $msg = 'Terjadi kesalahan: '.$e->getMessage();
            return $request->ajax()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * TOLAK usulan KGP + kirim notifikasi.
     */
    public function reject($id, Request $request)
    {
        try {
            DB::transaction(function () use ($id, $request) {

                /** @var \App\Models\Kgp $kgp */
                $kgp = Kgp::whereKey($id)->lockForUpdate()->firstOrFail();

                if (strtolower($kgp->status) !== 'menunggu') {
                    abort(422, 'Pengajuan sudah diproses.');
                }

                $kgp->update([
                    'status'         => 'Ditolak',
                    'catatan'        => $request->input('catatan', 'Ditolak pimpinan'),
                    'disetujui_oleh' => Auth::user()->id_user,
                    'disetujui_at'   => Carbon::now('Asia/Jakarta'),
                ]);

                NotifikasiHelper::send(
                    $kgp->id_user,
                    'kgp',
                    'Pengajuan KGP Anda DITOLAK oleh '.$this->currentPimpinanName().
                    '. Alasan: '.$kgp->catatan,
                    route('kgp.riwayat')
                );
            });

            return $request->ajax()
                ? response()->json(['message' => 'KGP ditolak.'])
                : back()->with('error', 'KGP ditolak.');

        } catch (\Throwable $e) {
            $msg = 'Terjadi kesalahan: '.$e->getMessage();
            return $request->ajax()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /** Nama pimpinan yang sedang login (fallback: User #id_user). */
    private function currentPimpinanName(): string
    {
        $u = Auth::user();
        return $u?->nama ? $u->nama : ('User #'.($u?->id_user ?? '-'));
    }
}

/**
 * Helper kecil untuk cek kolom eksis tanpa menambah import.
 */
if (! function_exists('Schema')) {
    function Schema() {
        return app('db')->connection()->getSchemaBuilder();
    }
}
