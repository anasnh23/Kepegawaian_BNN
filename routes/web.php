<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardPegawaiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PangkatController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PresensiController; 
use App\Http\Controllers\CutiController; 
use App\Http\Controllers\AdminCutiController;
use App\Http\Controllers\PresensiAdminController;

// Akses root langsung redirect ke login atau dashboard
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

// Dashboard role based
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->id_level == 1) {
        return redirect('/dashboard-admin');
    } elseif ($user->id_level == 2) {
        return redirect('/dashboard-pegawai');
    }
    abort(403);
})->name('dashboard');

// Login
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'postlogin']);

// Logout
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Route yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {

    // Dashboard admin & pegawai
    Route::get('/dashboard-admin', [DashboardAdminController::class, 'index'])->name('dashboard.admin');
    Route::get('/dashboard-pegawai', [DashboardPegawaiController::class, 'index'])->name('dashboard.pegawai');

    // Manajemen Pegawai & Pangkat
    Route::resource('/pegawai', PegawaiController::class);
    Route::resource('/pangkat', PangkatController::class);

    // Profil
    Route::get('/profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::post('/profil', [ProfilController::class, 'update'])->name('profil.update');

    // Presensi Pegawai
    Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');
    Route::post('/presensi', [PresensiController::class, 'store'])->name('presensi.store');

    // Presensi Admin
    Route::get('/presensi-admin', [PresensiAdminController::class, 'index'])->name('presensi.admin');

    // Cuti untuk Pegawai
    Route::get('/cutipegawai', [CutiController::class, 'index'])->name('cuti.pegawai');
    Route::post('/cuti/store', [CutiController::class, 'store']);
    Route::get('/riwayat-cuti', [CutiController::class, 'riwayat']);
    Route::get('/cuti/cetak/{id}', [CutiController::class, 'cetak'])->name('cuti.cetak');

    // Manajemen Cuti Admin
    Route::get('/cutiadmin', [AdminCutiController::class, 'index']);
    Route::post('/cuti/set-status', [AdminCutiController::class, 'setStatus']);
    Route::get('/cuti/edit/{id}', [AdminCutiController::class, 'edit']);
    Route::put('/cuti/update-status/{id}', [AdminCutiController::class, 'updateStatus']);

});
