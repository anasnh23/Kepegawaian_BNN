@extends('layouts.template')
@section('title', 'Data Presensi Pegawai')

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

  /* Legend */
  .legend .pill{
    display:inline-flex; align-items:center; gap:.45rem; background:#fff; border:1px solid #e6edf6;
    border-radius:999px; padding:.28rem .6rem; font-size:.85rem; margin-right:.4rem;
  }
  .dot{ width:10px; height:10px; border-radius:50%; display:inline-block; }
  .d-green{ background: var(--green); }
  .d-yellow{ background: var(--yellow); }
  .d-red{ background: var(--red); }

  /* Tabel */
  .table thead th{ background:#0f1f39; color:#eaf2ff; border-color:#0f1f39; font-weight:700; vertical-align:middle; }
  .table td, .table th{ border-color:#e9eef6; }
  .table-hover tbody tr:hover{ background:#fbfdff; }
  .thead-sticky{ position: sticky; top:0; z-index: 2; }
  .table-wrap{ max-height: 65vh; overflow:auto; border-radius: 10px; }

  /* Badge status */
  .badge-status{ font-weight:700; border-radius:10px; padding:.35rem .55rem; }
  .badge-hadir{ background:#e7f6ec; color:#146c2e; border:1px solid #bfe5c8; }
  .badge-terlambat{ background:#fff7e6; color:#7a4d00; border:1px solid #ffe0ad; }
  .badge-absen{ background:#fdeaea; color:#842029; border:1px solid #f8c2c7; }

  /* Thumbnail & modal */
  .thumb{ width:48px; height:48px; object-fit:cover; border-radius:8px; border:1px solid #e6edf6; cursor:pointer; }
  .modal-img{ max-width:100%; border-radius:12px; box-shadow:0 12px 28px rgba(0,0,0,.25); }

  /* Small helpers */
  .soft{ background: var(--soft); }
</style>

<div class="container-fluid">

  {{-- ======= Header ======= --}}
  <div class="bnn-hero mb-3 d-flex align-items-center justify-content-between">
    <div>
      <h4><i class="fas fa-calendar-check mr-2"></i>Data Presensi Pegawai</h4>
      <div class="sub">Kelola dan pantau kehadiran pegawai secara presisi</div>
    </div>
    <div>
      <a href="{{ route('dashboard.admin') }}" class="btn btn-bnn btn-sm">
        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
      </a>
    </div>
  </div>

  {{-- ======= Toolbar Filter & Export ======= --}}
  <div class="card bnn-card mb-3">
    <div class="card-body">
      <form id="formFilterExport" method="GET" action="{{ route('presensi.admin') }}">
        <div class="form-row align-items-end">
          <div class="col-lg-2 col-md-3 mb-2">
            <label class="mb-1" for="filter">Jenis Filter</label>
            <select name="filter" id="filter" class="form-control form-control-sm toolbar-chip">
              <option value="harian"   {{ request('filter') == 'harian' ? 'selected' : '' }}>Harian</option>
              <option value="mingguan" {{ request('filter') == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
              <option value="bulanan"  {{ request('filter') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
            </select>
          </div>

          <div class="col-lg-2 col-md-3 mb-2" id="tanggalField">
            <label class="mb-1" for="tanggal">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control form-control-sm toolbar-chip" value="{{ request('tanggal') }}">
          </div>

          <div class="col-lg-2 col-md-3 mb-2" id="mingguField" style="display:none;">
            <label class="mb-1" for="minggu">Mulai Minggu</label>
            <input type="date" name="minggu" id="minggu" class="form-control form-control-sm toolbar-chip" value="{{ request('minggu') }}">
          </div>

          <div class="col-lg-2 col-md-3 mb-2" id="bulanField" style="display:none;">
            <label class="mb-1" for="bulan">Bulan</label>
            <input type="month" name="bulan" id="bulan" class="form-control form-control-sm toolbar-chip" value="{{ request('bulan') }}">
          </div>

          {{-- Quick filter status (client-side) --}}
          <div class="col-lg-2 col-md-3 mb-2">
            <label class="mb-1">Status</label>
            <select id="statusQuick" class="form-control form-control-sm toolbar-chip">
              <option value="">Semua</option>
              <option value="hadir">Hadir</option>
              <option value="terlambat">Terlambat</option>
              <option value="tidak hadir">Tidak Hadir</option>
            </select>
          </div>

          {{-- Pencarian client-side --}}
          <div class="col-lg-2 col-md-3 mb-2">
            <label class="mb-1">Cari</label>
            <input type="text" id="searchText" class="form-control form-control-sm toolbar-chip" placeholder="Nama / Tanggal">
          </div>

          {{-- Export --}}
          <div class="col-lg-2 col-md-3 mb-2 d-flex">
            <div class="btn-group w-100">
              <button type="button" class="btn btn-sm btn-success dropdown-toggle w-100" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-file-export fa-sm"></i> Export
              </button>
              <div class="dropdown-menu dropdown-menu-right w-100">
                <button class="dropdown-item" type="submit" name="export" value="excel">
                  <i class="fas fa-file-excel fa-sm text-success mr-2"></i> Export Excel
                </button>
                <button class="dropdown-item" type="submit" name="export" value="pdf">
                  <i class="fas fa-file-pdf fa-sm text-danger mr-2"></i> Export PDF
                </button>
              </div>
            </div>
          </div>
        </div>

        {{-- Legend & tips --}}
        <div class="mt-3 d-flex flex-wrap align-items-center justify-content-between">
          <div class="legend">
            <span class="pill"><span class="dot d-green"></span> Hadir</span>
            <span class="pill"><span class="dot d-yellow"></span> Terlambat</span>
            <span class="pill"><span class="dot d-red"></span> Tidak Hadir</span>
          </div>
          <small class="text-muted">Tip: ubah <em>Jenis Filter</em> → isi tanggal/bulan → otomatis submit.</small>
        </div>
      </form>
    </div>
  </div>

  {{-- ======= Info Mingguan ======= --}}
  @if(request('filter') === 'mingguan' && request('minggu'))
    @php
      $start = \Carbon\Carbon::parse(request('minggu'))->startOfWeek(\Carbon\Carbon::MONDAY);
      $end = $start->copy()->addDays(4);
    @endphp
    <div class="alert alert-info soft border-0">
      Menampilkan data <strong>{{ $start->translatedFormat('d F Y') }}</strong> s.d. <strong>{{ $end->translatedFormat('d F Y') }}</strong>
    </div>
  @endif

  {{-- ======= Tabel Presensi ======= --}}
  <div class="card bnn-card">
    <div class="card-header d-flex align-items-center">
      <h5 class="mb-0"><i class="fas fa-calendar-check fa-sm mr-2"></i> Data Presensi</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-wrap">
        <table class="table table-bordered table-hover text-sm m-0">
          <thead class="text-center thead-sticky">
            <tr>
              <th style="min-width:60px;">No</th>
              <th style="min-width:180px;">Nama</th>
              <th style="min-width:120px;">Tanggal</th>
              <th style="min-width:120px;">Jam Masuk</th>
              <th style="min-width:110px;">Foto Masuk</th>
              <th style="min-width:120px;">Jam Pulang</th>
              <th style="min-width:110px;">Foto Pulang</th>
              <th style="min-width:120px;">Status</th>
            </tr>
          </thead>
          <tbody id="tbodyPresensi">
            @forelse($data as $i => $row)
            @php
              // Normalisasi status untuk tampilan & filter
              $rawStatus  = strtolower((string)($row->status ?? '-'));
              $statusKey  = str_replace('_',' ', $rawStatus); // contoh "tidak_hadir" -> "tidak hadir"
              $statusKey  = in_array($statusKey, ['hadir','terlambat','tidak hadir']) ? $statusKey : $statusKey;
              $badgeClass = $statusKey === 'hadir' ? 'badge-hadir'
                           : ($statusKey === 'terlambat' ? 'badge-terlambat' : 'badge-absen');
              $statusLabel = $statusKey === 'tidak hadir' ? 'Tidak Hadir' : ucfirst($statusKey);

              $tgl = \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y');
              $nama = $row->user->nama ?? '-';
              $fotoMasuk = $row->foto_masuk ? asset('storage/presensi/'.$row->foto_masuk) : null;
              $fotoPulang = $row->foto_pulang ? asset('storage/presensi/'.$row->foto_pulang) : null;
            @endphp
            <tr data-status="{{ $statusKey }}">
              <td class="text-center">{{ $i+1 }}</td>
              <td>{{ $nama }}</td>
              <td>{{ $tgl }}</td>
              <td class="text-center">{{ $row->jam_masuk ?? '-' }}</td>
              <td class="text-center">
                @if($fotoMasuk)
                  <img src="{{ $fotoMasuk }}" alt="Masuk" class="thumb"
                       data-toggle="modal" data-target="#imgModal"
                       data-img="{{ $fotoMasuk }}" data-title="Foto Masuk - {{ $nama }} ({{ $tgl }})">
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td class="text-center">{{ $row->jam_pulang ?? '-' }}</td>
              <td class="text-center">
                @if($fotoPulang)
                  <img src="{{ $fotoPulang }}" alt="Pulang" class="thumb"
                       data-toggle="modal" data-target="#imgModal"
                       data-img="{{ $fotoPulang }}" data-title="Foto Pulang - {{ $nama }} ({{ $tgl }})">
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td class="text-center">
                <span class="badge-status {{ $badgeClass }}">{{ $statusLabel }}</span>
              </td>
            </tr>
            @empty
              <tr><td colspan="8" class="text-center text-muted">Tidak ada data presensi.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- ======= Modal Preview Gambar ======= --}}
  <div class="modal fade" id="imgModal" tabindex="-1" aria-labelledby="imgModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content" style="border:0; background:transparent;">
        <div class="modal-body text-center">
          <img src="" alt="Preview" class="modal-img" id="previewImg">
          <div class="mt-2 p-2" style="background:#ffffff; border-radius:10px; display:inline-block; box-shadow:0 8px 24px rgba(16,24,40,.08)">
            <small id="previewTitle" class="text-muted"></small>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
  $(document).ready(function () {
    const form = $('#formFilterExport');

    function toggleFields() {
      let val = $('#filter').val();
      // jangan reset nilai ketika load ulang (agar tetap terlihat nilai yang dipilih)
      $('#tanggalField, #mingguField, #bulanField').hide();

      if (val === 'harian') {
        $('#tanggalField').show();
      } else if (val === 'mingguan') {
        $('#mingguField').show();
      } else if (val === 'bulanan') {
        $('#bulanField').show();
      }
    }

    // Saat jenis filter berubah
    $('#filter').on('change', function () {
      // reset value saat user mengganti tipe (biar tidak rancu)
      $('#tanggal, #minggu, #bulan').val('');
      toggleFields();
    });

    // Auto-submit ketika input tanggal berubah sesuai jenis filter aktif
    $('#tanggal, #minggu, #bulan').on('change', function () {
      const filter = $('#filter').val();
      if (filter === 'harian'   && $('#tanggal').val()) form.submit();
      if (filter === 'mingguan' && $('#minggu').val())  form.submit();
      if (filter === 'bulanan'  && $('#bulan').val())   form.submit();
    });

    // Inisialisasi saat pertama load
    toggleFields();

    // ===== Client-side quick filter status & search =====
    const $rows = $('#tbodyPresensi tr');
    function applyClientFilters(){
      const status = ($('#statusQuick').val() || '').toLowerCase();
      const text = ($('#searchText').val() || '').toLowerCase();

      $rows.each(function(){
        const row = $(this);
        const rowStatus = (row.data('status') || '').toLowerCase();
        const rowText = row.text().toLowerCase();
        const okStatus = !status || rowStatus === status;
        const okText = !text || rowText.includes(text);
        row.toggle(okStatus && okText);
      });
    }
    $('#statusQuick').on('change', applyClientFilters);
    $('#searchText').on('input', applyClientFilters);

    // ===== Preview modal untuk foto =====
    $('#imgModal').on('show.bs.modal', function (evt) {
      const img = $(evt.relatedTarget);
      $('#previewImg').attr('src', img.data('img') || '');
      $('#previewTitle').text(img.data('title') || '');
    });
  });
</script>
@endpush
@endsection
