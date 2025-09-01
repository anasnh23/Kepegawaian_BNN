@extends('layouts.template')
@section('title', 'Manajemen Cuti Pegawai')

@section('content')
<style>
  :root{
    --bnn-navy:#003366; --bnn-navy-2:#0b2f5e; --bnn-blue:#144272;
    --bnn-gold:#f0ad4e; --bnn-gold-2:#d89a2b;
    --soft:#f5f8fc; --ink:#0f172a; --line:#e6edf6; --muted:#6b7a8c;
    --green:#28a745; --yellow:#ffc107; --red:#dc3545;
  }

  /* Header BNN */
  .bnn-hero{
    background: linear-gradient(135deg, var(--bnn-navy), #012148 60%, var(--bnn-navy-2));
    color:#fff; border-radius:16px; padding:18px 20px; position:relative; overflow: hidden;
    box-shadow: 0 14px 36px rgba(0,33,72,.22);
  }
  .bnn-hero::after{
    content:""; position:absolute; right:-60px; top:-60px; width:200px; height:200px; opacity:.08;
    background:url('{{ asset('images/bnn-watermark.svg') }}') no-repeat center/contain;
  }
  .bnn-hero h4{ font-weight:800; margin-bottom:2px; }
  .bnn-hero .sub{ color:#dbe7ff; font-size:.95rem; }

  /* Card & Toolbar */
  .bnn-card{ border:1px solid var(--line); border-radius:14px; box-shadow:0 8px 24px rgba(16,24,40,.06); overflow:hidden; }
  .bnn-card .card-header{ background:var(--bnn-navy); color:#fff; font-weight:700; }
  .toolbar-chip{
    border-radius:10px; border:1px solid #dde6f4; background:#fff; padding:.45rem .7rem;
    font-size:.92rem; box-shadow:0 1px 2px rgba(16,24,40,.04);
  }
  .toolbar-chip:focus{ border-color:var(--bnn-gold-2); box-shadow:0 0 0 .18rem rgba(240,173,78,.22); }
  .btn-bnn{
    background:linear-gradient(135deg, var(--bnn-gold), #ffd777);
    color:#172554; border:0; font-weight:800; border-radius:10px;
    box-shadow:0 6px 16px rgba(244,196,48,.35);
  }
  .btn-bnn:hover{ filter:brightness(1.03); color:#172554; }

  /* Tabel */
  .table thead th{ background:#0f1f39; color:#eaf2ff; border-color:#0f1f39; font-weight:700; vertical-align:middle; }
  .table td, .table th{ border-color:#e9eef6; }
  .table-hover tbody tr:hover{ background:#fbfdff; }
  .thead-sticky{ position: sticky; top:0; z-index: 2; }
  .table-wrap{ max-height: 70vh; overflow:auto; border-radius: 10px; }

  /* Badge status */
  .badge-status{ font-weight:700; border-radius:999px; padding:.35rem .7rem; }
  .st-acc{ background:#e7f6ec; color:#146c2e; border:1px solid #bfe5c8; }
  .st-pend{ background:#fff7e6; color:#7a4d00; border:1px solid #ffe0ad; }
  .st-rej{ background:#fdeaea; color:#842029; border:1px solid #f8c2c7; }

  /* Kapsul info */
  .pill{ display:inline-flex; align-items:center; gap:.45rem; background:#fff; border:1px solid #e6edf6;
    border-radius:999px; padding:.28rem .6rem; font-size:.85rem; margin-right:.4rem; }

  /* Modal detail */
  .detail-label{ font-weight:700; color:#334155; width:38%; }
  .detail-val{ color:#0f172a; }
  .kv{ display:flex; border-bottom:1px dashed #e9eef6; padding:.4rem 0; }
</style>

<div class="container-fluid">

  {{-- ======= Header ======= --}}
  <div class="bnn-hero mb-3 d-flex align-items-center justify-content-between">
    <div>
      <h4><i class="fas fa-suitcase mr-2"></i>Manajemen Cuti Pegawai</h4>
      <div class="sub">Setujui, tolak, dan kelola pengajuan cuti dengan efisien</div>
    </div>
    <div class="d-flex">
      <a href="{{ route('dashboard.admin') }}" class="btn btn-bnn btn-sm mr-2">
        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
      </a>
      <button id="btnRefresh" class="btn btn-light btn-sm">
        <i class="fas fa-sync-alt mr-1"></i> Muat Ulang
      </button>
    </div>
  </div>

  {{-- ======= Toolbar ======= --}}
  <div class="card bnn-card mb-3">
    <div class="card-body">
      <div class="form-row align-items-end">
        <div class="col-lg-2 col-md-3 mb-2">
          <label class="mb-1">Status</label>
          <select id="filterStatus" class="form-control form-control-sm toolbar-chip">
            <option value="">Semua</option>
            <option value="Menunggu">Menunggu</option>
            <option value="Disetujui">Disetujui</option>
            <option value="Ditolak">Ditolak</option>
          </select>
        </div>
        <div class="col-lg-2 col-md-3 mb-2">
          <label class="mb-1">Jenis Cuti</label>
          <input type="text" id="filterJenis" class="form-control form-control-sm toolbar-chip" placeholder="Tahunan / Sakit / ...">
        </div>
        <div class="col-lg-3 col-md-4 mb-2">
          <label class="mb-1">Cari</label>
          <input type="text" id="filterText" class="form-control form-control-sm toolbar-chip" placeholder="Nama / Keterangan / Tanggal">
        </div>
        <div class="col-lg-5 mb-2 d-flex align-items-end justify-content-lg-end">
          <div class="pill mr-2"><i class="far fa-clock"></i> Menunggu</div>
          <div class="pill mr-2"><i class="far fa-check-circle text-success"></i> Disetujui</div>
          <div class="pill"><i class="far fa-times-circle text-danger"></i> Ditolak</div>
        </div>
      </div>
    </div>
  </div>

  {{-- ======= Tabel ======= --}}
  <div class="card bnn-card">
    <div class="card-header d-flex align-items-center">
      <h5 class="mb-0"><i class="fas fa-list mr-2"></i> Daftar Pengajuan Cuti</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-wrap">
        <table class="table table-bordered table-striped table-hover text-sm m-0">
          <thead class="thead-sticky text-center">
            <tr>
              <th style="min-width:60px;">No</th>
              <th style="min-width:180px;">Nama Pegawai</th>
              <th style="min-width:140px;">Jenis Cuti</th>
              <th style="min-width:120px;">Pengajuan</th>
              <th style="min-width:220px;">Periode</th>
              <th style="min-width:90px;">Lama</th>
              <th style="min-width:220px;">Keterangan</th>
              <th style="min-width:120px;">Status</th>
              <th style="min-width:140px;">Aksi</th>
            </tr>
          </thead>
          <tbody id="tbodyCuti">
            @forelse($cuti as $item)
            @php
              $status = $item->status ?? 'Menunggu';
              $badge = $status === 'Disetujui' ? 'st-acc' : ($status === 'Ditolak' ? 'st-rej' : 'st-pend');
              $nama = optional($item->pegawai)->nama ?? '-';
              $tglPengajuan = \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y');
              $mulai = \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y');
              $selesai = \Carbon\Carbon::parse($item->tanggal_selesai)->format('d-m-Y');
              $periode = $mulai.' s/d '.$selesai;
              $ket = $item->keterangan ?? '-';
            @endphp
            <tr data-status="{{ $status }}" data-jenis="{{ strtolower($item->jenis_cuti ?? '') }}">
              <td class="text-center">{{ $loop->iteration }}</td>
              <td>
                <a href="javascript:void(0)" class="text-primary font-weight-bold btn-detail"
                   data-nama="{{ $nama }}"
                   data-jenis="{{ $item->jenis_cuti }}"
                   data-pengajuan="{{ $tglPengajuan }}"
                   data-periode="{{ $periode }}"
                   data-lama="{{ $item->lama_cuti }} hari"
                   data-keterangan="{{ $ket }}"
                   data-status="{{ $status }}">
                   {{ $nama }}
                </a>
              </td>
              <td class="text-center">{{ $item->jenis_cuti }}</td>
              <td class="text-center">{{ $tglPengajuan }}</td>
              <td class="text-center">{{ $periode }}</td>
              <td class="text-center">{{ $item->lama_cuti }} hari</td>
              <td>{{ $ket }}</td>
              <td class="text-center">
                <span class="badge-status {{ $badge }}">{{ $status }}</span>
              </td>
              <td class="text-center">
                @if($status === 'Menunggu')
                  <button onclick="setujui({{ $item->id_cuti }})" class="btn btn-sm btn-success mr-1" title="Setujui">
                    <i class="fas fa-check"></i>
                  </button>
                  <button onclick="tolak({{ $item->id_cuti }})" class="btn btn-sm btn-danger mr-1" title="Tolak">
                    <i class="fas fa-times"></i>
                  </button>
                @endif
                <button class="btn btn-sm btn-warning btnEditStatus" data-id="{{ $item->id_cuti }}" data-status="{{ $status }}" title="Ubah Status">
                  <i class="fas fa-pencil-alt"></i>
                </button>
              </td>
            </tr>
            @empty
              <tr><td colspan="9" class="text-center text-muted">Tidak ada data cuti</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- ======= Modal Detail ======= --}}
  <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border:1px solid var(--line); border-radius:14px;">
        <div class="modal-header" style="background:var(--bnn-navy); color:#fff;">
          <h5 class="modal-title"><i class="fas fa-id-badge mr-2"></i>Detail Cuti</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
        </div>
        <div class="modal-body" style="background:#fff;">
          <div class="kv"><div class="detail-label">Nama Pegawai</div><div class="detail-val" id="dNama">-</div></div>
          <div class="kv"><div class="detail-label">Jenis Cuti</div><div class="detail-val" id="dJenis">-</div></div>
          <div class="kv"><div class="detail-label">Tanggal Pengajuan</div><div class="detail-val" id="dPengajuan">-</div></div>
          <div class="kv"><div class="detail-label">Periode</div><div class="detail-val" id="dPeriode">-</div></div>
          <div class="kv"><div class="detail-label">Lama</div><div class="detail-val" id="dLama">-</div></div>
          <div class="kv"><div class="detail-label">Keterangan</div><div class="detail-val" id="dKet">-</div></div>
          <div class="kv"><div class="detail-label">Status</div><div class="detail-val" id="dStatus">-</div></div>
        </div>
        <div class="modal-footer" style="background:#f7f9fc;">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times-circle mr-1"></i> Tutup</button>
        </div>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // ===== Refresh =====
  document.getElementById('btnRefresh')?.addEventListener('click', function(){ location.reload(); });

  // ===== Client-side filters =====
  function applyFilters(){
    const st = (document.getElementById('filterStatus').value || '').toLowerCase();
    const jenis = (document.getElementById('filterJenis').value || '').toLowerCase();
    const text = (document.getElementById('filterText').value || '').toLowerCase();

    const rows = document.querySelectorAll('#tbodyCuti tr');
    rows.forEach(row=>{
      const rowSt = (row.getAttribute('data-status') || '').toLowerCase();
      const rowJenis = (row.getAttribute('data-jenis') || '').toLowerCase();
      const rowText = row.innerText.toLowerCase();

      const okSt = !st || rowSt === st;
      const okJenis = !jenis || rowJenis.includes(jenis);
      const okText = !text || rowText.includes(text);
      row.style.display = (okSt && okJenis && okText) ? '' : 'none';
    });
  }
  ['filterStatus','filterJenis','filterText'].forEach(id=>{
    const el = document.getElementById(id); if (el) el.addEventListener(id==='filterText'?'input':'change', applyFilters);
  });

  // ===== Detail modal (klik nama) =====
  document.querySelectorAll('.btn-detail').forEach(btn=>{
    btn.addEventListener('click', function(){
      document.getElementById('dNama').innerText = this.dataset.nama || '-';
      document.getElementById('dJenis').innerText = this.dataset.jenis || '-';
      document.getElementById('dPengajuan').innerText = this.dataset.pengajuan || '-';
      document.getElementById('dPeriode').innerText = this.dataset.periode || '-';
      document.getElementById('dLama').innerText = this.dataset.lama || '-';
      document.getElementById('dKet').innerText = this.dataset.keterangan || '-';
      document.getElementById('dStatus').innerText = this.dataset.status || '-';
      $('#detailModal').modal('show');
    });
  });

  // ===== Aksi Setujui / Tolak / Ubah Status =====
  function updateStatus(id, status) {
    $.ajax({
      url: "{{ url('/cuti/set-status') }}",
      type: "POST",
      data: { _token: '{{ csrf_token() }}', id: id, status: status },
      success: function(res) {
        Swal.fire('Berhasil', res.message, 'success').then(()=>location.reload());
      },
      error: function(err) {
        Swal.fire('Gagal', err?.responseJSON?.message || 'Terjadi kesalahan.', 'error');
      }
    });
  }
  window.setujui = function(id){
    Swal.fire({ title:'Setujui cuti ini?', icon:'question', showCancelButton:true, confirmButtonText:'Ya, Setujui' })
      .then(r=>{ if(r.isConfirmed) updateStatus(id,'Disetujui'); });
  }
  window.tolak = function(id){
    Swal.fire({ title:'Tolak cuti ini?', icon:'warning', showCancelButton:true, confirmButtonText:'Ya, Tolak' })
      .then(r=>{ if(r.isConfirmed) updateStatus(id,'Ditolak'); });
  }

  $(document).ready(function(){
    $('.btnEditStatus').click(function(){
      const id = $(this).data('id');
      const current = $(this).data('status');
      Swal.fire({
        title: 'Ubah Status Cuti',
        input: 'select',
        inputOptions: { 'Menunggu':'Menunggu','Disetujui':'Disetujui','Ditolak':'Ditolak' },
        inputValue: current,
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        inputLabel: 'Pilih status baru'
      }).then((result)=>{
        if(result.isConfirmed && result.value !== current){
          updateStatus(id, result.value);
        }
      });
    });
  });
</script>
@endpush
@endsection
