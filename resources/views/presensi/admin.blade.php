@extends('layouts.template')
@section('title', 'Data Presensi Pegawai')

@section('content')
<style>
  :root{
    --bnn-navy:#003366; --bnn-navy-2:#0b2f5e;
    --bnn-gold:#f0ad4e; --bnn-gold-2:#d89a2b;
    --soft:#f5f8fc; --line:#e6edf6;
    --green:#28a745; --yellow:#ffc107; --red:#dc3545;
  }

  /* Header BNN */
  .bnn-hero{
    background: linear-gradient(135deg, var(--bnn-navy), #012148 60%, var(--bnn-navy-2));
    color:#fff; border-radius:16px; padding:18px 20px;
    position:relative; overflow:hidden;
    box-shadow:0 14px 36px rgba(0,33,72,.22);
    z-index:1;
  }
  .bnn-hero::after{
    content:""; position:absolute; right:-60px; top:-60px;
    width:200px; height:200px; opacity:.08;
    background:url('{{ asset('images/bnn-watermark.svg') }}') no-repeat center/contain;
    z-index:0;
  }
  .bnn-hero h4{ font-weight:800; margin-bottom:2px; }
  .bnn-hero .sub{ color:#dbe7ff; font-size:.95rem; }
  .bnn-hero .btn{ z-index:2; position:relative; }

  /* Card */
  .bnn-card{ border:1px solid var(--line); border-radius:14px; box-shadow:0 8px 24px rgba(16,24,40,.06); overflow:hidden; }
  .bnn-card .card-header{ background:var(--bnn-navy); color:#fff; font-weight:700; }

  /* Table */
  .table-wrap{ max-height:65vh; overflow:auto; border-radius:10px; }
  .table{ min-width:1150px; table-layout:auto; }
  .table thead th{ background:#0f1f39; color:#eaf2ff; border-color:#0f1f39; position:sticky; top:0; z-index:2; }
  .table td, .table th{ border-color:#e9eef6; vertical-align:middle; }
  .table-hover tbody tr:hover{ background:#fbfdff; }

  /* Kolom lebar tetap agar rapi */
  th:nth-child(8), td:nth-child(8){ width:130px; } /* Status */
  th:nth-child(5), td:nth-child(5),
  th:nth-child(7), td:nth-child(7){ width:100px; } /* Foto */

  /* Badge status: anti wrap, selalu satu baris */
  .badge-status{
    display:inline-flex; align-items:center; justify-content:center;
    white-space:nowrap; line-height:1; font-weight:700;
    border-radius:999px; padding:.45rem .65rem; min-width:110px;
    font-size:.85rem;
  }
  .badge-hadir{ background:#e7f6ec; color:#146c2e; border:1px solid #bfe5c8; }
  .badge-terlambat{ background:#fff7e6; color:#7a4d00; border:1px solid #ffe0ad; }
  .badge-absen{ background:#fdeaea; color:#842029; border:1px solid #f8c2c7; }

  /* Responsif: kecilkan badge di layar sempit */
  @media (max-width: 992px){
    .badge-status{ min-width:96px; font-size:.8rem; padding:.35rem .55rem; }
    .table{ min-width:1000px; }
  }
  @media (max-width: 576px){
    .badge-status{ min-width:86px; font-size:.78rem; }
  }

  /* Thumb */
  .thumb{ width:48px; height:48px; object-fit:cover; border-radius:8px; border:1px solid #e6edf6; cursor:pointer; }
  .modal-img{ max-width:100%; border-radius:12px; box-shadow:0 12px 28px rgba(0,0,0,.25); }

  /* Soft bg */
  .soft{ background:var(--soft); }
</style>

<div class="container-fluid">

  {{-- Header --}}
  <div class="bnn-hero mb-3 d-flex align-items-center justify-content-between">
    <div>
      <h4><i class="fas fa-calendar-check mr-2"></i>Data Presensi Pegawai</h4>
      <div class="sub">Kelola dan pantau kehadiran pegawai secara presisi</div>
    </div>
    <div>
      <a href="{{ url('/dashboard-admin') }}" class="btn btn-warning btn-sm font-weight-bold">
        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
      </a>
    </div>
  </div>

  {{-- Toolbar Filter --}}
  <div class="card bnn-card mb-3">
    <div class="card-body">
      <form id="formFilterExport" method="GET" action="{{ route('presensi.admin') }}">
        <div class="row g-2 align-items-end">
          <div class="col-md-2">
            <label for="filter">Jenis Filter</label>
            <select name="filter" id="filter" class="form-control form-control-sm">
              <option value="harian"   {{ request('filter') == 'harian' ? 'selected' : '' }}>Harian</option>
              <option value="mingguan" {{ request('filter') == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
              <option value="bulanan"  {{ request('filter') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
            </select>
          </div>
          <div class="col-md-2" id="tanggalField">
            <label for="tanggal">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control form-control-sm" value="{{ request('tanggal') }}">
          </div>
          <div class="col-md-2" id="mingguField" style="display:none;">
            <label for="minggu">Mulai Minggu</label>
            <input type="date" name="minggu" id="minggu" class="form-control form-control-sm" value="{{ request('minggu') }}">
          </div>
          <div class="col-md-2" id="bulanField" style="display:none;">
            <label for="bulan">Bulan</label>
            <input type="month" name="bulan" id="bulan" class="form-control form-control-sm" value="{{ request('bulan') }}">
          </div>
          <div class="col-md-2">
            <label>Status</label>
            <select id="statusQuick" class="form-control form-control-sm">
              <option value="">Semua</option>
              <option value="hadir">Hadir</option>
              <option value="terlambat">Terlambat</option>
              <option value="tidak hadir">Tidak Hadir</option>
            </select>
          </div>
          <div class="col-md-2">
            <label>Cari</label>
            <input type="text" id="searchText" class="form-control form-control-sm" placeholder="Nama / Tanggal / Lokasi">
          </div>
          <div class="col-md-2 d-flex mt-2 mt-md-0">
            <div class="btn-group w-100">
              <button type="button" class="btn btn-sm btn-success dropdown-toggle w-100" data-toggle="dropdown">
                <i class="fas fa-file-export fa-sm"></i> Export
              </button>
              <div class="dropdown-menu dropdown-menu-right w-100">
                <button class="dropdown-item" type="submit" name="export" value="excel">
                  <i class="fas fa-file-excel text-success mr-2"></i> Export Excel
                </button>
                <button class="dropdown-item" type="submit" name="export" value="pdf">
                  <i class="fas fa-file-pdf text-danger mr-2"></i> Export PDF
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Info Mingguan --}}
  @if(request('filter') === 'mingguan' && request('minggu'))
    @php
      $start = \Carbon\Carbon::parse(request('minggu'))->startOfWeek(\Carbon\Carbon::MONDAY);
      $end = $start->copy()->addDays(4);
    @endphp
    <div class="alert alert-info soft border-0">
      Menampilkan data <strong>{{ $start->translatedFormat('d F Y') }}</strong>
      s.d. <strong>{{ $end->translatedFormat('d F Y') }}</strong>
    </div>
  @endif

  {{-- Tabel --}}
  <div class="card bnn-card">
    <div class="card-header d-flex align-items-center">
      <h5 class="mb-0"><i class="fas fa-calendar-check fa-sm mr-2"></i> Data Presensi</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-wrap">
        <table class="table table-bordered table-hover text-sm m-0">
          <thead class="text-center">
            <tr>
              <th style="width:60px">No</th>
              <th>Nama</th>
              <th style="width:120px">Tanggal</th>
              <th style="width:110px">Jam Masuk</th>
              <th>Foto Masuk</th>
              <th style="width:110px">Jam Pulang</th>
              <th>Foto Pulang</th>
              <th style="width:130px">Status</th>
              <th>Lokasi</th>
            </tr>
          </thead>
          <tbody id="tbodyPresensi">
            @forelse($data as $i => $row)
              @php
                $statusKey   = str_replace('_',' ', strtolower($row->status ?? '-'));
                $badgeClass  = $statusKey === 'hadir' ? 'badge-hadir'
                              : ($statusKey === 'terlambat' ? 'badge-terlambat' : 'badge-absen');
                $statusLabel = $statusKey === 'tidak hadir' ? 'Tidak Hadir' : ucfirst($statusKey);
                $tgl         = \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y');
                $nama        = $row->user->nama ?? '-';
                $fotoMasuk   = $row->foto_masuk_url;
                $fotoPulang  = $row->foto_pulang_url;
              @endphp
              <tr data-status="{{ $statusKey }}">
                <td class="text-center">{{ method_exists($data, 'firstItem') ? $data->firstItem() + $i : $i+1 }}</td>
                <td>{{ e($nama) }}</td>
                <td>{{ $tgl }}</td>
                <td class="text-center">{{ $row->jam_masuk ?? '-' }}</td>
                <td class="text-center" style="width:90px">
                  @if($fotoMasuk)
                    <img src="{{ $fotoMasuk }}" class="thumb"
                         alt="Foto Masuk {{ e($nama) }}"
                         data-toggle="modal" data-target="#imgModal"
                         data-img="{{ $fotoMasuk }}" data-title="Foto Masuk - {{ $nama }} ({{ $tgl }})">
                  @else <span class="text-muted">-</span> @endif
                </td>
                <td class="text-center">{{ $row->jam_pulang ?? '-' }}</td>
                <td class="text-center" style="width:90px">
                  @if($fotoPulang)
                    <img src="{{ $fotoPulang }}" class="thumb"
                         alt="Foto Pulang {{ e($nama) }}"
                         data-toggle="modal" data-target="#imgModal"
                         data-img="{{ $fotoPulang }}" data-title="Foto Pulang - {{ $nama }} ({{ $tgl }})">
                  @else <span class="text-muted">-</span> @endif
                </td>
                <td class="text-center">
                  <span class="badge-status {{ $badgeClass }}">{{ $statusLabel }}</span>
                </td>
                <td>
                  @if($row->lokasi)
                    {{ $row->lokasi }}
                  @elseif($row->lat_masuk && $row->long_masuk)
                    {{ $row->lat_masuk }}, {{ $row->long_masuk }}
                  @else - @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="9" class="text-center text-muted">Tidak ada data presensi.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      @if(method_exists($data, 'links'))
      <div class="d-flex flex-wrap justify-content-between align-items-center px-3 py-2 border-top" style="gap:.5rem">
        <div class="text-muted small mb-2 mb-md-0">
          @if($data->total() > 0)
            Menampilkan <strong>{{ $data->firstItem() }}</strong>–<strong>{{ $data->lastItem() }}</strong>
            dari <strong>{{ $data->total() }}</strong> data
          @else
            Tidak ada data
          @endif
        </div>
        <div class="d-flex align-items-center" style="gap:.5rem">
          {{-- Prev / Next ringkas --}}
          <a href="{{ $data->previousPageUrl() ?: 'javascript:void(0)' }}"
             class="btn btn-sm btn-outline-secondary {{ $data->onFirstPage() ? 'disabled' : '' }}">
            ‹ Sebelumnya
          </a>
          <a href="{{ $data->nextPageUrl() ?: 'javascript:void(0)' }}"
             class="btn btn-sm btn-outline-secondary {{ $data->hasMorePages() ? '' : 'disabled' }}">
            Berikutnya ›
          </a>
        </div>
      </div>
      <div class="px-3 pb-3">
        {{-- Links Laravel + bawa query filter --}}
        {{ $data->onEachSide(1)->withQueryString()->links() }}
      </div>
      @endif
    </div>
  </div>

  {{-- Modal Preview Gambar --}}
  <div class="modal fade" id="imgModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content" style="border:0; background:transparent;">
        <div class="modal-body text-center">
          <img src="" class="modal-img" id="previewImg" alt="Preview">
          <div class="mt-2 p-2 bg-white rounded"
               style="display:inline-block; box-shadow:0 8px 24px rgba(16,24,40,.08)">
            <small id="previewTitle" class="text-muted"></small>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
$(function(){
  const form = $('#formFilterExport');

  function toggleFields(){
    let val = $('#filter').val();
    $('#tanggalField,#mingguField,#bulanField').hide();
    if(val==='harian') $('#tanggalField').show();
    if(val==='mingguan') $('#mingguField').show();
    if(val==='bulanan') $('#bulanField').show();
  }

  $('#filter').on('change', function(){
    $('#tanggal,#minggu,#bulan').val('');
    toggleFields();
  });

  $('#tanggal,#minggu,#bulan').on('change', function(){
    const f = $('#filter').val();
    if(f==='harian' && $('#tanggal').val()) form.submit();
    if(f==='mingguan'&& $('#minggu').val()) form.submit();
    if(f==='bulanan' && $('#bulan').val()) form.submit();
  });

  toggleFields();

  // Quick filter (hanya untuk baris di halaman saat ini)
  function applyFilters(){
    const s = ($('#statusQuick').val()||'').toLowerCase();
    const t = ($('#searchText').val()||'').toLowerCase();
    $('#tbodyPresensi tr').each(function(){
      const r = $(this);
      const rs = (r.data('status')||'').toLowerCase();
      const rt = r.text().toLowerCase();
      r.toggle((!s||rs===s) && (!t||rt.includes(t)));
    });
  }
  $('#statusQuick').on('change', applyFilters);
  $('#searchText').on('input', applyFilters);

  // Preview modal
  $('#imgModal').on('show.bs.modal', function(evt){
    const img = $(evt.relatedTarget);
    $('#previewImg').attr('src', img.data('img')||'');
    $('#previewTitle').text(img.data('title')||'');
  });
});
</script>
@endpush
@endsection
