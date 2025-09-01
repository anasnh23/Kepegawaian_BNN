{{-- resources/views/pendidikan/show.blade.php --}}
<div class="modal-header" style="background-color: #003366; color: white;">
    <h5 class="modal-title"><i class="fas fa-eye mr-2"></i>Detail Pendidikan</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body" style="background-color: #f8f9fa;">
    <div class="row">

        {{-- Informasi Detail Pendidikan --}}
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-sm">
                <tbody>
                    <tr>
                        <th class="bg-light">Nama Pegawai</th>
                        <td>{{ $pendidikan->user->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Jenis Pendidikan</th>
                        <td>{{ $pendidikan->jenis_pendidikan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tahun Kelulusan</th>
                        <td>{{ $pendidikan->tahun_kelulusan ?? '-' }}</td>
                    </tr>
                </tbody>
    