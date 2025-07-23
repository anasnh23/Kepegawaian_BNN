<?php

namespace App\Http\Controllers;

use App\Models\Pangkat;
use App\Models\MUser;
use App\Models\RefGolonganPangkat;
use Illuminate\Http\Request;

class PangkatController extends Controller
{
    public function index()
    {
        $pangkat = Pangkat::with(['user', 'refGolongan'])->get();

        $breadcrumb = (object)[
            'title' => 'Data Pangkat Pegawai',
            'list' => ['Dashboard', 'Kepegawaian', 'Pangkat']
        ];

        return view('pangkat.index', compact('pangkat', 'breadcrumb'))->with('activeMenu', 'pangkat');
    }

    public function create()
    {
        $users = MUser::all();
        $golongan = RefGolonganPangkat::all();

        return view('pangkat.create', compact('users', 'golongan'))->with('activeMenu', 'pangkat');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required',
            'id_ref_pangkat' => 'required',
            'golongan_pangkat' => 'required',
        ]);

        Pangkat::create($request->all());

        return redirect('/pangkat')->with('success', 'Data pangkat berhasil ditambahkan.');
    }

    // Implementasi edit, update, delete bisa disusulkan
}
