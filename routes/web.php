<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controllers
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardPegawaiController;
use App\Http\Controllers\DashboardPimpinanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PangkatController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PresensiAdminController;
use App\Http\Controllers\PresensiDinasController;
use App\Http\Controllers\PimpinanCutiController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\AdminCutiController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\RefJabatanController;
use App\Http\Controllers\RiwayatGajiController;

// === KGP ===
use App\Http\Controllers\PengajuanKgpController;
use App\Http\Controllers\ApprovalKgpController;
use App\Http\Controllers\KgpRiwayatGajiController;

// === Dashboard Info (Admin kelola pengumuman di dashboard pegawai) ===
use App\Http\Controllers\DashboardInfoController;

// Root → dashboard / login
Route::get('/', function () {
    if (Auth::check()) return redirect('/dashboard');
    return redirect('/login');
});

// Router dashboard per level
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->id_level == 1) return redirect('/dashboard-admin');
    if ($user->id_level == 2) return redirect('/dashboard-pegawai');
    if ($user->id_level == 3) return redirect('/dashboard-pimpinan');
    abort(403);
})->name('dashboard');

// ===================== AUTH =====================
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'postlogin']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Semua route di bawah wajib login
Route::middleware(['auth'])->group(function () {

    /* ===================== DASHBOARD ===================== */
    Route::get('/dashboard-admin',    [DashboardAdminController::class,    'index'])->name('dashboard.admin');
    Route::get('/dashboard-pegawai',  [DashboardPegawaiController::class,  'index'])->name('dashboard.pegawai');
    Route::get('/dashboard-pimpinan', [DashboardPimpinanController::class, 'index'])->name('dashboard.pimpinan');

    /* ===================== PROFIL ===================== */
    Route::get('/profil',                  [ProfilController::class, 'show'])->name('profil.show');
    Route::get('/profil/edit',             [ProfilController::class, 'edit'])->name('profil.edit');
    Route::post('/profil',                 [ProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/update-password', [ProfilController::class, 'updatePassword'])->name('profil.updatePassword');

    /* ===================== PRESENSI ===================== */
    Route::get('/presensi',        [PresensiController::class, 'index'])->name('presensi.index');
    Route::post('/presensi',       [PresensiController::class, 'store'])->name('presensi.store');
    Route::get('/presensi-admin',  [PresensiAdminController::class, 'index'])->name('presensi.admin');
    Route::get('/presensi/export', [PresensiAdminController::class, 'exportExcel'])->name('presensi.export');

    // Presensi Dinas Luar
    Route::get('/presensi-dinas',        [PresensiDinasController::class, 'index'])->name('presensi.dinas');
    Route::post('/presensi-dinas/store', [PresensiDinasController::class, 'store'])->name('presensi.dinas.store');

    /* ===================== PEGAWAI & PANGKAT ===================== */
    Route::resource('/pegawai', PegawaiController::class);
    Route::resource('/pangkat', PangkatController::class);

    /* ===================== CUTI ===================== */
    // Pegawai
    Route::get('/cutipegawai',          [CutiController::class, 'index'])->name('cuti.pegawai');
    Route::post('/cuti/store',          [CutiController::class, 'store'])->name('cuti.store');
    Route::get('/riwayat-cuti',         [CutiController::class, 'riwayat'])->name('cuti.riwayat');
    Route::get('/cuti/cetak/{id}',      [CutiController::class, 'cetak'])->name('cuti.cetak');
    Route::post('/cuti/upload-dokumen', [CutiController::class, 'uploadDokumen'])->name('cuti.uploadDokumen');

    // Admin
    Route::get('/cutiadmin',               [AdminCutiController::class, 'index'])->name('cutiadmin.index');
    Route::post('/cuti/set-status',        [AdminCutiController::class, 'setStatus'])->name('cuti.setStatus');
    Route::get('/cuti/edit/{id}',          [AdminCutiController::class, 'edit'])->name('cuti.edit');
    Route::put('/cuti/update-status/{id}', [AdminCutiController::class, 'updateStatus'])->name('cuti.updateStatus');

    // Pimpinan: Approval Dokumen Cuti
    Route::get('/approval-dokumen',                    [PimpinanCutiController::class, 'index'])->name('approval.dokumen');
    Route::post('/approval-dokumen/setujui/{id}',      [PimpinanCutiController::class, 'approve'])->name('dokumen.setujui');
    Route::post('/approval-dokumen/tolak/{id}',        [PimpinanCutiController::class, 'reject'])->name('dokumen.tolak');
    Route::get('/riwayat-approval',                    [PimpinanCutiController::class, 'riwayat'])->name('riwayat.approval');
    Route::get('/approval-dokumen/{id}/edit',          [PimpinanCutiController::class, 'edit'])->name('approval.edit');
    Route::put('/approval-dokumen/{id}/update-status', [PimpinanCutiController::class, 'updateStatus'])->name('approval.updateStatus');

    // 🔹 Endpoint AJAX tanpa {id}
    Route::post('/approval-dokumen/update-status', [PimpinanCutiController::class, 'updateStatusAjax'])
        ->name('approval.dokumen.updateStatusAjax');

    /* ===================== NOTIFIKASI ===================== */
    Route::prefix('notifikasi')->group(function () {
        Route::get('/',              [NotifikasiController::class, 'semua'])->name('notifikasi.semua');
        Route::get('/{id}/baca',     [NotifikasiController::class, 'baca'])->name('notifikasi.baca.id');
        Route::post('/tandai-semua', [NotifikasiController::class, 'tandaiSemua'])->name('notifikasi.tandaiSemua');
        Route::get('/tandai-semua',  [NotifikasiController::class, 'tandaiSemua']); // fallback GET
    });

    /* ===================== MASTER & RIWAYAT GAJI ===================== */
    Route::resource('/ref_jabatan', RefJabatanController::class);
    // Route::resource('/riwayat_gaji', RiwayatGajiController::class);

    /* ===================== KGP ===================== */
    // Pegawai
    Route::get('/kgp/pengajuan',         [PengajuanKgpController::class, 'index'])->name('kgp.pengajuan');
    Route::post('/kgp/store',            [PengajuanKgpController::class, 'store'])->name('kgp.store');
    Route::get('/kgp/riwayat-pengajuan', [PengajuanKgpController::class, 'riwayat'])->name('kgp.riwayat.pengajuan');

    // Riwayat Gaji KGP
    Route::get('/kgp/riwayat',           [KgpRiwayatGajiController::class, 'index'])->name('kgp.riwayat');
    Route::get('/kgp/riwayat/{id_user}', [KgpRiwayatGajiController::class, 'index'])->name('kgp.riwayat.user');
    Route::post('/kgp/approve/{id_user}',[KgpRiwayatGajiController::class, 'approveNextStage'])->name('kgp.approve');

    // Pimpinan
    Route::get('/approval-kgp',               [ApprovalKgpController::class, 'index'])->name('approval.kgp');
    Route::post('/approval-kgp/approve/{id}', [ApprovalKgpController::class, 'approve'])->name('approval.kgp.approve');
    Route::post('/approval-kgp/reject/{id}',  [ApprovalKgpController::class, 'reject'])->name('approval.kgp.reject');

// ===================== DASHBOARD INFO (ADMIN ONLY UI) =====================
Route::prefix('dashboard-info')->group(function () {
    Route::get('/',          [DashboardInfoController::class, 'index'])->name('dashboard-info.index');
    Route::get('/create',    [DashboardInfoController::class, 'create'])->name('dashboard-info.create');
    Route::post('/',         [DashboardInfoController::class, 'store'])->name('dashboard-info.store');
    Route::get('/{id}/edit', [DashboardInfoController::class, 'edit'])->name('dashboard-info.edit');
    Route::put('/{id}',      [DashboardInfoController::class, 'update'])->name('dashboard-info.update');
    Route::delete('/{id}',   [DashboardInfoController::class, 'destroy'])->name('dashboard-info.destroy');
});

});
