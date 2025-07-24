<?php

namespace App\Http\Controllers;

   use App\Models\JabatanModel; // Correct model import
   use Illuminate\Http\Request;

   class JabatanController extends Controller
   {
       /**
        * Display a listing of the resource.
        */
       public function index()
       {
           $jabatans = JabatanModel::all(); // Use JabatanModel
           return view('jabatan.index', compact('jabatans'));
       }

       /**
        * Show the form for creating a new resource.
        */
       public function create()
       {
           return view('jabatan.create');
       }

       /**
        * Store a newly created resource in storage.
        */
       public function store(Request $request)
       {
           // Update validation to match model's fillable fields
           $request->validate([
               'id_user' => 'required|exists:users,id', // Assuming 'users' table exists
               'id_ref_jabatan' => 'required|exists:ref_jabatan,id_ref_jabatan', // Adjust based on your ref table
               'tahun_kelulusan' => 'nullable|integer',
               'tmt' => 'nullable|date',
           ]);

           JabatanModel::create($request->all()); // Use JabatanModel

           return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil ditambahkan.');
       }

       /**
        * Display the specified resource.
        */
       public function show(JabatanModel $jabatan) // Type-hint the correct model
       {
           return view('jabatan.show', compact('jabatan'));
       }

       /**
        * Show the form for editing the specified resource.
        */
       public function edit(JabatanModel $jabatan) // Type-hint the correct model
       {
           return view('jabatan.edit', compact('jabatan'));
       }

       /**
        * Update the specified resource in storage.
        */
       public function update(Request $request, JabatanModel $jabatan) // Type-hint the correct model
       {
           // Update validation to match model's fillable fields
           $request->validate([
               'id_user' => 'required|exists:users,id',
               'id_ref_jabatan' => 'required|exists:ref_jabatan,id_ref_jabatan',
               'tahun_kelulusan' => 'nullable|integer',
               'tmt' => 'nullable|date',
           ]);

           $jabatan->update($request->all());

           return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil diperbarui.');
       }

       /**
        * Remove the specified resource from storage.
        */
       public function destroy(JabatanModel $jabatan) // Type-hint the correct model
       {
           $jabatan->delete();

           return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil dihapus.');
       }
   }
   