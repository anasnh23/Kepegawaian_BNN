<?php

namespace App\Http\Controllers;

use App\Models\Pangkat;
use App\Models\MUser;
use App\Models\RefGolonganPangkat;
use App\Models\JabatanModel; // Import model dengan nama yang benar

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PangkatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->id_level == 1) {
            // Admin: Tampilkan semua data
            $pangkats = Pangkat::with(['user', 'refPangkat', 'jabatanModel'])->get(); // Pastikan nama relasi match method di model
            $breadcrumb = (object)[
                'title' => 'Pangkat Semua Pegawai',
                'list' => ['Dashboard', 'Kepegawaian', 'Pangkat']
            ];
        } else {
            // Non-admin: Tampilkan hanya milik sendiri
            $pangkats = Pangkat::with(['user', 'refPangkat', 'jabatanModel'])->where('id_user', $user->id_user)->get(); // Pastikan nama relasi match
            $breadcrumb = (object)[
                'title' => 'Pangkat Saya',
                'list' => ['Dashboard', 'Kepegawaian', 'Pangkat']
            ];
        }

        $activeMenu = 'pangkat';

        return view('pangkat.index', compact('pangkats', 'breadcrumb', 'activeMenu'));
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
        $refPangkats = RefGolonganPangkat::all(); // Ambil data ref pangkat untuk select
        $jabatans = JabatanModel::all(); // Gunakan nama model yang benar
        return view('pangkat.create', compact('users', 'refPangkats', 'jabatans'));
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
            'id_jabatan' => 'nullable|exists:jabatan,id_jabatan', // Sesuaikan nama tabel jabatan
            'id_ref_pangkat' => 'nullable|exists:ref_golongan_pangkat,id_ref_pangkat',
            'golongan_pangkat' => 'nullable|string|max:10',
        ]);

        Pangkat::create($request->all());

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Pangkat berhasil ditambahkan.']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pangkat = Pangkat::find($id);

        if (!$pangkat) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Cek akses: Non-admin hanya bisa lihat milik sendiri
        if (Auth::user()->id_level != 1 && $pangkat->id_user != Auth::user()->id_user) {
            abort(403);
        }

        $pangkat->load(['user', 'refPangkat', 'jabatanModel']); // Pastikan nama relasi match method di model
        return view('pangkat.show', compact('pangkat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh edit
        }
        $pangkat = Pangkat::find($id);

        if (!$pangkat) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $users = MUser::all(); // Ambil data users untuk select
        $refPangkats = RefGolonganPangkat::all(); // Ambil data ref pangkat untuk select
        $jabatans = JabatanModel::all(); // Gunakan nama model yang benar
        return view('pangkat.edit', compact('pangkat', 'users', 'refPangkats', 'jabatans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh update
        }
        $pangkat = Pangkat::find($id);

        if (!$pangkat) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Validation to match model's fillable fields
        $request->validate([
            'id_user' => 'required|exists:m_user,id_user',
            'id_jabatan' => 'nullable|exists:jabatan,id_jabatan', // Sesuaikan nama tabel jabatan
            'id_ref_pangkat' => 'nullable|exists:ref_golongan_pangkat,id_ref_pangkat',
            'golongan_pangkat' => 'nullable|string|max:10',
        ]);

        $pangkat->update($request->all());

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Pangkat berhasil diperbarui.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh delete
        }
        $pangkat = Pangkat::find($id);

        if (!$pangkat) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $pangkat->delete();

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Pangkat berhasil dihapus.']);
    }
}
