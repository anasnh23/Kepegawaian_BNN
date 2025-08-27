<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PresensiModel;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresensiExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class PresensiAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = PresensiModel::with('user')->latest();
        $tipe = $request->filter;
        $tanggal = null;

        // === FILTER DATA PRESENSI ===
        if ($tipe === 'harian' && $request->filled('tanggal')) {
            $tanggal = $request->tanggal;
            $query->whereDate('tanggal', $tanggal);

        } elseif ($tipe === 'mingguan' && $request->filled('minggu')) {
            // Mulai dari hari Senin sampai Jumat
            $start = Carbon::parse($request->minggu)->startOfWeek(Carbon::MONDAY);
            $end = $start->copy()->addDays(4);
            $tanggal = $start->toDateString();
            $query->whereBetween('tanggal', [$start, $end]);

        } elseif ($tipe === 'bulanan' && $request->filled('bulan')) {
            $carbon = Carbon::parse($request->bulan);
            $tanggal = $request->bulan;
            $query->whereMonth('tanggal', $carbon->month)
                  ->whereYear('tanggal', $carbon->year);
        }

        $data = $query->get();

        // === HANDLE EXPORT ===
        if ($request->has('export')) {
            if ($request->export === 'excel') {
                return Excel::download(new PresensiExport($tipe, $tanggal), 'Presensi_Export.xlsx');
            } elseif ($request->export === 'pdf') {
                $pdf = PDF::loadView('presensi.export-pdf', compact('data'))
                         ->setPaper('a4', 'landscape');
                return $pdf->download('Presensi_Export.pdf');
            }
        }

        // === BREADCRUMB DAN TAMPILAN ===
        $breadcrumb = (object)[
            'title' => 'Data Presensi',
            'list' => ['Dashboard', 'Kepegawaian', 'Data Presensi']
        ];

        return view('presensi.admin', compact('data', 'breadcrumb'))
            ->with('activeMenu', 'presensi-admin');
    }
}
