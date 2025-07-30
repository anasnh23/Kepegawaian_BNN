<?php

namespace App\Http\Controllers;

use App\Models\RiwayatGajiModel; // Ubah ke nama class yang benar (asumsi class di file RiwayatGajiModel.php adalah RiwayatGajiModel)
use App\Models\MUser; // Import model MUser untuk relasi dan select
use Illuminate\Http\Request;

class RiwayatGajiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $riwayatGajis = RiwayatGajiModel::with('user')->get(); // Eager load relasi user untuk efisiensi
        return view('riwayat_gaji.index', compact('riwayatGajis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = MUser::all(); // Ambil data users untuk select
        return view('riwayat_gaji.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation to match model's fillable fields
        $request->validate([
            'id_user' => 'required|exists:m_user,id_user', 
            'tanggal_berlaku' => 'required|date',
            'gaji_pokok' => 'required|integer',
            'keterangan' => 'nullable|string',
        ]);

        RiwayatGajiModel::create($request->all()); // Use RiwayatGajiModel

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Riwayat Gaji berhasil ditambahkan.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(RiwayatGajiModel $riwayatGaji) // Type-hint the correct model
    {
        $riwayatGaji->load('user'); // Load relasi jika diperlukan
        return view('riwayat_gaji.show', compact('riwayatGaji'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiwayatGajiModel $riwayatGaji) // Type-hint the correct model
    {
        $users = MUser::all(); // Ambil data users untuk select
        return view('riwayat_gaji.edit', compact('riwayatGaji', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiwayatGajiModel $riwayatGaji) // Type-hint the correct model
    {
        // Validation to match model's fillable fields
        $request->validate([
            'id_user' => 'required|exists:m_users,id_user',
            'tanggal_berlaku' => 'required|date',
            'gaji_pokok' => 'required|integer',
            'keterangan' => 'nullable|string',
        ]);

        $riwayatGaji->update($request->all());

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Riwayat Gaji berhasil diperbarui.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiwayatGajiModel $riwayatGaji) // Type-hint the correct model
    {
        $riwayatGaji->delete();

        // Kembalikan response JSON untuk AJAX
        return response()->json(['success' => 'Riwayat Gaji berhasil dihapus.']);
    }
}
