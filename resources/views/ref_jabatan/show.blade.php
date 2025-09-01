<div class="modal-header bnn-header text-white">
  <h5 class="modal-title font-weight-bold">
    <i class="fas fa-id-badge mr-2"></i> Detail Jabatan Referensi
  </h5>
  <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<style>
  :root{
    --bnn-navy:#003366; --bnn-blue:#144272; --bnn-gold:#f0ad4e;
    --line:#e6edf6; --soft:#f8fafc;
  }
  .bnn-header{
    background:linear-gradient(135deg,var(--bnn-navy),var(--bnn-blue));
    border-top-left-radius:.5rem; border-top-right-radius:.5rem;
  }
  .info-card{
    background:#fff; border:1px solid var(--line); border-radius:12px;
    box-shadow:0 6px 16px rgba(0,33,72,.08); overflow:hidden;
  }
  .info-card .heading{
    background:linear-gradient(180deg,#f9fbff,#eef4ff);
    border-bottom:1px solid var(--line); font-weight:700;
    padding:.8rem 1rem; letter-spacing:.3px; color:var(--bnn-navy);
  }
  .kv{ display:grid; grid-template-columns:35% 65%; border-bottom:1px dashed #eef2f7; }
  .kv:last-child{ border-bottom:0; }
  .kv .k{ padding:.7rem 1rem; font-weight:600; color:#3a4a66; background:#f7fbff; }
  .kv .v{ padding:.7rem 1rem; color:#22324d; }
  .modal-footer{ border-top:1px solid var(--line); background:#f9fbfd; }
  .btn-bnn-close{ background:#6c757d; color:#fff; font-weight:600; border-radius:8px; }
  .btn-bnn-close:hover{ filter:brightness(1.05); color:#fff; }
</style>

<div class="modal-body" style="background-color: var(--soft);">
  <div class="row">
    <div class="col-md-12">
      <div class="info-card">
        <div class="heading"><i class="fas fa-info-circle mr-1"></i> Informasi Jabatan</div>
        <div>
          <div class="kv">
            <div class="k">Nama Jabatan</div>
            <div class="v">{{ $refJabatan->nama_jabatan }}</div>
          </div>
          <div class="kv">
            <div class="k">Eselon</div>
            <div class="v">{{ $refJabatan->eselon ?? '-' }}</div>
          </div>
          <div class="kv">
            <div class="k">Keterangan</div>
            <div class="v">{{ $refJabatan->keterangan ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal-footer justify-content-end">
  <button type="button" class="btn btn-bnn-close" data-dismiss="modal">
    <i class="fas fa-times-circle"></i> Tutup
  </button>
</div>
