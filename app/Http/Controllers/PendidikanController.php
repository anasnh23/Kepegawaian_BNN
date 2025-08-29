<?php

namespace App\Http\Controllers;

use App\Models\PendidikanModel;
use App\Models\MUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PendidikanController extends Controller
{
   
    public function index()
    {
        $user = Auth::user();

        if ($user->id_level == 1) {
            // Admin: Tampilkan semua data
            $pendidikans = PendidikanModel::with('user')->get();
            $breadcrumb = (object)[
                'title' => 'Pendidikan Semua Pegawai',
                'list' => ['Dashboard', 'Kepegawaian', 'Pendidikan']
            ];
        } else {
            // Non-admin: Tampilkan hanya milik sendiri
            $pendidikans = PendidikanModel::with('user')->where('id_user', $user->id_user)->get();
            $breadcrumb = (object)[
                'title' => 'Pendidikan Saya',
                'list' => ['Dashboard', 'Kepegawaian', 'Pendidikan']
            ];
        }

        $activeMenu = 'pendidikan';

        return view('pendidikan.index', compact('pendidikans', 'breadcrumb', 'activeMenu'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh create
        }
        $users = MUser::all(); // Ambil data users untuk select
        return view('pendidikan.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh store
        }
        // Validation to match model's fillable fields
        $request->validate([
            'id_user' => 'required|exists:m_user,id_user',
            'jenis_pendidikan' => 'required|string',
            'tahun_kelulusan' => 'required|integer',
        ]);

        PendidikanModel::create($request->all());

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Pendidikan berhasil ditambahkan.']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pendidikan = PendidikanModel::find($id);

        if (!$pendidikan) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Cek akses: Non-admin hanya bisa lihat milik sendiri
        if (Auth::user()->id_level != 1 && $pendidikan->id_user != Auth::user()->id_user) {
            abort(403);
        }

        $pendidikan->load('user'); // Load relasi jika diperlukan
        return view('pendidikan.show', compact('pendidikan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh edit
        }
        $pendidikan = PendidikanModel::find($id);

        if (!$pendidikan) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $users = MUser::all(); // Ambil data users untuk select
        return view('pendidikan.edit', compact('pendidikan', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh update
        }
        $pendidikan = PendidikanModel::find($id);

        if (!$pendidikan) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Validation to match model's fillable fields
        $request->validate([
            'id_user' => 'required|exists:m_user,id_user',
            'jenis_pendidikan' => 'required|string',
            'tahun_kelulusan' => 'required|integer',
        ]);

        $pendidikan->update($request->all());

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Pendidikan berhasil diperbarui.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh delete
        }
        $pendidikan = PendidikanModel::find($id);

        if (!$pendidikan) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $pendidikan->delete();

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Pendidikan berhasil dihapus.']);
    }
}
