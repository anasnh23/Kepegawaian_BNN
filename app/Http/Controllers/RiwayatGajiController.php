<?php

namespace App\Http\Controllers;

use App\Models\RiwayatGajiModel;
use App\Models\MUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatGajiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->id_level == 1) {
            // Admin: Tampilkan semua data
            $riwayatGajis = RiwayatGajiModel::with('user')->get();
            $breadcrumb = (object)[
                'title' => 'Riwayat Gaji Semua Pegawai',
                'list' => ['Dashboard', 'Kepegawaian', 'Riwayat Gaji']
            ];
        } else {
            // Non-admin: Tampilkan hanya milik sendiri
            $riwayatGajis = RiwayatGajiModel::with('user')->where('id_user', $user->id_user)->get();
            $breadcrumb = (object)[
                'title' => 'Riwayat Gaji Saya',
                'list' => ['Dashboard', 'Kepegawaian', 'Riwayat Gaji']
            ];
        }

        $activeMenu = 'riwayat_gaji';

        return view('riwayat_gaji.index', compact('riwayatGajis', 'breadcrumb', 'activeMenu'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->id_level == 1) {
            // Admin: Bisa pilih user mana saja
            $users = MUser::all();
        } else {
            // Non-admin: Hanya bisa untuk diri sendiri
            $users = MUser::where('id_user', $user->id_user)->get();
        }
        
        return view('riwayat_gaji.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Validation to match model's fillable fields
        $request->validate([
            'id_user' => 'required|exists:m_user,id_user', 
            'tanggal_berlaku' => 'required|date',
            'gaji_pokok' => 'required|integer',
            'keterangan' => 'nullable|string',
        ]);

        // Jika bukan admin, pastikan hanya bisa menambah data untuk diri sendiri
        if ($user->id_level != 1 && $request->id_user != $user->id_user) {
            return response()->json(['error' => 'Anda tidak memiliki akses untuk menambah data user lain.'], 403);
        }

        RiwayatGajiModel::create($request->all());

        return response()->json(['success' => 'Riwayat Gaji berhasil ditambahkan.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(RiwayatGajiModel $riwayatGaji)
    {
        $user = Auth::user();
        
        // Jika bukan admin, pastikan hanya bisa melihat data milik sendiri
        if ($user->id_level != 1 && $riwayatGaji->id_user != $user->id_user) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }
        
        $riwayatGaji->load('user');
        return view('riwayat_gaji.show', compact('riwayatGaji'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiwayatGajiModel $riwayatGaji)
    {
        $user = Auth::user();
        
        // Jika bukan admin, pastikan hanya bisa edit data milik sendiri
        if ($user->id_level != 1 && $riwayatGaji->id_user != $user->id_user) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }
        
        if ($user->id_level == 1) {
            // Admin: Bisa pilih user mana saja
            $users = MUser::all();
        } else {
            // Non-admin: Hanya bisa untuk diri sendiri
            $users = MUser::where('id_user', $user->id_user)->get();
        }
        
        return view('riwayat_gaji.edit', compact('riwayatGaji', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiwayatGajiModel $riwayatGaji)
    {
        $user = Auth::user();
        
        // Jika bukan admin, pastikan hanya bisa update data milik sendiri
        if ($user->id_level != 1 && $riwayatGaji->id_user != $user->id_user) {
            return response()->json(['error' => 'Anda tidak memiliki akses untuk mengupdate data ini.'], 403);
        }
        
        // Validation to match model's fillable fields
        $request->validate([
            'id_user' => 'required|exists:m_user,id_user', // Perbaiki dari m_users ke m_user
            'tanggal_berlaku' => 'required|date',
            'gaji_pokok' => 'required|integer',
            'keterangan' => 'nullable|string',
        ]);

        // Jika bukan admin, pastikan tidak bisa mengubah id_user ke user lain
        if ($user->id_level != 1 && $request->id_user != $user->id_user) {
            return response()->json(['error' => 'Anda tidak dapat mengubah data untuk user lain.'], 403);
        }

        $riwayatGaji->update($request->all());

        return response()->json(['success' => 'Riwayat Gaji berhasil diperbarui.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiwayatGajiModel $riwayatGaji)
    {
        $user = Auth::user();
        
        // Jika bukan admin, pastikan hanya bisa hapus data milik sendiri
        if ($user->id_level != 1 && $riwayatGaji->id_user != $user->id_user) {
            return response()->json(['error' => 'Anda tidak memiliki akses untuk menghapus data ini.'], 403);
        }
        
        $riwayatGaji->delete();

        return response()->json(['success' => 'Riwayat Gaji berhasil dihapus.']);
    }
}
