<?php
namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) { // jika sudah login, maka redirect ke halaman home 
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal: ' . $validator->errors()->first()
                ]);
            }
            return redirect('/login')->with('error', 'Validasi gagal: ' . $validator->errors()->first());
        }

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            session([
                'profile_img_path' => Auth::user()->foto,
                'id_user' => Auth::user()->id_user
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Login Berhasil',
                    'redirect' => url('/dashboard')
                ]);
            }
            return redirect('/dashboard')->with('success', 'Login Berhasil');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => false,
                'message' => 'Login Gagal: Username atau password salah'
            ]);
        }

        return redirect('/login')->with('error', 'Login Gagal: Username atau password salah');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Logout Berhasil');
    }
}