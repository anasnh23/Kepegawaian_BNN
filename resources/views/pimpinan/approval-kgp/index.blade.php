@extends('layouts.template')
@section('title', 'Approval Kenaikan Gaji Berkala')

@section('content')
<style>
  :root{
    --bnn-navy:#003366; --bnn-navy-2:#0b2f5e; --bnn-blue:#144272;
    --bnn-gold:#f0ad4e; --bnn-gold-2:#d89a2b;
    --soft:#f5f8fc; --line:#e6edf6; --muted:#6b7a8c;
    --green:#28a745; --yellow:#ffc107; --red:#dc3545;
  }

  .bnn-hero{
    background:linear-gradient(135deg,var(--bnn-navy),#012148 60%,var(--bnn-navy-2));
    color:#fff;border-radius:16px;padding:18px 20px;position:relative;overflow:hidden;
    box-shadow:0 14px 36px rgba(0,33,72,.22);
  }
  .bnn-hero::after{
    content:"";position:absolute;right:-60px;top:-60px;width:200px;height:200px;opacity:.08;
    background:url('{{ asset('images/bnn-watermark.svg') }}') no-repeat center/contain;
  }
  .bnn-card{border:1px solid var(--line);border-radius:14px;box-shadow:0 8px 24px rgba(16,24,40,.06);overflow:hidden;}
  .bnn-card .card-header{background:var(--bnn-navy);color:#fff;font-weight:700;}

  .table thead th{background:#0f1f39;color:#eaf2ff;border-color:#0f1f39;font-weight:700;}
  .table-hover tbody tr:hover{background:#fbfdff;}
  .badge-status{font-weight:700;border-radius:999px;padding:.35rem .7rem;}
  .st-acc{background:#e7f6ec;color:#146c2e;border:1px solid #bfe5c8;}
  .st-pend{background:#fff7e6;color:#7a4d00;border:1px solid #ffe0ad;}
  .st-rej{background:#fdeaea;color:#842029;border:1px solid #f8c2c7;}
</style>

<div class="container-fluid">

  {{-- Header --}}
  <div class="bnn-hero mb-3 d-flex align-items-center justify-content-between">
    <div>
      <h4><i class="fas fa-money-check-alt mr-2"></i> Approval Kenaikan Gaji Berkala</h4>
      <div class="sub">Setujui, tolak, dan kelola usulan KGP pegawai</div>
    </div>
    <div>
      <a href="{{ route('dashboard.pimpinan') }}" class="btn btn-warning btn-sm">
        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
      </a>
    </div>
  </div>

  {{-- Tabel --}}
  <div class="card bnn-card">
    <div class="card-header"><i class="fas fa-list mr-2"></i> Daftar Usulan KGP</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover m-0 text-sm">
          <thead class="text-center">
            <tr>
              <th>No</th>
              <th>Nama Pegawai</th>
              <th>Tahun KGP</th>
              <th>TMT</th>
              <th>Status</th>
              <th style="width:180px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($kgp as $row)
              @php
                $status = $row->status ?? 'Menunggu';
                $badge = $status === 'Disetujui' ? 'st-acc' : ($status === 'Ditolak' ? 'st-rej' : 'st-pend');
              @endphp
              <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $row->pegawai->nama ?? '-' }}</td>
                <td class="text-center">{{ $row->tahun_kgp }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row->tmt)->format('d-m-Y') }}</td>
                <td class="text-center">
                  <span class="badge-status {{ $badge }}">{{ $status }}</span>
                </td>
                <td class="text-center">
                  @if($status === 'Menunggu')
                    <form action="{{ route('approval.kgb.approve',$row->id_kgp) }}" method="POST" style="display:inline-block">
                      @csrf
                      <button class="btn btn-success btn-sm" onclick="return confirm('Setujui usulan KGP ini?')">
                        <i class="fas fa-check"></i> Setujui
                      </button>
                    </form>
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalTolak{{ $row->id_kgp }}">
                      <i class="fas fa-times"></i> Tolak
                    </button>

                    {{-- Modal Tolak --}}
                    <div class="modal fade" id="modalTolak{{ $row->id_kgp }}">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="fas fa-times-circle"></i> Tolak KGP</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                          </div>
                          <form action="{{ route('approval.kgb.reject',$row->id_kgp) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                              <label>Catatan Penolakan</label>
                              <textarea name="catatan" class="form-control" rows="3" placeholder="Alasan penolakan..."></textarea>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                              <button type="submit" class="btn btn-danger">Tolak</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  @else
                    <em>-</em>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted">Belum ada usulan KGP</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
