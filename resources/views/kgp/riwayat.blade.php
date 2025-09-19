@extends('layouts.template')
@section('title','Riwayat KGP')

@section('content')
<style>
  :root{
    --bnn-navy:#003366; --bnn-navy-2:#0b2f5e; --soft:#f5f8fc; --line:#e6edf6;
  }
  .bnn-hero{background:linear-gradient(135deg,var(--bnn-navy),#012148 60%,var(--bnn-navy-2));
    color:#fff;border-radius:16px;padding:18px 20px;margin-bottom:14px;box-shadow:0 14px 36px rgba(0,33,72,.22);}
  .badge-bnn{border-radius:999px;font-weight:700;padding:.4rem .6rem}
  .badge-wait{background:#fff7e6;color:#7a4d00;border:1px solid #ffe0ad}
  .badge-ok{background:#e7f6ec;color:#146c2e;border:1px solid #bfe5c8}
  .badge-no{background:#fdeaea;color:#842029;border:1px solid #f8c2c7}
  .card-bnn{border:1px solid var(--line);border-radius:14px;box-shadow:0 8px 24px rgba(16,24,40,.06);overflow:hidden;}
  .card-bnn .card-header{background:var(--bnn-navy);color:#fff;font-weight:700;}
</style>

<div class="container-fluid">

  <div class="bnn-hero d-flex align-items-center justify-content-between">
    <div>
      <h4 class="mb-1"><i class="fas fa-list mr-2"></i>Riwayat Pengajuan KGP</h4>
      <small class="text-light">Seluruh pengajuan Anda</small>
    </div>
    <a href="{{ route('kgp.pengajuan') }}" class="btn btn-sm btn-outline-light">
      <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
  </div>

  <div class="card card-bnn">
    <div class="card-header">Daftar Pengajuan</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead class="thead-dark">
            <tr>
              <th style="width:70px">No</th>
              <th>Tahun</th>
              <th>Status</th>
              <th>Disetujui Oleh</th>
              <th>Diajukan Pada</th>
              <th>Diperbarui</th>
            </tr>
          </thead>  
          <tbody>
          @forelse($riwayat as $i => $r)
            @php $st = strtolower($r->status ?? ''); @endphp
            <tr>
              <td class="text-center">{{ $i+1 }}</td>
              <td>{{ $r->tahun_kgp ?? '-' }}</td>
              <td>
                <span class="badge-bnn {{ $st=='disetujui'?'badge-ok':($st=='ditolak'?'badge-no':'badge-wait') }}">
                  {{ $r->status ?? '-' }}
                </span>
              </td>
              <td>{{ $r->disetujui_oleh ?? '-' }}</td>
              <td>{{ \Carbon\Carbon::parse($r->created_at)->translatedFormat('d M Y H:i') }}</td>
              <td>{{ \Carbon\Carbon::parse($r->updated_at)->translatedFormat('d M Y H:i') }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted p-3">Belum ada riwayat.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection
