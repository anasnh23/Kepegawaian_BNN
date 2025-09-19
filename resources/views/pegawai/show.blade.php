{{-- resources/views/pegawai/show.blade.php --}}
@php
    use Carbon\Carbon;

    $foto         = $pegawai->foto ? asset('storage/'.$pegawai->foto) : asset('images/default.png');
    $nama         = data_get($pegawai, 'nama', '-');
    $nip          = data_get($pegawai, 'nip', '-');
    $email        = data_get($pegawai, 'email', '-');
    $username     = data_get($pegawai, 'username', '-');
    $jk           = data_get($pegawai, 'jenis_kelamin', null) === 'L' ? 'Laki-laki' : (data_get($pegawai, 'jenis_kelamin') ? 'Perempuan' : '-');
    $agama        = data_get($pegawai, 'agama', '-');
    $nohp         = data_get($pegawai, 'no_tlp', '-');

    $pendidikan   = data_get($pegawai, 'pendidikan.jenis_pendidikan', null);
    $tahunLulus   = data_get($pegawai, 'pendidikan.tahun_kelulusan', null);

    $levelName    = data_get($pegawai, 'level.level_name', 'Level Tidak Diketahui');

    $namaJabatan  = data_get($pegawai, 'jabatan.refJabatan.nama_jabatan', null);
    $tmtJabatan   = data_get($pegawai, 'jabatan.tmt', null);
    $tmtJabatanF  = $tmtJabatan ? Carbon::parse($tmtJabatan)->format('d-m-Y') : '-';

    $golPangkat   = data_get($pegawai, 'pangkat.refPangkat.golongan_pangkat', null);
    $tmtPangkat   = data_get($pegawai, 'pangkat.tmt', null);
    $tmtPangkatF  = $tmtPangkat ? Carbon::parse($tmtPangkat)->format('d-m-Y') : '-';

    // ===== Gaji Pokok =====
    $gajiPokok    = data_get($pegawai, 'gaji_pokok', null);
    $gajiPokokF   = is_null($gajiPokok) ? '-' : ('Rp '.number_format((int)$gajiPokok, 0, ',', '.'));
@endphp

<style>
    /* ===== THEME BNN ===== */
    :root{
        --bnn-navy:#003366;
        --bnn-navy-2:#0b2f5e;
        --bnn-gold:#f0ad4e;
        --bnn-gold-deep:#d9952b;
        --bnn-soft:#f5f8fc;
    }
    .bnn-header{
        background: linear-gradient(135deg, var(--bnn-navy) 0%, #012148 60%, var(--bnn-navy-2) 100%);
        color:#fff; position:relative; overflow:hidden;
        border-top-left-radius:.5rem; border-top-right-radius:.5rem;
    }
    .bnn-header::after{
        content:""; position:absolute; right:-60px; top:-60px; width:180px; height:180px; opacity:.07;
        background:url('{{ asset('images/bnn-watermark.svg') }}') no-repeat center/contain;
        pointer-events:none;
    }
    .bnn-title{ font-weight:700; letter-spacing:.3px; text-shadow:0 2px 6px rgba(0,0,0,.25); }
    .modal-body.bnn-body{ background:var(--bnn-soft); }
    .profile-card{
        background:#fff; border:1px solid #e9eef6; border-radius:1rem; padding:1rem;
        box-shadow:0 6px 20px rgba(0,33,72,.06);
    }
    .avatar-ring{
        --ring: 3px;
        box-shadow: 0 0 0 var(--ring) #fff, 0 0 0 calc(var(--ring) + 2px) var(--bnn-gold);
    }
    .chip{
        display:inline-flex; align-items:center; gap:.4rem;
        background: #fff7e9; color:#7a4d00;
        border:1px solid #ffe0ad; border-radius:999px; padding:.25rem .6rem; font-weight:600; font-size:.8rem;
    }
    .chip-mint{
        background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0;
    }
    .bnn-badge{
        background:linear-gradient(180deg, var(--bnn-gold), var(--bnn-gold-deep));
        color:#1d1d1d; font-weight:700; border:0; border-radius:.6rem; padding:.35rem .6rem;
    }
    .info-card{
        background:#fff; border:1px solid #e9eef6; border-radius:1rem; overflow:hidden;
        box-shadow:0 6px 20px rgba(0,33,72,.06);
    }
    .info-card .heading{
        background:linear-gradient(180deg,#f9fbff,#eef4ff);
        border-bottom:1px solid #e9eef6; font-weight:700;
        padding:.8rem 1rem; letter-spacing:.2px; color:#0b2f5e;
    }
    .kv{
        display:grid; grid-template-columns: 42% 58%;
        gap:0; border-bottom:1px dashed #eef2f7;
    }
    .kv:nth-child(even){ background:#fcfdff; }
    .kv .k{ padding:.65rem 1rem; font-weight:600; color:#3a4a66; }
    .kv .v{ padding:.65rem 1rem; color:#22324d; }
    .kv .v .badge{ transform: translateY(-1px); }

    .nav-pills.bnn .nav-link{
        border-radius: .8rem; font-weight:700; color:#244266; border:1px solid transparent;
    }
    .nav-pills.bnn .nav-link.active{
        background:linear-gradient(135deg,var(--bnn-navy),#1a3d73);
        color:#fff; box-shadow:0 8px 18px rgba(0,33,72,.25);
        border-color:#0d2c55;
    }
    .btn-bnn{
        background:linear-gradient(135deg,var(--bnn-navy),#1a3d73);
        color:#fff; border:0; font-weight:700; letter-spacing:.2px;
    }
    .btn-ghost{
        background:#fff; border:1px solid #e1e7f0; color:#1d3b66; font-weight:700;
    }
    .fade-in{ animation: fadeIn .4s ease both; }
    @keyframes fadeIn{ from{opacity:0; transform: translateY(6px);} to{opacity:1; transform:none;} }
    @media (max-width:575.98px){
        .kv{ grid-template-columns: 100%; }
        .kv .k{ background:#f7fbff; border-bottom:1px dashed #eef2f7; }
    }

    /* ====== PRINT OVERRIDES ====== */
    @media print {
      @page { size: A4; margin: 14mm 12mm; }
      html, body { background:#fff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }

      .modal-header, .modal-footer,
      [data-toggle="tooltip"], .tooltip { display:none !important; }

      #bnnPrintable { box-shadow:none !important; background:#fff !important; }
      .profile-card, .info-card { box-shadow:none !important; border-color:#d9e1ef !important; }
      .info-card .heading { background:#eef3ff !important; }

      .bnn-header::after { display:none !important; }

      img { max-width:100% !important; filter:none !important; }

      .tab-content .tab-pane { display:block !important; opacity:1 !important; visibility:visible !important; }
      .nav-pills, .nav-tabs { display:none !important; list-style:none !important; }

      .badge, .bnn-badge, .chip { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; box-shadow:none !important; }

      .kv { grid-template-columns: 38% 62% !important; border-bottom:1px solid #eef2f7 !important; }
      .kv .k { background:#f6f8ff !important; }
    }

    body.print-mode .modal-header,
    body.print-mode .modal-footer,
    body.print-mode [data-toggle="tooltip"]{ display:none !important; }
    body.print-mode .tab-content .tab-pane{ display:block !important; opacity:1 !important; }
    body.print-mode .nav-pills, body.print-mode .nav-tabs{ display:none !important; }
</style>

<div class="modal-header bnn-header">
    <h5 class="modal-title bnn-title">
        <i class="fas fa-id-badge mr-2"></i> Detail Profil Pegawai
    </h5>
    <div class="d-flex align-items-center">
        <button type="button" class="btn btn-sm btn-ghost mr-2" onclick="bnnCopyNIP('{{ $nip }}')" data-toggle="tooltip" title="Salin NIP">
            <i class="fas fa-copy"></i>
        </button>
        <button type="button" class="btn btn-sm btn-bnn mr-2" onclick="bnnPrintSection()" data-toggle="tooltip" title="Cetak">
            <i class="fas fa-print"></i>
        </button>
        <button type="button" class="close text-white ml-2" data-dismiss="modal" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>

<div class="modal-body bnn-body">
    <div id="bnnPrintable" class="fade-in">
        <div class="row">
            {{-- Sidebar Profil --}}
            <div class="col-lg-4 mb-3">
                <div class="profile-card h-100 text-center">
                    <img src="{{ $foto }}" alt="Foto {{ $nama }}"
                         class="rounded-circle avatar-ring shadow-sm mb-3" width="140" height="140" style="object-fit:cover;">
                    <h5 class="mb-1 text-dark" style="font-weight:800">{{ $nama }}</h5>
                    <div class="text-muted small mb-2" id="nipText">{{ $nip }}</div>

                    <div class="mb-2">
                        <span class="bnn-badge" data-toggle="tooltip" title="Level Pengguna">
                            <i class="fas fa-shield-alt mr-1"></i>{{ $levelName }}
                        </span>
                    </div>

                    {{-- Ringkasan gaji (chip kecil) --}}
                    <div class="mb-3">
                        <span class="chip chip-mint" data-toggle="tooltip" title="Gaji Pokok Saat Ini">
                            <i class="fas fa-money-bill-wave"></i>
                            {{ $gajiPokokF }}
                        </span>
                    </div>

                    <div class="text-left">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="chip"><i class="fas fa-briefcase"></i> {{ $namaJabatan ?? '-' }}</span>
                            <span class="badge badge-success badge-pill">{{ $golPangkat ?? '-' }}</span>
                        </div>
                        <div class="small text-muted">
                            <i class="far fa-calendar-check"></i> TMT Jabatan: <strong>{{ $tmtJabatanF }}</strong><br>
                            <i class="far fa-calendar-check"></i> TMT Pangkat: <strong>{{ $tmtPangkatF }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Konten Detail --}}
            <div class="col-lg-8">
                <ul class="nav nav-pills bnn mb-3" id="bnnTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-profil" data-toggle="pill" href="#pane-profil" role="tab">
                            <i class="fas fa-user mr-1"></i> Informasi Dasar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-kedinasan" data-toggle="pill" href="#pane-kedinasan" role="tab">
                            <i class="fas fa-landmark mr-1"></i> Kedinasan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-kontak" data-toggle="pill" href="#pane-kontak" role="tab">
                            <i class="fas fa-envelope mr-1"></i> Kontak
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{-- Pane Profil --}}
                    <div class="tab-pane fade show active" id="pane-profil" role="tabpanel" aria-labelledby="tab-profil">
                        <div class="info-card">
                            <div class="heading"><i class="fas fa-id-card mr-1"></i> Data Pribadi</div>
                            <div>
                                <div class="kv"><div class="k">Nama Lengkap</div><div class="v">{{ $nama }}</div></div>
                                <div class="kv"><div class="k">NIP</div><div class="v">{{ $nip }}</div></div>
                                <div class="kv"><div class="k">Jenis Kelamin</div><div class="v">{{ $jk }}</div></div>
                                <div class="kv"><div class="k">Agama</div><div class="v">{{ $agama }}</div></div>
                                <div class="kv"><div class="k">Username</div><div class="v">{{ $username }}</div></div>
                            </div>
                        </div>
                    </div>

                    {{-- Pane Kedinasan --}}
                    <div class="tab-pane fade" id="pane-kedinasan" role="tabpanel" aria-labelledby="tab-kedinasan">
                        <div class="info-card">
                            <div class="heading"><i class="fas fa-landmark mr-1"></i> Informasi Kedinasan</div>
                            <div>
                                <div class="kv">
                                    <div class="k">Jabatan</div>
                                    <div class="v">
                                        @if($namaJabatan)
                                            <span class="badge badge-info badge-pill px-2 py-1">{{ $namaJabatan }}</span>
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                                <div class="kv"><div class="k">TMT Jabatan</div><div class="v">{{ $tmtJabatanF }}</div></div>
                                <div class="kv">
                                    <div class="k">Pangkat</div>
                                    <div class="v">
                                        @if($golPangkat)
                                            <span class="badge badge-success badge-pill px-2 py-1">{{ $golPangkat }}</span>
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                                <div class="kv"><div class="k">TMT Pangkat</div><div class="v">{{ $tmtPangkatF }}</div></div>
                                <div class="kv">
                                    <div class="k">Level Akses</div>
                                    <div class="v"><span class="bnn-badge">{{ $levelName }}</span></div>
                                </div>
                                {{-- ====== Gaji Pokok (baru) ====== --}}
                                <div class="kv">
                                    <div class="k">Gaji Pokok</div>
                                    <div class="v">
                                        @if($gajiPokokF !== '-')
                                            <span class="badge badge-primary px-2 py-1">{{ $gajiPokokF }}</span>
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pane Kontak --}}
                    <div class="tab-pane fade" id="pane-kontak" role="tabpanel" aria-labelledby="tab-kontak">
                        <div class="info-card">
                            <div class="heading"><i class="fas fa-address-book mr-1"></i> Kontak & Pendidikan</div>
                            <div>
                                <div class="kv"><div class="k">Email</div><div class="v">{{ $email }}</div></div>
                                <div class="kv"><div class="k">No. HP</div><div class="v">{{ $nohp }}</div></div>
                                <div class="kv">
                                    <div class="k">Pendidikan</div>
                                    <div class="v">
                                        @if($pendidikan)
                                            {{ $pendidikan }}
                                            @if($tahunLulus)
                                                <span class="text-muted">({{ $tahunLulus }})</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>{{-- /tab-content --}}
            </div>
        </div>
    </div>
</div>

<div class="modal-footer justify-content-between" style="background:#f7f9fc; border-top:1px solid #e9eef6;">
    <small class="text-muted"><i class="far fa-check-circle"></i> Detail</small>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-times-circle"></i> Tutup
    </button>
</div>

<script>
    // Tooltip & tab init
    $(function(){
        $('[data-toggle="tooltip"]').tooltip({ boundary: 'window' });
        $('#bnnTabs a[data-toggle="pill"]').on('shown.bs.tab', function(){ window.dispatchEvent(new Event('resize')); });
    });

    // Salin NIP ke clipboard
    function bnnCopyNIP(nip){
        if(!nip) return;
        const ta = document.createElement('textarea');
        ta.value = nip; document.body.appendChild(ta); ta.select();
        try{
            document.execCommand('copy');
            $('.btn-ghost,[data-toggle="tooltip"][title="Salin NIP"]').tooltip('hide')
                .attr('data-original-title', 'Tersalin!').tooltip('show');
            setTimeout(()=>{$('.btn-ghost').tooltip('hide').attr('data-original-title','Salin NIP');},1200);
        }catch(e){}
        document.body.removeChild(ta);
    }

    // Cetak dengan @media print (tanpa membuka jendela baru)
    function bnnPrintSection(){
        document.body.classList.add('print-mode');
        // Pastikan semua pane terlihat saat print
        var panes = document.querySelectorAll('.tab-content .tab-pane');
        panes.forEach(p => { p.classList.add('active','show'); });

        window.print();

        // Kembalikan state setelah print
        setTimeout(function(){
            document.body.classList.remove('print-mode');
            panes.forEach(p => { p.classList.remove('active','show'); });
            var first = document.querySelector('#pane-profil');
            if (first) first.classList.add('active','show');
        }, 300);
    }
</script>
