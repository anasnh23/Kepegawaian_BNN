<?php

namespace App\Http\Controllers;

use App\Models\RefGolonganPangkat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefGolonganPangkatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Karena ini data referensi global, tampilkan semua untuk admin dan non-admin
        // Namun, sesuaikan breadcrumb jika diperlukan
        $refGolonganPangkats = RefGolonganPangkat::all();
        $breadcrumb = (object)[
            'title' => 'Referensi Golongan Pangkat',
            'list' => ['Dashboard', 'Referensi', 'Golongan Pangkat']
        ];

        $activeMenu = 'ref_golongan_pangkat';

        return view('ref_golongan_pangkat.index', compact('refGolonganPangkats', 'breadcrumb', 'activeMenu'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh create
        }
        return view('ref_golongan_pangkat.create');
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
            'golongan_pangkat' => 'required|string|max:255',
            'gaji_pokok' => 'required|numeric',
            'masa_kerja_min' => 'required|integer',
            'masa_kerja_maks' => 'required|integer',
            'keterangan' => 'nullable|string',
        ]);

        RefGolonganPangkat::create($request->all());

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Referensi Golongan Pangkat berhasil ditambahkan.']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $refGolonganPangkat = RefGolonganPangkat::find($id);

        if (!$refGolonganPangkat) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Data referensi global, boleh dilihat semua user
        return view('ref_golongan_pangkat.show', compact('refGolonganPangkat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh edit
        }
        $refGolonganPangkat = RefGolonganPangkat::find($id);

        if (!$refGolonganPangkat) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return view('ref_golongan_pangkat.edit', compact('refGolonganPangkat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh update
        }
        $refGolonganPangkat = RefGolonganPangkat::find($id);

        if (!$refGolonganPangkat) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Validation to match model's fillable fields
        $request->validate([
            'golongan_pangkat' => 'required|string|max:255',
            'gaji_pokok' => 'required|numeric',
            'masa_kerja_min' => 'required|integer',
            'masa_kerja_maks' => 'required|integer',
            'keterangan' => 'nullable|string',
        ]);

        $refGolonganPangkat->update($request->all());

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Referensi Golongan Pangkat berhasil diperbarui.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (Auth::user()->id_level != 1) {
            abort(403); // Hanya admin boleh delete
        }
        $refGolonganPangkat = RefGolonganPangkat::find($id);

        if (!$refGolonganPangkat) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $refGolonganPangkat->delete();

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Referensi Golongan Pangkat berhasil dihapus.']);
    }
}
