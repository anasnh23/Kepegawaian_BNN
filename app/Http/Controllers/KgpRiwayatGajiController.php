<?php

namespace App\Http\Controllers;

use App\Models\MUser;
use App\Models\RiwayatGajiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KgpRiwayatGajiController extends Controller
{
    protected int $stepYears;           // kelipatan tahun
    protected ?int $flatRaise;          // kenaikan flat (opsional)
    protected float $percentRaise;      // kenaikan persen (opsional)
    protected bool $includeTmkInTotal;  // hitung TMK di total?

    public function __construct()
    {
        $this->stepYears         = (int) env('KGP_STEP_YEARS', 4);
        $this->flatRaise         = env('KGP_FLAT') !== null ? (int) env('KGP_FLAT') : null;
        $this->percentRaise      = (float) env('KGP_PERCENT', 0.00);
        $this->includeTmkInTotal = filter_var(env('KGP_INCLUDE_TMK_IN_TOTAL', true), FILTER_VALIDATE_BOOL);
    }

    /** ============================== INDEX ============================== */
    public function index(Request $request)
    {
        $auth   = Auth::user();
        $userId = ($auth->id_level == 1 && $request->filled('user_id'))
            ? (int) $request->get('user_id')
            : $auth->id_user;

        /** @var \App\Models\MUser $user */
        $user = MUser::with(['gajiTerakhir','pangkat.refPangkat','level'])->findOrFail($userId);

        // Riwayat (semua approval KGP yang sudah dibuat admin/pimpinan)
        $riwayat = RiwayatGajiModel::where('id_user', $user->id_user)
            ->orderBy('tanggal_berlaku', 'asc')
            ->get();

        // Baseline dari referensi golongan
        $gajiDasar = (int) ($user->gaji_pokok_dasar ?? 0);

        // Gaji berjalan = riwayat terbaru -> fallback baseline (anti-0)
        $gajiBerjalan = (int) ($user->gaji_pokok ?? 0);
        if ($gajiBerjalan === 0 && $gajiDasar > 0) {
            $gajiBerjalan = $gajiDasar;
        }

        // TMK per tahap (default 1.000.000)
        $tmkPerStage = (int) env('KGP_NAIK_PER_4_TAHUN', 1000000);

        // === Inti perubahan: TMK dihitung berdasar jumlah tahap yang SUDAH DISETUJUI (jumlah baris riwayat)
        //    -> Tahap 1 disetujui = 1 * 1jt, dst.
        $approvedStageCount = $riwayat->count();

        $tmk = 0;
        if ($this->includeTmkInTotal && $approvedStageCount > 0) {
            $tmk = $approvedStageCount * $tmkPerStage;
        }

        $totalGaji = $gajiBerjalan + $tmk;

        // Timeline (mulai Stage-1, selalu munculkan Tahap 1. Jika ada riwayat, tandai Disetujui)
        $tmtStart    = $user->tanggal_mulai_kerja;   // bisa null, tapi tetap render timeline dengan basis riwayat
        $kgpTimeline = $this->buildKgpTimeline($user, $tmtStart, $riwayat, $tmkPerStage, $approvedStageCount);
        $elig        = $kgpTimeline['eligibility'];

        $breadcrumb = (object)[
            'title' => $auth->id_level == 1 ? 'KGP • Riwayat Gaji Pegawai' : 'KGP • Riwayat Gaji Saya',
            'list'  => ['Dashboard', 'Kepegawaian', 'KGP / Riwayat Gaji'],
        ];
        $activeMenu = 'riwayat_gaji_kgp';

        return view('riwayat_gaji_kgp.index', [
            'user'            => $user,
            'riwayat'         => $riwayat,
            'kgpTimeline'     => $kgpTimeline,
            'gajiDasar'       => $gajiDasar,
            'gajiBerjalan'    => $gajiBerjalan,
            'tmk'             => $tmk,
            'totalGaji'       => $totalGaji,
            'elig'            => $elig,
            'breadcrumb'      => $breadcrumb,
            'activeMenu'      => $activeMenu,
            'approvedStages'  => $approvedStageCount,
            'tmkPerStage'     => $tmkPerStage,
        ]);
    }

    /** ====================== APPROVE TAHAP BERIKUT ====================== */
    public function approveTahapBerikut(Request $request, int $id_user)
    {
        $auth = Auth::user();
        abort_unless($auth->id_level == 1, 403, 'Hanya admin/pimpinan.');

        /** @var \App\Models\MUser $user */
        $user = MUser::with(['gajiTerakhir','pangkat.refPangkat'])->findOrFail($id_user);

        $riwayat  = RiwayatGajiModel::where('id_user', $user->id_user)->orderBy('tanggal_berlaku')->get();
        $timeline = $this->buildKgpTimeline($user, $user->tanggal_mulai_kerja, $riwayat, (int) env('KGP_NAIK_PER_4_TAHUN',1000000), $riwayat->count());
        $elig     = $timeline['eligibility'];

        if (!$elig['eligible_now']) {
            return back()->with('warning', 'Belum saatnya tahap berikut disetujui.');
        }

        // Basis kenaikan: baseline (referensi golongan), fallback gaji berjalan kalau kosong
        $base = (int) ($user->gaji_pokok_dasar ?? 0);
        if ($base === 0) {
            $base = (int) ($user->gaji_pokok ?? 0);
        }

        // Hitung gaji pokok baru (sesuai kebijakanmu)
        $new = $base;
        if ($this->flatRaise !== null) {
            $new = $base + $this->flatRaise;
        } elseif ($this->percentRaise > 0) {
            $new = (int) round($base * (1 + $this->percentRaise));
        }

        // Tanggal berlaku
        $tanggalInput = $request->input('tanggal_berlaku');
        $tanggal      = $tanggalInput ?: $elig['eligible_from']->toDateString();

        RiwayatGajiModel::create([
            'id_user'         => $user->id_user,
            'tanggal_berlaku' => $tanggal,
            'gaji_pokok'      => $new,
            'keterangan'      => 'KGP disetujui (tahap '.$elig['next_stage'].')',
        ]);

        return back()->with('success',
            'KGP Tahap '.$elig['next_stage'].' disetujui. Gaji pokok baru: Rp '.number_format($new,0,',','.')
        );
    }

    /* ============================== UTILITIES ============================== */

    /**
     * Build timeline:
     * - Selalu munculkan Stage-1.
     * - Jika ada riwayat, tandai stage sesuai jumlahnya sebagai "Disetujui".
     * - Nilai badge "Total" = gaji_pokok(stage) + (approvedStageCount * tmkPerStage).
     */
    protected function buildKgpTimeline(
        MUser $user,
        ?Carbon $tmtStart,
        $riwayatCollection,
        int $tmkPerStage,
        int $approvedStageCount
    ): array {
        $now         = Carbon::now('Asia/Jakarta');
        $stages      = [];
        $riil        = collect($riwayatCollection)->sortBy('tanggal_berlaku')->values();
        $latestPokok = (int) optional($riil->last())->gaji_pokok;

        // --- hitung berapa stage “menurut kalender”
        $calendarStageCount = 0;
        if ($tmtStart) {
            $diffYears          = $tmtStart->diffInYears($now);
            $calendarStageCount = max(1, intdiv($diffYears, $this->stepYears)); // min. 1 supaya Tahap 1 selalu muncul
        } else {
            $calendarStageCount = 1; // jika TMT tidak ada, tetap munculkan Tahap 1
        }

        // stage yang harus ditampilkan = max(hasil kalender, jumlah riwayat, 1)
        $maxStage = max($calendarStageCount, $riil->count(), 1);

        for ($i = 1; $i <= $maxStage; $i++) {
            // tanggal teoritis
            $date = $tmtStart ? $tmtStart->copy()->addYears($i * $this->stepYears) : $now;

            // approved jika sudah ada baris riwayat ke-i
            $row      = $riil->get($i - 1);         // riwayat ke-1 → Stage 1
            $approved = (bool) $row;

            $gPokok   = $approved
                ? (int) $row->gaji_pokok
                : ($i == 1 ? ((int) ($user->gaji_pokok ?? $user->gaji_pokok_dasar ?? 0)) : null);

            // Total per tampilan = gaji pokok stage ini + TMK kumulatif yg sudah disetujui
            $gTotal = null;
            if ($approved) {
                $gTotal = $gPokok + ($approvedStageCount * $tmkPerStage);
            }

            $stages[] = [
                'stage'      => $i,
                'date'       => $date,
                'approved'   => $approved,
                'gaji'       => $gPokok,
                'gaji_total' => $gTotal,
                'label'      => 'Tahap '.$i,
            ];
        }

        // eligibility tahap berikut (berdasar TMT; jika TMT null, pakai hari ini)
        $startBase    = $tmtStart ?: $now;
        $nextStage    = $riil->count() + 1;
        $eligibleFrom = $startBase->copy()->addYears($nextStage * $this->stepYears);
        $eligibleNow  = $now->greaterThanOrEqualTo($eligibleFrom);

        return [
            'stages'         => $stages,
            'approved_count' => $approvedStageCount,
            'next_stage'     => $nextStage,
            'eligibility'    => [
                'eligible_now'  => $eligibleNow,
                'eligible_from' => $eligibleFrom,
                'next_stage'    => $nextStage,
            ],
        ];
    }
}
