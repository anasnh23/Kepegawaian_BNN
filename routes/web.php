<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Tambahkan ini untuk Auth::check() di route root
use App\Http\Controllers\WelcomeController;
<<<<<<< HEAD
use App\Http\Controllers\PegawaiController;

Route::get('/', [WelcomeController::class, 'index']);
Route::resource('/pegawai', PegawaiController::class);
=======
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route root: Redirect berdasarkan status autentikasi
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

// Routes untuk autentikasi (tidak perlu middleware auth untuk login)
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'postlogin']);

// Route logout (dilindungi middleware auth)
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Group routes yang dilindungi middleware auth
Route::middleware(['auth'])->group(function () {
    // Dashboard (gunakan ini sebagai landing setelah login)
    Route::get('/dashboard', [WelcomeController::class, 'index'])->name('dashboard');
    
    // Welcome (jika diperlukan, bisa digabung dengan dashboard jika sama)
    Route::get('/welcome', [WelcomeController::class, 'index']);
    
    // Resource routes untuk User (menangani CRUD: index, create, store, show, edit, update, destroy)
    // Catatan: Route::resource otomatis membuat route seperti /user, /user/create, dll.
    Route::resource('user', UserController::class);
});
>>>>>>> 4d20b1604450ebb21361a61d0e27af0f9925b249
