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
                'list'  => ['Dashboard', 'Kepegawaian', 'Riwayat Gaji']
            ];
        } else {
            // Non-admin: Tampilkan hanya milik sendiri
            $riwayatGajis = RiwayatGajiModel::with('user')->where('id_user', $user->id_user)->get();
            $breadcrumb = (object)[
                'title' => 'Riwayat Gaji Saya',
                'list'  => ['Dashboard', 'Kepegawaian', 'Riwayat Gaji']
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
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh create
        }

        $users = MUser::all(); // Untuk select user di form
        return view('riwayat_gaji.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh store
        }

        // Validasi sesuai field fillable pada model
        $request->validate([
            'id_user'          => 'required|exists:m_user,id_user',
            'tanggal_berlaku'  => 'required|date',
            'gaji_pokok'       => 'required|numeric',
            'keterangan'       => 'nullable|string'
        ]);

        RiwayatGajiModel::create($request->only([
            'id_user', 'tanggal_berlaku', 'gaji_pokok', 'keterangan'
        ]));

        // Response JSON untuk AJAX
        return response()->json(['success' => 'Riwayat gaji berhasil ditambahkan.']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $riwayatGaji = RiwayatGajiModel::find($id);

        if (!$riwayatGaji) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Non-admin hanya boleh melihat miliknya sendiri
        if (Auth::user()->id_level != 1 && $riwayatGaji->id_user != Auth::user()->id_user) {
            abort(403);
        }

        $riwayatGaji->load('user'); // muat relasi jika diperlukan

        return view('riwayat_gaji.show', compact('riwayatGaji'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh edit
        }

        $riwayatGaji = RiwayatGajiModel::find($id);

        if (!$riwayatGaji) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $users = MUser::all(); // Untuk select user di form
        return view('riwayat_gaji.edit', compact('riwayatGaji', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh update
        }

        $riwayatGaji = RiwayatGajiModel::find($id);

        if (!$riwayatGaji) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Validasi sesuai field fillable pada model
        $request->validate([
            'id_user'          => 'required|exists:m_user,id_user',
            'tanggal_berlaku'  => 'required|date',
            'gaji_pokok'       => 'required|numeric',
            'keterangan'       => 'nullable|string'
        ]);

        $riwayatGaji->update($request->only([
            'id_user', 'tanggal_berlaku', 'gaji_pokok', 'keterangan'
        ]));

        // Response JSON untuk AJAX
        return response()->json(['success' => 'Riwayat gaji berhasil diperbarui.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh delete
        }

        $riwayatGaji = RiwayatGajiModel::find($id);

        if (!$riwayatGaji) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $riwayatGaji->delete();

        // Response JSON untuk AJAX
        return response()->json(['success' => 'Riwayat gaji berhasil dihapus.']);
    }
}
