@extends('layouts.template')
@section('title','KGP • Riwayat Gaji')

@section('page_header')
<style>
:root{ --bnn-navy:#0a2647; --bnn-blue:#144272; --bnn-cyan:#2c74b3; --bnn-gold:#f4c430; --bnn-gold-2:#ffd76a; --line:#e6eef9; }
.pagehead{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:6px 0 14px}
.pagehead .title{font-weight:900;font-size:2rem;line-height:1.15;color:#0a2647;letter-spacing:.2px}
.pagehead .crumb{color:#64748b;font-weight:800}
.pagehead .crumb a{color:#0f172a;text-decoration:none;font-weight:900}
.pagehead .crumb .sep{margin:0 8px;opacity:.5}
.hero-band{background:linear-gradient(135deg,#0d2b52,#062447 65%);border:1px solid rgba(255,255,255,.08);
  color:#eaf2ff;border-radius:22px;padding:18px;display:flex;align-items:center;justify-content:space-between;gap:16px;
  box-shadow:0 24px 60px rgba(4,14,33,.28)}
.hero-left{display:flex;align-items:center;gap:12px}
.hero-icon{width:36px;height:36px;border-radius:12px;display:grid;place-items:center;
  background:linear-gradient(135deg,var(--bnn-gold),var(--bnn-gold-2));color:#2e1c00;
  box-shadow:inset 0 1px 0 rgba(255,255,255,.6),0 10px 24px rgba(0,0,0,.35)}
.hero-title{font-size:1.4rem;font-weight:900;letter-spacing:.2px;color:#fff}
.hero-sub{margin-top:2px;color:#c9d6ee;font-weight:700}
.btn-dash{background:#ffb703;border:none;color:#1a1a1a;font-weight:900;border-radius:10px;padding:10px 14px;
  display:inline-flex;align-items:center;gap:8px;box-shadow:0 12px 28px rgba(16,24,40,.22)}
.btn-dash:hover{filter:brightness(1.05)}
</style>

<div class="container-fluid">
  <div class="pagehead">
    <div class="title">Pengajuan KGP</div>
    <div class="crumb">
      <a href="{{ route('dashboard') }}">Dashboard</a><span class="sep">/</span>Kepegawaian<span class="sep">/</span>Pengajuan KGP
    </div>
  </div>

  <div class="hero-band">
    <div class="hero-left">
      <div class="hero-icon"><i class="fas fa-file-invoice-dollar"></i></div>
      <div>
        <div class="hero-title">Pengajuan Kenaikan Gaji Berkala</div>
        <div class="hero-sub">Pengajuan berdasarkan masa kerja kelipatan 4 tahun</div>
      </div>
    </div>
    <a href="{{ route('dashboard') }}" class="btn-dash">
      <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
  </div>
</div>
@endsection

@section('content')
@php
  $rp      = fn($n) => is_null($n) ? '-' : 'Rp '.number_format($n,0,',','.');
  $stages  = $kgpTimeline['stages'] ?? [];
  // TMK kumulatif untuk menampilkan total di tabel riwayat:
  $tmkKumulatif = (int)($approvedStages ?? 0) * (int)($tmkPerStage ?? 0);
@endphp

<style>
:root{ --line:#e6eef9; --shadow-2:0 12px 28px rgba(3,10,27,.14); --shadow-3:0 8px 18px rgba(3,10,27,.10); }
.card{background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:var(--shadow-2);overflow:hidden;margin-top:18px}
.card-h{padding:14px 16px;border-bottom:1px solid var(--line);background:linear-gradient(180deg,#edf4ff,#ffffff);
  font-weight:900;color:#0a2647;display:flex;align-items:center;gap:10px}
.card-b{padding:14px 16px}
.kpi{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.kpi .item{background:#fff;border:1px solid var(--line);border-radius:16px;padding:12px;box-shadow:var(--shadow-3)}
.kpi .t{font-size:.78rem;color:#0a2647;font-weight:900;text-transform:uppercase}
.kpi .v{font-weight:900;color:#0f172a;font-size:1.08rem}
.alert{border-radius:16px;padding:12px 14px;border:1px dashed #dbeafe;background:#f5faff;display:flex;justify-content:space-between;gap:12px;margin-top:14px}
.badge{border-radius:999px;padding:4px 10px;font-weight:800}
.badge-ok{background:#dcfce7;color:#065f46}
.badge-wait{background:#fff1c2;color:#6a4b00}
.badge-info{background:#eef6ff;color:#0a2647;border:1px solid #dbeafe}
.stages{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.stagebox{background:#fff;border:1px solid var(--line);border-radius:16px;padding:12px;box-shadow:var(--shadow-3)}
.stagebox h4{margin:0 0 4px;font-weight:900;color:#0a2647;font-size:1.02rem}
.table{width:100%;border-collapse:separate;border-spacing:0 8px}
.table thead th{font-weight:900;font-size:.85rem;padding:6px 10px;border-bottom:1px dashed #e5eaf3}
.trow td{background:#fff;border:1px solid var(--line);padding:10px}
.trow td:first-child{border-radius:12px 0 0 12px}
.trow td:last-child{border-radius:0 12px 12px 0}
@media (max-width:1100px){ .kpi{grid-template-columns:1fr} .stages{grid-template-columns:1fr 1fr} }
@media (max-width:768px){ .stages{grid-template-columns:1fr} }
</style>

<div class="container-fluid">

  {{-- Ringkasan & Riwayat --}}
  <section class="card">
    <div class="card-h"><i class="fas fa-coins"></i> Ringkasan & Riwayat</div>
    <div class="card-b">

      <div class="kpi">
        <div class="item">
          <div class="t">Pegawai</div>
          <div class="v">{{ $user->nama }} • {{ $user->nip }}</div>
        </div>
        <div class="item">
          <div class="t">Gaji Pokok Saat Ini</div>
          <div class="v">{{ $rp($gajiBerjalan) }}</div>
        </div>
        <div class="item">
          <div class="t">Total Gaji (Pokok{{ $tmk?'+TMK':'' }})</div>
          <div class="v">{{ $rp($totalGaji) }}</div>
        </div>
      </div>

      <div class="alert">
        <div>
          Tahap Berikut: <b>{{ $elig['next_stage'] }}</b>
          @if($elig['eligible_now'])
            <span class="badge badge-ok">Sudah waktunya</span>
          @else
            <span class="badge badge-wait">Belum waktunya</span>
          @endif
          <div style="color:#64748b;font-weight:700">
            Dapat diajukan mulai: {{ optional($elig['eligible_from'])->translatedFormat('d F Y') ?? '-' }}
          </div>
        </div>
        <div>
          @if(Auth::user()->id_level == 1)
            <form action="{{ route('kgp.approve', $user->id_user) }}" method="POST"
                  onsubmit="return confirm('Setujui KGP tahap {{ $elig['next_stage'] }} untuk {{ $user->nama }}?');">
              @csrf
              <button class="btn btn-sm btn-primary" {{ $elig['eligible_now'] ? '' : 'disabled' }}>
                <i class="fas fa-check-circle mr-1"></i> Approve Tahap {{ $elig['next_stage'] }}
              </button>
            </form>
          @endif
        </div>
      </div>

      {{-- Timeline --}}
      <div class="card" style="margin-top:14px">
        <div class="card-h"><i class="fas fa-stream"></i> Timeline Tahap (Kelipatan 4 Tahun)</div>
        <div class="card-b">
          <div class="stages">
            @forelse($stages as $s)
              <div class="stagebox">
                <h4>{{ $s['label'] }}</h4>
                <div>{{ $s['date']->translatedFormat('d F Y') }}</div>
                <div style="margin-top:6px">
                  @if($s['approved'])
                    <span class="badge badge-ok">Disetujui</span>
                  @else
                    <span class="badge badge-wait">Belum</span>
                  @endif

                  {{-- Jika sudah disetujui, tampilkan TOTAL; jika belum & ada pokok, tampilkan pokok --}}
                  @if(!is_null($s['gaji_total'] ?? null))
                    <span class="badge badge-info">Total: {{ $rp($s['gaji_total']) }}</span>
                  @elseif(!is_null($s['gaji'] ?? null))
                    <span class="badge badge-info">{{ $rp($s['gaji']) }}</span>
                  @endif
                </div>
              </div>
            @empty
              <em>Tidak ada data TMT.</em>
            @endforelse
          </div>
        </div>
      </div>

      {{-- Tabel Riwayat Gaji --}}
      <div class="card" style="margin-top:14px">
        <div class="card-h"><i class="fas fa-table"></i> Riwayat Gaji (Database)</div>
        <div class="card-b">
          <table class="table">
            <thead>
              <tr>
                <th style="width:60px">No</th>
                <th>Tanggal Berlaku</th>
                <th>Gaji Pokok</th>
                <th>Total (Pokok+TMK)</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              @forelse($riwayat as $i => $row)
                @php
                  // Menampilkan total sesuai kebijakan tampilan: pokok + TMK kumulatif saat ini
                  $totalRow = (int)$row->gaji_pokok + $tmkKumulatif;
                @endphp
                <tr class="trow">
                  <td style="text-align:center">{{ $i+1 }}</td>
                  <td>{{ \Carbon\Carbon::parse($row->tanggal_berlaku)->translatedFormat('d F Y') }}</td>
                  <td>{{ $rp($row->gaji_pokok) }}</td>
                  <td>{{ $rp($totalRow) }}</td>
                  <td>{{ $row->keterangan ?? '-' }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center">Belum ada riwayat.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div style="color:#667085;font-size:.92rem;margin-top:10px">
        * KGP dihitung kelipatan 4 tahun sejak TMT awal. Nilai “Total (Pokok+TMK)” pada tabel menambahkan TMK
        kumulatif berdasarkan jumlah tahap yang sudah disetujui.
      </div>

    </div>
  </section>
</div>
@endsection
