@extends('layouts.template')
@section('title', 'Data Presensi Pegawai')

@section('content')
<div class="container-fluid">

    <!-- Filter dan Export -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form id="formFilterExport" method="GET" action="{{ route('presensi.admin') }}">
                <div class="form-row align-items-end">
                    <div class="col-md-3">
                        <label for="filter">Jenis Filter</label>
                        <select name="filter" id="filter" class="form-control form-control-sm">
                            <option value="harian" {{ request('filter') == 'harian' ? 'selected' : '' }}>Harian</option>
                            <option value="mingguan" {{ request('filter') == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                            <option value="bulanan" {{ request('filter') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="tanggalField">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control form-control-sm" value="{{ request('tanggal') }}">
                    </div>
                    <div class="col-md-3" id="mingguField" style="display:none;">
                        <label for="minggu">Tanggal Mulai (Mingguan)</label>
                        <input type="date" name="minggu" id="minggu" class="form-control form-control-sm" value="{{ request('minggu') }}">
                    </div>
                    <div class="col-md-3" id="bulanField" style="display:none;">
                        <label for="bulan">Bulan</label>
                        <input type="month" name="bulan" id="bulan" class="form-control form-control-sm" value="{{ request('bulan') }}">
                    </div>

                    <div class="col-md-3 mt-3 mt-md-0 d-flex">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-file-export fa-sm"></i> Export
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" type="submit" name="export" value="excel">
                                    <i class="fas fa-file-excel fa-sm text-success mr-2"></i> Export Excel
                                </button>
                                <button class="dropdown-item" type="submit" name="export" value="pdf">
                                    <i class="fas fa-file-pdf fa-sm text-danger mr-2"></i> Export PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Rentang Mingguan -->
    @if(request('filter') === 'mingguan' && request('minggu'))
        @php
            $start = \Carbon\Carbon::parse(request('minggu'))->startOfWeek(\Carbon\Carbon::MONDAY);
            $end = $start->copy()->addDays(4);
        @endphp
        <div class="alert alert-info">
            Menampilkan data dari <strong>{{ $start->translatedFormat('d F Y') }}</strong> sampai <strong>{{ $end->translatedFormat('d F Y') }}</strong>
        </div>
    @endif

    <!-- Data Presensi -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-calendar-check fa-sm mr-2"></i> Data Presensi</h5>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-hover text-sm m-0">
                <thead class="thead-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Foto Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Foto Pulang</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $row)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $row->user->nama ?? '-' }}</td>
                        <td>{{ $row->tanggal }}</td>
                        <td>{{ $row->jam_masuk ?? '-' }}</td>
                        <td>
                            @if($row->foto_masuk)
                                <a href="{{ asset('storage/presensi/'.$row->foto_masuk) }}" target="_blank">
                                    <img src="{{ asset('storage/presensi/'.$row->foto_masuk) }}" width="45" class="img-thumbnail">
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $row->jam_pulang ?? '-' }}</td>
                        <td>
                            @if($row->foto_pulang)
                                <a href="{{ asset('storage/presensi/'.$row->foto_pulang) }}" target="_blank">
                                    <img src="{{ asset('storage/presensi/'.$row->foto_pulang) }}" width="45" class="img-thumbnail">
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge 
                                {{ $row->status == 'hadir' ? 'badge-success' : 
                                   ($row->status == 'terlambat' ? 'badge-warning' : 'badge-danger') }}">
                                {{ ucfirst($row->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted">Tidak ada data presensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        const form = $('#formFilterExport');

        function toggleFields() {
            let val = $('#filter').val();

            // Reset semua field saat filter berubah
            $('#tanggal, #minggu, #bulan').val('');
            $('#tanggalField, #mingguField, #bulanField').hide();

            if (val === 'harian') {
                $('#tanggalField').show();
            } else if (val === 'mingguan') {
                $('#mingguField').show();
            } else if (val === 'bulanan') {
                $('#bulanField').show();
            }
        }

        // Saat jenis filter berubah
        $('#filter').on('change', function () {
            toggleFields();
        });

        // Auto-submit ketika input tanggal berubah sesuai jenis filter aktif
        $('#tanggal, #minggu, #bulan').on('change', function () {
            const filter = $('#filter').val();

            if (filter === 'harian' && $('#tanggal').val()) {
                form.submit();
            } else if (filter === 'mingguan' && $('#minggu').val()) {
                form.submit();
            } else if (filter === 'bulanan' && $('#bulan').val()) {
                form.submit();
            }
        });

        // Inisialisasi saat pertama load
        toggleFields();
    });
</script>
@endpush

@endsection
