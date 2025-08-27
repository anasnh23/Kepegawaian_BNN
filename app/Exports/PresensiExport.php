<?php

namespace App\Exports;

use App\Models\PresensiModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class PresensiExport implements FromCollection, WithHeadings
{
    protected $tipe;
    protected $tanggal;

    public function __construct($tipe, $tanggal)
    {
        $this->tipe = $tipe;
        $this->tanggal = $tanggal;
    }

    public function collection()
    {
        $query = PresensiModel::with('user')->select('id_user', 'tanggal', 'jam_masuk', 'jam_pulang', 'status');

        if ($this->tipe === 'harian') {
            $query->whereDate('tanggal', $this->tanggal);
        } elseif ($this->tipe === 'mingguan') {
            $start = Carbon::parse($this->tanggal)->startOfWeek(Carbon::MONDAY);
            $end = $start->copy()->addDays(4); // Senin sampai Jumat
            $query->whereBetween('tanggal', [$start, $end]);
        } elseif ($this->tipe === 'bulanan') {
            $query->whereMonth('tanggal', Carbon::parse($this->tanggal)->month)
                  ->whereYear('tanggal', Carbon::parse($this->tanggal)->year);
        }

        $data = $query->get()->map(function ($item) {
            return [
                'Nama' => $item->user->nama ?? '-',
                'Tanggal' => $item->tanggal,
                'Jam Masuk' => $item->jam_masuk ?? '-',
                'Jam Pulang' => $item->jam_pulang ?? '-',
                'Status' => ucfirst($item->status),
            ];
        });

        return new Collection($data);
    }

    public function headings(): array
    {
        return ['Nama', 'Tanggal', 'Jam Masuk', 'Jam Pulang', 'Status'];
    }
}
