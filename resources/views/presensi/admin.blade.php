@extends('layouts.template')
@section('title', 'Data Presensi Pegawai')

@section('content')
<div class="container-fluid">



    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Data Presensi</h5>
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
                    @foreach($data as $i => $row)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $row->user->nama ?? '-' }}</td>
                        <td>{{ $row->tanggal }}</td>
                        <td>{{ $row->jam_masuk ?? '-' }}</td>
                        <td>
                            @if($row->foto_masuk)
                                <a href="{{ asset('storage/presensi/'.$row->foto_masuk) }}" target="_blank">
                                    <img src="{{ asset('storage/presensi/'.$row->foto_masuk) }}" width="50" class="img-thumbnail">
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $row->jam_pulang ?? '-' }}</td>
                        <td>
                            @if($row->foto_pulang)
                                <a href="{{ asset('storage/presensi/'.$row->foto_pulang) }}" target="_blank">
                                    <img src="{{ asset('storage/presensi/'.$row->foto_pulang) }}" width="50" class="img-thumbnail">
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- DataTables --}}
@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#datatable').DataTable();
    });
</script>
@endpush

@endsection
