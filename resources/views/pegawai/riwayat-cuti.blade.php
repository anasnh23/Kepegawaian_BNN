@extends('layouts.template')
@section('content')

{{-- Riwayat Cuti — BNN Premier (polish v3) --}}
{{-- Nuansa BNN: Navy #003366 & Gold #FFC107. Fitur: filter cepat, export CSV dinamis, tabel lengket, status pill dengan tooltip. --}}

@push('styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
@endpush

<style>
  :root{ --bnn-navy:#003366; --bnn-gold:#ffc107; --muted:#6c757d; --surface:#ffffff; }
  body{ background:linear-gradient(180deg,#f8fbff 0%,#f5f8fc 100%); }
  .bnn-hero{ background:linear-gradient(135deg,var(--bnn-navy),#0b4c97); color:#fff; border-radius:18px; padding:18px 22px; }
  .bnn-hero .badge{ background:rgba(255,193,7,.18); color:#ffd866; border:1px solid rgba(255,193,7,.35); }

  .bnn-card{ border:0; border-radius:18px; box-shadow:0 10px 30px rgba(0,0,0,.06); overflow:hidden; }
  .bnn-card .card-header{ background:linear-gradient(135deg,#07325e,#0b447d); color:#fff; border-bottom:3px solid var(--bnn-gold); }

  .bnn-tabs .nav-link{ color:var(--bnn-navy); font-weight:700; border:0; }
  .bnn-tabs .nav-link.active{ color:#fff !important; background:var(--bnn-navy); border-radius:10px; box-shadow:0 6px 18px rgba(0,51,102,.25); }

  /* Filter bar & controls */
  .filter-bar{ background:#fff; border:1px solid #e7edf6; border-radius:12px; padding:12px; }
  .filter-bar .form-control, .filter-bar .custom-select{
    border-radius:10px; border:1px solid #e2e8f0; padding:.6rem .9rem; min-height:2.6rem; line-height:1.25;
  }
  .filter-bar .custom-select{ -webkit-appearance:none; -moz-appearance:none; appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M4 6l4 4 4-4' stroke='%23003366' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right .8rem center; background-size:16px; padding-right:2.2rem;
  }
  input[type=date]::-webkit-calendar-picker-indicator{ padding:.2rem; }

  /* Tabel */
  .table thead th{ position:sticky; top:0; background:#0c3b72; color:#fff; z-index:1; vertical-align:middle; }
  .table thead th:first-child{ border-top-left-radius:8px; }
  .table thead th:last-child{ border-top-right-radius:8px; }
  .table-hover tbody tr:hover{ background:#f7fbff; }
  .status-pill{ display:inline-flex; align-items:center; gap:.35rem; padding:.25rem .55rem; border-radius:999px; font-weight:700; font-size:.85rem; }
  .pill-acc{ background:#e6f6ee; color:#147d3f; }
  .pill-rej{ background:#fdecea; color:#b42318; }
  .pill-pend{ background:#fff6e6; color:#8a5b0a; }
  .pill-info{ background:#eaf2ff; color:#0b4aa0; }

  .empty-state{ text-align:center; padding:30px 14px; color:#5b6b88; }

  .btn-bnn{ background:#198754; border-color:#198754; border-radius:10px; font-weight:800; }
  .btn-bnn-primary{ background:#0552a1; border-color:#0552a1; border-radius:10px; font-weight:800; }
  .btn-soft{ background:#f5f8ff; border:1px solid #dce6f7; color:#0b4aa0; border-radius:10px; font-weight:700; }

  .legend i{ width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:.4rem; }
  .lg-acc{ background:#2ecc71; } .lg-rej{ background:#e74c3c; } .lg-pend{ background:#f39c12; } .lg-info{ background:#3498db; }

  .result-info{ color:#334e77; font-weight:600; }

  @media (max-width: 576px){
    #btnReset{ width:100%; }
  }
</style>

<div class="container-fluid py-3 py-md-4 animate__animated animate__fadeIn">
  {{-- HERO --}}
  <div class="bnn-hero mb-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap">
      <div>
        <span class="badge mb-2">Sistem Kepegawaian BNN</span>
        <h3 class="mb-0">Riwayat Cuti Pegawai</h3>
        <small class="text-white-50">Lacak pengajuan, dokumen pimpinan, dan statusnya secara cepat.</small>
      </div>
      <div class="legend text-white-50 small">
        <span class="mr-3"><i class="lg-acc"></i> Disetujui</span>
        <span class="mr-3"><i class="lg-rej"></i> Ditolak</span>
        <span class="mr-3"><i class="lg-pend"></i> Menunggu</span>
        <span><i class="lg-info"></i> Info</span>
      </div>
    </div>
  </div>

  <div class="card bnn-card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="mb-0"><i class="fas fa-history mr-2"></i> Riwayat</h5>
      <div class="d-flex align-items-center gap-2">
        <span class="result-info mr-3" id="resultInfo">—</span>
        <button class="btn btn-soft" id="btnExportCsv" data-target="#tableAdmin"><i class="fas fa-file-export mr-1"></i> Export CSV</button>
      </div>
    </div>
    <div class="card-body">

      {{-- FILTER BAR --}}
      <div class="filter-bar mb-3">
        <div class="form-row align-items-end">
          <div class="form-group col-md-4">
            <label class="mb-1"><strong>Cari</strong></label>
            <input type="text" class="form-control" id="searchInput" placeholder="Cari nama, NIP, jenis cuti, keterangan…">
          </div>
          <div class="form-group col-md-3">
            <label class="mb-1"><strong>Status</strong></label>
            <select id="statusFilter" class="custom-select">
              <option value="">Semua Status</option>
              <option value="disetujui">Disetujui</option>
              <option value="ditolak">Ditolak</option>
              <option value="menunggu">Menunggu</option>
            </select>
          </div>

          <div class="form-group col-md-4">
            <label class="mb-1"><strong>Rentang Tanggal Pengajuan</strong></label>
            <div class="d-flex align-items-center">
              <input type="date" class="form-control mr-2" id="dateFrom" placeholder="YYYY-MM-DD">
              <span class="mx-1">s.d.</span>
              <input type="date" class="form-control ml-2" id="dateTo" placeholder="YYYY-MM-DD">
            </div>
          </div>

          <div class="form-group col-auto ml-auto">
            <button class="btn btn-bnn" id="btnReset"><i class="fas fa-undo"></i> Reset</button>
          </div>
        </div>
      </div>

      {{-- TABS --}}
      <ul class="nav nav-tabs bnn-tabs mb-3" id="riwayatTab" role="tablist">
        <li class="nav-item"><a class="nav-link active" id="admin-tab" data-toggle="tab" href="#riwayat_admin" role="tab"><i class="fas fa-user-check mr-1"></i> Approval Admin</a></li>
        <li class="nav-item"><a class="nav-link" id="pimpinan-tab" data-toggle="tab" href="#riwayat_pimpinan" role="tab"><i class="fas fa-user-tie mr-1"></i> Persetujuan Pimpinan</a></li>
      </ul>

      <div class="tab-content" id="riwayatTabContent">
        {{-- ADMIN TABLE --}}
        <div class="tab-pane fade show active" id="riwayat_admin" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="tableAdmin">
              <thead>
                <tr class="text-center">
                  <th style="width:60px">No</th>
                  <th>Nama</th>
                  <th>NIP</th>
                  <th>Tgl Pengajuan</th>
                  <th>Jenis</th>
                  <th>Periode</th>
                  <th>Lama</th>
                  <th class="text-left">Keterangan</th>
                  <th>Status</th>
                  <th style="width:120px">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($cuti as $item)
                <tr data-status="{{ strtolower($item->status) }}" data-date="{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('Y-m-d') }}">
                  <td class="text-center">{{ $loop->iteration }}</td>
                  <td>{{ $item->pegawai->nama ?? '-' }}</td>
                  <td>{{ $item->pegawai->nip ?? '-' }}</td>
                  <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
                  <td>{{ $item->jenis_cuti }}</td>
                  <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d-m-Y') }}</td>
                  <td class="text-center">{{ $item->lama_cuti }} hari</td>
                  <td class="text-left">{{ $item->keterangan ?? '-' }}</td>
                  <td class="text-center">
                    @if($item->status == 'Disetujui')
                      <span class="status-pill pill-acc" title="Cuti disetujui"><i class="fas fa-check-circle"></i> Disetujui</span>
                    @elseif($item->status == 'Ditolak')
                      <span class="status-pill pill-rej" title="Cuti ditolak"><i class="fas fa-times-circle"></i> Ditolak</span>
                    @else
                      <span class="status-pill pill-pend" title="Menunggu persetujuan"><i class="fas fa-hourglass-half"></i> Menunggu</span>
                    @endif
                  </td>
                  <td class="text-center">
                    @if($item->status == 'Disetujui')
                      <a href="{{ url('/cuti/cetak/' . $item->id_cuti) }}" target="_blank" class="btn btn-sm btn-bnn-primary"><i class="fas fa-print"></i> Cetak</a>
                    @else
                      <button class="btn btn-sm btn-soft" disabled>-</button>
                    @endif
                  </td>
                </tr>
                @empty
                <tr><td colspan="10"><div class="empty-state"><i class="fas fa-folder-open fa-lg mb-2"></i><div>Belum ada data cuti</div></div></td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        {{-- PIMPINAN TABLE --}}
        <div class="tab-pane fade" id="riwayat_pimpinan" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="tablePimpinan">
              <thead>
                <tr class="text-center">
                  <th style="width:60px">No</th>
                  <th>Nama</th>
                  <th>NIP</th>
                  <th>Jenis</th>
                  <th>Dokumen</th>
                  <th>Status Upload</th>
                  <th>Status Pimpinan</th>
                </tr>
              </thead>
              <tbody>
                @forelse($cuti as $item)
                  @php $approval = $item->approvalPimpinan; @endphp
                  <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->pegawai->nama ?? '-' }}</td>
                    <td>{{ $item->pegawai->nip ?? '-' }}</td>
                    <td>{{ $item->jenis_cuti }}</td>
                    <td class="text-center">
                      @if($approval && $approval->dokumen_path)
                        <a href="{{ asset('storage/' . $approval->dokumen_path) }}" target="_blank" class="btn btn-sm btn-bnn-primary"><i class="fas fa-file-pdf"></i> Lihat</a>
                      @else
                        <span class="status-pill pill-info" title="Belum ada dokumen diupload"><i class="fas fa-info-circle"></i> Belum Upload</span>
                      @endif
                    </td>
                    <td class="text-center">
                      @if($approval && $approval->dokumen_path)
                        <span class="status-pill pill-info" title="Dokumen terkirim"><i class="fas fa-paper-plane"></i> Terkirim</span>
                      @else
                        <span class="status-pill pill-pend" title="Belum dikirim"><i class="fas fa-hourglass-half"></i> Belum</span>
                      @endif
                    </td>
                    <td class="text-center">
                      @if($approval && $approval->status == 'Disetujui')
                        <span class="status-pill pill-acc" title="Disetujui pimpinan"><i class="fas fa-check-circle"></i> Disetujui</span>
                      @elseif($approval && $approval->status == 'Ditolak')
                        <span class="status-pill pill-rej" title="Ditolak pimpinan"><i class="fas fa-times-circle"></i> Ditolak</span>
                      @else
                        <span class="status-pill pill-pend" title="Menunggu keputusan pimpinan"><i class="fas fa-hourglass-half"></i> Menunggu</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="7"><div class="empty-state"><i class="fas fa-folder-open fa-lg mb-2"></i><div>Belum ada data dokumen cuti</div></div></td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script>
  const searchInput = document.getElementById('searchInput');
  const statusFilter = document.getElementById('statusFilter');
  const dateFrom = document.getElementById('dateFrom');
  const dateTo = document.getElementById('dateTo');
  const tableAdmin = document.querySelector('#tableAdmin tbody');
  const resultInfo = document.getElementById('resultInfo');

  function inDateRange(dateStr){
    if(!dateFrom.value && !dateTo.value) return true;
    const d = new Date(dateStr);
    const from = dateFrom.value ? new Date(dateFrom.value) : null;
    const to = dateTo.value ? new Date(dateTo.value) : null;
    if(from && d < from) return false;
    if(to && d > to) return false;
    return true;
  }

  function visibleRowCount(){
    return [...tableAdmin.rows].filter(r => r.style.display !== 'none').length;
  }

  function updateResultInfo(){
    const total = tableAdmin.rows.length;
    const vis = visibleRowCount();
    resultInfo.textContent = vis===total ? `${total} data` : `${vis} / ${total} data`;
  }

  function filterRows(){
    const q = (searchInput.value||'').toLowerCase();
    const st = (statusFilter.value||'').toLowerCase();
    [...tableAdmin.rows].forEach(r=>{
      const text = r.innerText.toLowerCase();
      const rowStatus = (r.getAttribute('data-status')||'').toLowerCase();
      const rowDate = r.getAttribute('data-date') || '';
      const matchText = q==='' || text.includes(q);
      const matchStatus = st==='' || rowStatus===st;
      const matchDate = inDateRange(rowDate);
      r.style.display = (matchText && matchStatus && matchDate) ? '' : 'none';
    });
    updateResultInfo();
  }

  function debounce(fn, ms){ let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn.apply(this,args), ms); }; }
  const debouncedFilter = debounce(filterRows, 180);

  searchInput.addEventListener('input', debouncedFilter);
  statusFilter.addEventListener('change', filterRows);
  dateFrom.addEventListener('change', filterRows);
  dateTo.addEventListener('change', filterRows);
  document.getElementById('btnReset').addEventListener('click', function(e){
    e.preventDefault(); searchInput.value=''; statusFilter.value=''; dateFrom.value=''; dateTo.value=''; filterRows();
  });

  updateResultInfo();

  // Export visible rows to CSV dengan nama file dinamis
  function exportCsv(tableSelector){
    const table = document.querySelector(tableSelector);
    const rows = [...table.querySelectorAll('tr')].filter(r => r.style.display !== 'none');
    const csv = rows.map(row => [...row.children].map(cell => '"'+cell.innerText.replace(/"/g,'""')+'"').join(',')).join('\n');
    const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    const now = new Date().toISOString().slice(0,10);
    a.href = url; a.download = `riwayat-cuti-admin-${now}.csv`;
    a.click();
    URL.revokeObjectURL(url);
  }
  document.getElementById('btnExportCsv').addEventListener('click', function(){ exportCsv('#tableAdmin'); });
</script>
@endpush

@endsection
