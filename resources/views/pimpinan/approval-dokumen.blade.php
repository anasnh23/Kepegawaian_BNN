@extends('layouts.template')
@section('title', 'Approval Dokumen Cuti')

@section('content')
<style>
:root {
  --bnn-navy:#003366;
  --bnn-gold:#ffc107;
}
body { background:#f4f6f9; }

/* Hero */
.bnn-hero {
  background: var(--bnn-navy);
  border-radius: 10px;
  padding: 20px 24px;
  color: #fff;
  margin-bottom: 20px;
  display:flex; justify-content:space-between; align-items:center;
}
.bnn-hero h2 { font-weight: 700; margin:0; }
.bnn-hero small { color:#dce3ec; }
.bnn-hero .btn-dashboard {
  background: var(--bnn-gold); color:#000; font-weight:600;
  border-radius:6px; padding:6px 14px; font-size:.9rem;
}
.bnn-hero .btn-dashboard:hover { background:#e0ac06; color:#000; }

/* Summary cards */
.summary-cards {
  display: flex; gap: 16px; flex-wrap: wrap; margin-top: 15px;
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

/* Status badge */
.badge { font-size:.8rem; font-weight:600; padding:.45em .75em; }

/* Action buttons */
.btn-action { border-radius:6px; font-size:.8rem; padding:.35rem .6rem; margin:2px; }
</style>

<div class="container-fluid">
  {{-- HERO --}}
  <div class="bnn-hero">
    <div>
      <h2><i class="fas fa-user-tie mr-2 text-warning"></i> Approval Dokumen Cuti</h2>
      <small>Lihat, verifikasi, dan sahkan dokumen cuti pegawai secara profesional.</small>
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
    <input type="text" id="searchInput" class="form-control mr-2 mb-2" placeholder="Cari nama/jenis cuti…" style="max-width:240px">
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
    <div class="card-header bg-white border-0">
      <h6 class="mb-0 font-weight-bold text-dark"><i class="fas fa-file-alt mr-2"></i>Daftar Dokumen</h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover table-bordered text-center mb-0" id="approvalTable">
          <thead>
            <tr>
              <th>No</th><th>Nama</th><th>Jenis</th><th>Pengajuan</th>
              <th>Periode</th><th>Lama</th><th>Dokumen</th><th>Status</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($cuti as $item)
              @php $approval = $item->approvalPimpinan; @endphp
              <tr data-status="{{ strtolower($approval->status ?? 'menunggu') }}" data-date="{{ $item->tanggal_pengajuan }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ optional($item->pegawai)->nama ?? '-' }}</td>
                <td>{{ $item->jenis_cuti ?? '-' }}</td>
                <td>{{ $item->tanggal_pengajuan ? \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') : '-' }}</td>
                <td>{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') : '-' }} s/d {{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d-m-Y') : '-' }}</td>
                <td>{{ $item->lama_cuti ? $item->lama_cuti.' hari' : '-' }}</td>
                <td>
                  @if($approval && $approval->dokumen_path)
                    <a href="{{ asset('storage/' . $approval->dokumen_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">Lihat</a>
                  @else
                    <span class="text-muted">Belum Upload</span>
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
                <td>
                  @if(!$approval || !$approval->status || strtolower($approval->status) == 'menunggu')
                    <button onclick="setujui({{ $approval->id }})" class="btn btn-success btn-action" title="Setujui"><i class="fas fa-check"></i></button>
                    <button onclick="tolak({{ $approval->id }})" class="btn btn-danger btn-action" title="Tolak"><i class="fas fa-times"></i></button>
                    <a href="{{ route('approval.edit', $approval->id) }}" class="btn btn-warning btn-action" title="Edit"><i class="fas fa-edit"></i></a>
                  @else
                    <span class="text-muted">Selesai</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="9" class="text-muted">Belum ada data dokumen</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Filter sederhana
const search=document.getElementById('searchInput'),
      status=document.getElementById('statusFilter'),
      from=document.getElementById('dateFrom'),
      to=document.getElementById('dateTo'),
      rows=document.querySelectorAll('#approvalTable tbody tr');
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

// SweetAlert actions
function setujui(id){ 
  Swal.fire({
    title:'Setujui dokumen ini?',icon:'question',showCancelButton:true,
    confirmButtonText:'Ya, Setujui',confirmButtonColor:'#003366'
  }).then(res=>{if(res.isConfirmed)updateStatus(id,'Disetujui');}); 
}
function tolak(id){ 
  Swal.fire({
    title:'Tolak dokumen ini?',icon:'warning',showCancelButton:true,
    confirmButtonText:'Ya, Tolak',confirmButtonColor:'#e74c3c'
  }).then(res=>{if(res.isConfirmed)updateStatus(id,'Ditolak');}); 
}
function updateStatus(id,status){ 
  $.post("{{ url('/approval-dokumen/update-status') }}",
    {_token:'{{ csrf_token() }}',id,status},
    res=>{ Swal.fire('Berhasil',res.message,'success').then(()=>location.reload()); }
  ).fail(()=>Swal.fire('Gagal','Terjadi kesalahan.','error')); 
}
</script>
@endpush
