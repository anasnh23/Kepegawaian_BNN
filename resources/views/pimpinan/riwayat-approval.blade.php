@extends('layouts.template')
@section('title', 'Riwayat Persetujuan Dokumen')

@section('content')
<style>
:root { --bnn-navy:#003366; --bnn-gold:#ffc107; }
body { background:#f4f6f9; }

/* Hero */
.bnn-hero {
  background: var(--bnn-navy);
  border-radius: 10px;
  padding: 20px 24px;
  margin-bottom: 20px;
  color: #fff;
  display:flex; justify-content:space-between; align-items:center;
}
.bnn-hero h2 { font-weight:700; margin:0; }
.bnn-hero small { color:#dce3ec; }
.bnn-hero .btn-dashboard {
  background: var(--bnn-gold); color:#000; font-weight:600;
  border-radius:6px; padding:6px 14px; font-size:.9rem;
}
.bnn-hero .btn-dashboard:hover { background:#e0ac06; color:#000; }

/* Summary cards */
.summary-cards {
  display:flex; gap:16px; flex-wrap:wrap; margin-top:15px;
}
.summary-cards .card {
  flex:1; min-width:160px; border:0; border-radius:8px;
  padding:14px; text-align:center; font-weight:600;
  background:#fff; box-shadow:0 2px 6px rgba(0,0,0,.08);
}
.sum-acc { border-left:5px solid #28a745; color:#28a745; }
.sum-rej { border-left:5px solid #dc3545; color:#dc3545; }
.sum-pend{ border-left:5px solid #ffc107; color:#856404; }

/* Filter */
.filter-bar {
  background:#fff; border:1px solid #e0e0e0; border-radius:8px;
  padding:12px; margin-bottom:15px;
}

/* Table */
.table thead th {
  background: var(--bnn-navy); color:#fff;
  font-size:.85rem; text-transform:uppercase;
}
.table-hover tbody tr:hover { background:#f8fbff; }
.badge { font-size:.8rem; font-weight:600; padding:.45em .75em; }
</style>

<div class="container-fluid">
  {{-- HERO --}}
  <div class="bnn-hero">
    <div>
      <h2><i class="fas fa-check-double text-warning mr-2"></i> Riwayat Persetujuan Dokumen</h2>
      <small>Lihat seluruh riwayat persetujuan cuti pegawai</small>
    </div>
    <a href="{{ url('/dashboard') }}" class="btn btn-dashboard">
      <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
  </div>

  {{-- SUMMARY --}}
  <div class="summary-cards">
    <div class="card sum-acc">{{ $cuti->where('approvalPimpinan.status','Disetujui')->count() }} Disetujui</div>
    <div class="card sum-rej">{{ $cuti->where('approvalPimpinan.status','Ditolak')->count() }} Ditolak</div>
    <div class="card sum-pend">{{ $cuti->whereNull('approvalPimpinan.status')->count() }} Menunggu</div>
  </div>

  {{-- FILTER --}}
  <div class="filter-bar d-flex flex-wrap gap-2 align-items-center">
    <input type="text" id="searchInput" class="form-control mr-2 mb-2" placeholder="Cari nama/NIP/jenis cuti…" style="max-width:240px">
    <select id="statusFilter" class="form-control mr-2 mb-2" style="max-width:180px">
      <option value="">Semua Status</option>
      <option value="disetujui">Disetujui</option>
      <option value="ditolak">Ditolak</option>
      <option value="menunggu">Menunggu</option>
    </select>
    <input type="date" id="dateFrom" class="form-control mr-2 mb-2" style="max-width:180px">
    <input type="date" id="dateTo" class="form-control mr-2 mb-2" style="max-width:180px">
    <button class="btn btn-secondary btn-sm mb-2" id="btnReset"><i class="fas fa-undo"></i> Reset</button>
  </div>

  {{-- TABLE --}}
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover table-bordered text-center mb-0" id="riwayatTable">
          <thead>
            <tr>
              <th>No</th>
              <th class="text-left">Nama</th>
              <th>NIP</th>
              <th class="text-left">Jenis Cuti</th>
              <th>Periode</th>
              <th>Lama</th>
              <th>Dokumen</th>
              <th>Status</th>
              <th>Tgl Persetujuan</th>
            </tr>
          </thead>
          <tbody>
            @forelse($cuti as $item)
              @php $approval = $item->approvalPimpinan; @endphp
              <tr data-status="{{ strtolower($approval->status ?? 'menunggu') }}" data-date="{{ $approval->updated_at ?? $item->tanggal_pengajuan }}">
                <td>{{ $loop->iteration }}</td>
                <td class="text-left">{{ $item->pegawai->nama ?? '-' }}</td>
                <td>{{ $item->pegawai->nip ?? '-' }}</td>
                <td class="text-left">{{ $item->jenis_cuti }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d-m-Y') }}</td>
                <td>{{ $item->lama_cuti }} hari</td>
                <td>
                  @if($approval && $approval->dokumen_path)
                    <a href="{{ asset('storage/' . $approval->dokumen_path) }}" target="_blank" class="btn btn-outline-info btn-sm">
                      <i class="fas fa-file-pdf"></i> Lihat
                    </a>
                  @else
                    <span class="text-muted">Tidak Ada</span>
                  @endif
                </td>
                <td>
                  @if($approval?->status == 'Disetujui')
                    <span class="badge badge-success">Disetujui</span>
                  @elseif($approval?->status == 'Ditolak')
                    <span class="badge badge-danger">Ditolak</span>
                  @else
                    <span class="badge badge-warning text-dark">Menunggu</span>
                  @endif
                </td>
                <td>{{ $approval->updated_at ? \Carbon\Carbon::parse($approval->updated_at)->format('d-m-Y H:i') : '-' }}</td>
              </tr>
            @empty
              <tr><td colspan="9" class="text-muted">Belum ada dokumen yang diproses</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Filter sederhana
const search=document.getElementById('searchInput'),
      status=document.getElementById('statusFilter'),
      from=document.getElementById('dateFrom'),
      to=document.getElementById('dateTo'),
      rows=document.querySelectorAll('#riwayatTable tbody tr');
function filterRows(){
  const q=(search.value||'').toLowerCase();
  const st=status.value;
  const f=from.value?new Date(from.value):null;
  const t=to.value?new Date(to.value):null;
  rows.forEach(r=>{
    const txt=r.innerText.toLowerCase();
    const rs=r.dataset.status, d=new Date(r.dataset.date);
    const ok=(q==''||txt.includes(q))&&(st==''||rs==st)&&( (!f||d>=f)&&(!t||d<=t) );
    r.style.display=ok?'':'none';
  });
}
[search,status,from,to].forEach(el=>el.addEventListener('input',filterRows));
document.getElementById('btnReset').addEventListener('click',()=>{search.value='';status.value='';from.value='';to.value='';filterRows();});
</script>
@endpush
