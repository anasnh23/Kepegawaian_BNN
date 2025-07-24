@extends('layouts.template')
@section('title', 'Data Presensi Pegawai')

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    @if(isset($breadcrumb))
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0 mb-2">
            @foreach($breadcrumb->list as $key => $label)
                @if($key == count($breadcrumb->list) - 1)
                    <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
                @else
                    <li class="breadcrumb-item">{{ $label }}</li>
                @endif
            @endforeach
        </ol>
    </nav>
    @endif

    <h4 class="mb-3">{{ $breadcrumb->title ?? 'Data Presensi Pegawai' }}</h4>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <strong>Data Presensi</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped table-hover" id="datatable">
                <thead class="thead-dark">
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
