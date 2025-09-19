@extends('layouts.template')
@section('title','Pengajuan KGP')

@section('content')
<style>
  :root{
    --bnn-navy:#003366; --bnn-navy-2:#0b2f5e;
    --bnn-gold:#f0ad4e; --bnn-gold-2:#d89a2b;
    --soft:#f5f8fc; --line:#e6edf6;
    --green:#28a745; --yellow:#ffc107; --red:#dc3545;
  }
  .bnn-hero{
    background:linear-gradient(135deg,var(--bnn-navy),#012148 60%,var(--bnn-navy-2));
    color:#fff;border-radius:16px;padding:18px 20px;margin-bottom:14px;
    box-shadow:0 14px 36px rgba(0,33,72,.22); position:relative; overflow:hidden;
  }
  .bnn-hero::after{
    content:"";position:absolute;right:-60px;top:-60px;width:200px;height:200px;opacity:.08;
    background:url('{{ asset('images/bnn-watermark.svg') }}') no-repeat center/contain;
  }
  .badge-bnn{border-radius:999px;font-weight:700;padding:.4rem .6rem;}
  .badge-wait{background:#fff7e6;color:#7a4d00;border:1px solid #ffe0ad}
  .badge-ok{background:#e7f6ec;color:#146c2e;border:1px solid #bfe5c8}
  .badge-no{background:#fdeaea;color:#842029;border:1px solid #f8c2c7}
  .card-bnn{border:1px solid var(--line);border-radius:14px;box-shadow:0 8px 24px rgba(16,24,40,.06);overflow:hidden;}
  .card-bnn .card-header{background:var(--bnn-navy);color:#fff;font-weight:700;}
  .bnn-btn{background:var(--bnn-gold);border-color:var(--bnn-gold-2);color:#122;}
  .bnn-btn:hover{filter:brightness(.95)}
</style>

<div class="container-fluid">

  {{-- Hero --}}
  <div class="bnn-hero d-flex align-items-center justify-content-between">
    <div>
      <h4 class="mb-1"><i class="fas fa-money-check-alt mr-2"></i>Pengajuan Kenaikan Gaji Berkala</h4>
      <small class="text-light">Pengajuan berdasarkan masa kerja kelipatan 4 tahun</small>
    </div>
    <a href="{{ route('dashboard.pegawai') }}" class="btn btn-sm bnn-btn">
      <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
    </a>
  </div>

  {{-- Info & Kelayakan --}}
  <div class="card card-bnn mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
      <span>Informasi Kelayakan</span>
      @if($bolehAjukan)
        <span class="badge-bnn badge-ok">Layak Mengajukan</span>
      @elseif($adaPending)
        <span class="badge-bnn badge-wait">Ada Pengajuan Menunggu</span>
      @else
        <span class="badge-bnn badge-no">Belum Layak</span>
      @endif
    </div>
    <div class="card-body">
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

      <div class="row">
        <div class="col-md-6">
          <table class="table table-sm mb-0">
            <tr><th style="width:220px">TMT Awal</th><td>{{ $tmt->translatedFormat('d F Y') }}</td></tr>
            <tr><th>Masa Kerja</th><td>{{ $masaKerjaTahun }} tahun</td></tr>
            <tr><th>Tahap Seharusnya</th><td>{{ $tahapSeharusnya }}</td></tr>
            <tr><th>Jumlah Disetujui</th><td>{{ $pengajuanApproved }}</td></tr>
            <tr><th>Pengajuan Pending</th><td>{{ $adaPending ? 'Ya' : 'Tidak' }}</td></tr>
          </table>
        </div>
        <div class="col-md-6">
          @if($bolehAjukan)
            <div class="alert alert-success">
              Anda memenuhi syarat untuk <strong>Pengajuan KGP Tahap {{ $tahapBerikut }}</strong>.
            </div>
            <form method="POST" action="{{ route('kgp.store') }}">
              @csrf
              <button class="btn bnn-btn">
                <i class="fas fa-paper-plane mr-1"></i> Ajukan KGP Sekarang
              </button>
            </form>
          @else
            <div class="alert alert-warning">
              Tahap berikut (Tahap {{ $pengajuanApproved + 1 }}) dapat diajukan mulai:
              <strong>{{ $estimasiTanggalLayak->translatedFormat('d F Y') }}</strong>.
              @if($adaPending)
                <br>Pengajuan baru akan tersedia setelah keputusan pimpinan.
              @endif
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Riwayat Singkat --}}
  <div class="card card-bnn">
    <div class="card-header d-flex align-items-center justify-content-between">
      <span>Riwayat Pengajuan KGP</span>
      <a class="btn btn-sm btn-outline-light" href="{{ route('kgp.riwayat') }}"><i class="fas fa-list mr-1"></i> Lihat Semua</a>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead class="thead-dark">
            <tr>
              <th style="width:70px">No</th>
              <th>Tahun</th>
              <th>Status</th>
              <th>Diajukan Pada</th>
            </tr>
          </thead>
          <tbody>
          @forelse($riwayat->take(5) as $i => $r)
            @php $st = strtolower($r->status ?? ''); @endphp
            <tr>
              <td class="text-center">{{ $i+1 }}</td>
              <td>{{ $r->tahun_kgp ?? '-' }}</td>
              <td>
                <span class="badge-bnn {{ $st=='disetujui'?'badge-ok':($st=='ditolak'?'badge-no':'badge-wait') }}">
                  {{ $r->status ?? '-' }}
                </span>
              </td>
              <td>{{ \Carbon\Carbon::parse($r->created_at)->translatedFormat('d M Y H:i') }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted p-3">Belum ada pengajuan.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection
