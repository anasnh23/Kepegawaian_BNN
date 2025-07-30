<?php

namespace App\Http\Controllers;

use App\Models\RefJabatanModel; // Import model yang benar
use Illuminate\Http\Request;

class RefJabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index()
{
    $refJabatans = RefJabatanModel::all();

    $breadcrumb = (object)[
        'title' => 'Data Jabatan',
        'list' => ['Dashboard', 'Kepegawaian', 'Data Jabatan']
    ];

    $activeMenu = 'ref_jabatan';

    return view('ref_jabatan.index', compact('refJabatans', 'breadcrumb', 'activeMenu'));
}



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ref_jabatan.create'); // Menampilkan form untuk menambah jabatan
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'eselon' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:255',
        ]);

        RefJabatanModel::create($request->all()); // Menyimpan data jabatan baru

        return redirect()->route('ref_jabatan.index')->with('success', 'Jabatan referensi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RefJabatanModel $refJabatan)
    {
        return view('ref_jabatan.show', compact('refJabatan')); // Menampilkan detail jabatan
    }

    /**
     * Show the form for editing the specified resource.
     */
public function edit($id)
{
    $refJabatan = RefJabatanModel::findOrFail($id);
    return view('ref_jabatan.edit', compact('refJabatan'));
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RefJabatanModel $refJabatan)
    {
        // Validasi input
        $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'eselon' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $refJabatan->update($request->all()); // Memperbarui data jabatan

        return redirect()->route('ref_jabatan.index')->with('success', 'Jabatan referensi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RefJabatanModel $refJabatan)
    {
        $refJabatan->delete(); // Menghapus jabatan

        return redirect()->route('ref_jabatan.index')->with('success', 'Jabatan referensi berhasil dihapus.');
    }
}
