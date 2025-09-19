@extends('layouts.template')
@section('title','Approval KGP (Admin/Pimpinan)')

@section('content')
@php
  use App\Helpers\MasaKerja;

  $per4 = (int) env('KGP_NAIK_PER_4_TAHUN', 1000000);

  $rowsPending = $kgp ?? collect();
  $rowsHist    = $riwayat ?? collect();

  $total     = $rowsPending->count();
  $menunggu  = $rowsPending->where('status','Menunggu')->count();
  $disetujui = $rowsHist->where('status','Disetujui')->count();
  $ditolak   = $rowsHist->where('status','Ditolak')->count();
@endphp

<style>
:root{ --bnn-navy:#0b2f5e; --bnn-ink:#0f1f39; --bnn-gold:#ffc107; --line:#e6edf6; }
body{background:#f4f6f9;}
.bnn-hero{background:linear-gradient(135deg,var(--bnn-navy),#08254a 60%,var(--bnn-ink));color:#fff;border-radius:16px;padding:22px 24px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between}
.bnn-hero h1{font-weight:800;font-size:1.6rem;margin:0}
.bnn-hero .sub{color:#dbe7ff}
.btn-dash{background:var(--bnn-gold);color:#000;font-weight:800;border-radius:10px;padding:.55rem .9rem}

.kpi{display:grid;grid-template-columns:repeat(4,minmax(180px,1fr));gap:12px;margin-bottom:14px}
.kpi .cardy{background:#fff;border:1px solid var(--line);border-radius:14px;padding:14px;box-shadow:0 10px 26px rgba(16,24,40,.06)}
.kpi .title{font-size:.8rem;color:#6b7a90;font-weight:700}
.kpi .val{font-size:1.35rem;font-weight:800}
.badge-chip{border-radius:999px;padding:.25rem .55rem;font-size:.75rem;font-weight:800}
.chip-ok{background:#e7f6ec;color:#146c2e;border:1px solid #bfe5c8}
.chip-no{background:#fdeaea;color:#842029;border:1px solid #f8c2c7}
.chip-wait{background:#fff7e6;color:#7a4d00;border:1px solid #ffe0ad}

.card-bnn{border:1px solid var(--line);border-radius:16px;overflow:hidden;box-shadow:0 10px 26px rgba(16,24,40,.08)}
.card-bnn .card-header{background:var(--bnn-ink);color:#eaf2ff;font-weight:800}
.table thead th{background:var(--bnn-ink);color:#eaf2ff;border-color:var(--bnn-ink);text-transform:uppercase;font-size:.85rem}
.table td,.table th{border-color:#e9eef6;vertical-align:middle}
.table-hover tbody tr:hover{background:#fbfdff}
.table-wrap{overflow:auto}

.badge-bnn{border-radius:999px;padding:.42rem .68rem;font-weight:800;white-space:nowrap;font-size:.82rem}
.st-ok{background:#e7f6ec;color:#146c2e;border:1px solid #bfe5c8}
.st-wait{background:#fff7e6;color:#7a4d00;border:1px solid #ffe0ad}
.st-no{background:#fdeaea;color:#842029;border:1px solid #f8c2c7}

.btn-approve{background:#e7f6ec;color:#146c2e;border:1px solid #bfe5c8;font-weight:800;border-radius:10px;padding:.45rem .7rem}
.btn-reject{background:#fdeaea;color:#842029;border:1px solid #f8c2c7;font-weight:800;border-radius:10px;padding:.45rem .7rem}

.masa-kerja{font-weight:800;letter-spacing:.2px}
.masa-kerja .unit{font-weight:700;color:#6b7a90;margin:0 .25rem}
@media (max-width:992px){ .kpi{grid-template-columns:repeat(2,1fr)} }
@media (max-width:576px){ .kpi{grid-template-columns:1fr} }
</style>

<div class="container-fluid">
  {{-- HERO --}}
  <div class="bnn-hero">
    <div>
      <h1><i class="fas fa-money-check-alt mr-2 text-warning"></i> Approval Kenaikan Gaji Berkala</h1>
      <div class="sub">Kelola usulan KGP pegawai & lihat riwayat keputusan secara profesional.</div>
    </div>
    <a href="{{ url('/dashboard-admin') }}" class="btn btn-dash"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  </div>

  {{-- KPI --}}
  <div class="kpi">
    <div class="cardy d-flex justify-content-between align-items-center">
      <div><div class="title">TOTAL MENUNGGU</div><div class="val">{{ $total }}</div></div>
      <span class="badge-chip chip-wait">KGP</span>
    </div>
    <div class="cardy d-flex justify-content-between align-items-center">
      <div><div class="title">MENUNGGU</div><div class="val">{{ $menunggu }}</div></div>
      <span class="badge-chip chip-wait">Menunggu</span>
    </div>
    <div class="cardy d-flex justify-content-between align-items-center">
      <div><div class="title">DISETUJUI (riwayat)</div><div class="val text-success">{{ $disetujui }}</div></div>
      <span class="badge-chip chip-ok">OK</span>
    </div>
    <div class="cardy d-flex justify-content-between align-items-center">
      <div><div class="title">DITOLAK (riwayat)</div><div class="val text-danger">{{ $ditolak }}</div></div>
      <span class="badge-chip chip-no">NO</span>
    </div>
  </div>

  {{-- PENDING LIST --}}
  <div class="card card-bnn mb-4">
    <div class="card-header"><i class="fas fa-list mr-2"></i> Daftar Usulan KGP (Menunggu)</div>
    <div class="table-wrap">
      <table class="table table-bordered table-hover m-0" id="tblPending">
        <thead>
          <tr class="text-center">
            <th style="width:64px">No</th>
            <th>Pegawai</th>
            <th style="width:110px">Tahun</th>
            <th style="width:140px">TMT</th>
            <th style="width:160px">Masa Kerja</th>
            <th style="width:140px">Naik*</th>
            <th style="width:200px">Aksi</th>
          </tr>
        </thead>
        <tbody>
        @forelse($rowsPending as $i => $r)
          @php
            $p     = $r->pegawai;
            $nama  = $p->nama ?? '-';

            // ✅ Pakai helper: integer Tahun & Bulan (bukan pecahan)
            [$yRaw, $mRaw] = MasaKerja::yearsMonths((int)($p->id_user ?? 0));
            $years  = (int) $yRaw;
            $months = (int) $mRaw;

            // ✅ Kenaikan berdasarkan tahun utuh
            $naik = intdiv($years, 4) * $per4;

            $tmt = $r->tmt ? \Carbon\Carbon::parse($r->tmt)->translatedFormat('d M Y') : '-';
          @endphp
          <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td>
              <div class="font-weight-bold">{{ $nama }}</div>
              <div class="small text-muted">{{ $p?->jabatan?->nama_jabatan ?? '—' }}</div>
            </td>
            <td class="text-center">{{ $r->tahun_kgp ?? '-' }}</td>
            <td class="text-center">{{ $tmt }}</td>
            <td class="text-center masa-kerja">
              {{ $years }}<span class="unit">th</span> {{ $months }}<span class="unit">bulan</span>
            </td>
            <td class="text-center">Rp {{ number_format($naik,0,',','.') }}</td>
            <td class="text-center">
              <button class="btn btn-approve mr-1" data-action="approve" data-id="{{ $r->id_kgp }}"><i class="fas fa-check mr-1"></i> Setujui</button>
              <button class="btn btn-reject" data-action="reject" data-id="{{ $r->id_kgp }}"><i class="fas fa-times mr-1"></i> Tolak</button>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-muted p-3">Tidak ada usulan menunggu.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-2 small text-muted">
      *Kenaikan dihitung: <strong>Rp {{ number_format($per4,0,',','.') }}</strong> per <strong>4 tahun</strong> masa kerja.
    </div>
  </div>

  {{-- HISTORY --}}
  <div class="card card-bnn">
    <div class="card-header"><i class="fas fa-history mr-2"></i> Riwayat Disetujui / Ditolak (50 terbaru)</div>
    <div class="table-wrap">
      <table class="table table-bordered table-hover m-0" id="tblHistory">
        <thead>
          <tr class="text-center">
            <th style="width:64px">No</th>
            <th>Pegawai</th>
            <th style="width:110px">Tahun</th>
            <th style="width:140px">TMT</th>
            <th style="width:130px">Status</th>
            <th>Catatan</th>
            <th style="width:180px">Diputus Pada</th>
            <th style="width:200px">Oleh</th>
          </tr>
        </thead>
        <tbody>
        @forelse($rowsHist as $i => $r)
          @php
            $p     = $r->pegawai;
            $nama  = $p->nama ?? '-';
            $tmt   = $r->tmt ? \Carbon\Carbon::parse($r->tmt)->translatedFormat('d M Y') : '-';
            $st    = strtolower($r->status ?? '');
            $badge = $st==='disetujui' ? 'st-ok' : ($st==='ditolak' ? 'st-no' : 'st-wait');
            $oleh  = $r->disetujuiOleh?->nama ?: ('User #'.($r->disetujui_oleh ?? '-'));
            $waktu = $r->disetujui_at ? \Carbon\Carbon::parse($r->disetujui_at)->translatedFormat('d M Y H:i') : '-';
          @endphp
          <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td><div class="font-weight-bold">{{ $nama }}</div><div class="small text-muted">{{ $p?->jabatan?->nama_jabatan ?? '—' }}</div></td>
            <td class="text-center">{{ $r->tahun_kgp ?? '-' }}</td>
            <td class="text-center">{{ $tmt }}</td>
            <td class="text-center"><span class="badge-bnn {{ $badge }}">{{ ucfirst($st) }}</span></td>
            <td>{{ $r->catatan ?? '-' }}</td>
            <td class="text-center">{{ $waktu }}</td>
            <td>
              <div class="font-weight-bold">{{ $oleh }}</div>
              <div class="small text-muted">{{ $r->disetujuiOleh?->jabatan?->nama_jabatan ?? '—' }}</div>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="text-center text-muted p-3">Belum ada riwayat keputusan.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function(){
  const csrf = '{{ csrf_token() }}';
  const approveBase = "{{ url('/approval-kgp/approve') }}";
  const rejectBase  = "{{ url('/approval-kgp/reject') }}";

  function post(url, payload){
    return fetch(url, {
      method:'POST',
      headers:{'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'},
      body: JSON.stringify(Object.assign({_token: csrf}, payload||{}))
    }).then(r=>r.json().then(j=>({ok:r.ok, data:j})));
  }

  document.querySelector('#tblPending tbody')?.addEventListener('click', function(e){
    const btn = e.target.closest('button[data-action]');
    if(!btn) return;
    const id  = btn.dataset.id;

    if(btn.dataset.action==='approve'){
      Swal.fire({
        title:'Setujui usulan ini?', icon:'question', showCancelButton:true,
        confirmButtonText:'Ya, Setujui', confirmButtonColor:'#0b2f5e', cancelButtonText:'Batal'
      }).then(res=>{
        if(res.isConfirmed){
          post(`${approveBase}/${id}`).then(({ok,data})=>{
            if(ok){ Swal.fire('Berhasil',data.message||'KGP disetujui.','success').then(()=>location.reload()); }
            else { Swal.fire('Gagal',data.message||'Terjadi kesalahan.','error'); }
          }).catch(()=>Swal.fire('Gagal','Tidak dapat terhubung ke server.','error'));
        }
      });
    } else {
      Swal.fire({
        title:'Tolak usulan ini?', input:'text', inputPlaceholder:'Alasan penolakan (opsional)',
        icon:'warning', showCancelButton:true, confirmButtonText:'Ya, Tolak',
        confirmButtonColor:'#e74c3c', cancelButtonText:'Batal'
      }).then(res=>{
        if(res.isConfirmed){
          post(`${rejectBase}/${id}`, {catatan: res.value||'Ditolak pimpinan'}).then(({ok,data})=>{
            if(ok){ Swal.fire('Berhasil',data.message||'KGP ditolak.','success').then(()=>location.reload()); }
            else { Swal.fire('Gagal',data.message||'Terjadi kesalahan.','error'); }
          }).catch(()=>Swal.fire('Gagal','Tidak dapat terhubung ke server.','error'));
        }
      });
    }
  });
})();
</script>
@endpush
