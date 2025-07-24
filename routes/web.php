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

// Authenticated routes
Route::middleware(['auth'])->group(function () {
Route::get('/dashboard-admin', [DashboardAdminController::class, 'index'])->name('dashboard.admin');
Route::get('/dashboard-pegawai', [DashboardPegawaiController::class, 'index'])->name('dashboard.pegawai');


    Route::resource('/pegawai', PegawaiController::class);
    Route::resource('/pangkat', PangkatController::class);

    Route::get('/profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::post('/profil', [ProfilController::class, 'update'])->name('profil.update');

    // ✅ ROUTE PRESENSI
    Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');
    Route::post('/presensi', [PresensiController::class, 'store'])->name('presensi.store');

    // Admin - Data Presensi
    Route::get('/presensi-admin', [PresensiAdminController::class, 'index'])->name('presensi.admin');

   Route::get('/cutipegawai', [CutiController::class, 'index'])->name('cuti.pegawai');
    Route::post('/cuti/store', [CutiController::class, 'store']);
    Route::get('/riwayat-cuti', [CutiController::class, 'riwayat']);
    Route::get('/cuti/cetak/{id}', [CutiController::class, 'cetak'])->name('cuti.cetak');


    Route::get('/cutiadmin', [AdminCutiController::class, 'index']);
    Route::post('/cuti/set-status', [AdminCutiController::class, 'setStatus']);
    Route::get('/cuti/edit/{id}', [AdminCutiController::class, 'edit']);
    Route::put('/cuti/update-status/{id}', [AdminCutiController::class, 'updateStatus']);



});
