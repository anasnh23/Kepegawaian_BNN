@extends('layouts.template')
@section('title', 'Profil Saya')

@section('content')
<style>
    :root{
        --bnn-navy:#0a2647;
        --bnn-blue:#144272;
        --bnn-cyan:#2c74b3;
        --bnn-gold:#f4c430;
        --ink:#0f172a;
        --muted:#667085;
        --glass: rgba(255,255,255,.10);
        --stroke: rgba(255,255,255,.28);
    }

    /* =========================
       FIX “BERCAK PUTIH” SIDEBAR
       ========================= */
    .main-sidebar{ z-index:1050; position:relative; }
    .content-wrapper{ position:relative; z-index:1; }

    .nav-sidebar .nav-link .right,
    .nav-sidebar .nav-link .badge,
    .nav-sidebar .nav-link .float-right{
        background: transparent !important;
        box-shadow: none !important;
    }
    .nav-sidebar .nav-link{
        overflow:hidden; white-space:nowrap; text-overflow:ellipsis;
    }

    .stage *{ box-sizing:border-box; }

    /* ====== Latar aurora ====== */
    .stage{
        position:relative; padding:28px 16px; overflow:hidden;
        background:#071a2f;
        isolation:isolate;
    }
    .aurora, .aurora::before, .aurora::after{
        position:absolute; inset:auto; filter:blur(40px); opacity:.55; pointer-events:none;
        border-radius:50%;
        animation: floaty 18s ease-in-out infinite;
        mix-blend-mode:screen;
    }
    .aurora{ width:820px; height:820px; left:-220px; top:-180px; background:radial-gradient(circle, #3bc9ff, transparent 60%); }
    .aurora::before{ content:""; width:680px; height:680px; right:-180px; top:-140px; background:radial-gradient(circle, #60a5fa, transparent 60%); animation-delay: -6s;}
    .aurora::after{ content:""; width:760px; height:760px; left:40%; bottom:-260px; background:radial-gradient(circle, #22d3ee, transparent 60%); animation-delay: -12s;}

    @keyframes floaty{
      0%,100%{ transform:translateY(0) rotate(0deg) }
      50%{ transform:translateY(-20px) rotate(3deg) }
    }

    .hero{
        position:relative; border-radius:26px; overflow:hidden; color:#fff;
        background: linear-gradient(180deg, rgba(255,255,255,.12), rgba(255,255,255,.06));
        backdrop-filter: blur(14px);
        border:1px solid var(--stroke);
        box-shadow: 0 30px 90px rgba(0,0,0,.45);
        z-index:1;
    }
    .hero::before{
        content:""; position:absolute; inset:0; z-index:0;
        background: url("{{ asset('adminlte/dist/img/pattern.svg') }}") center/620px repeat;
        opacity:.06;
    }
    .hero::after{
        content:""; position:absolute; inset:-2px; z-index:-1; border-radius:28px;
        background: conic-gradient(from 0deg, #60a5fa, #22d3ee, #a5b4fc, #60a5fa);
        filter: blur(18px); opacity:.35; animation: spin 12s linear infinite;
    }
    @keyframes spin{ to{ transform: rotate(360deg); } }

    .row-hero{ display:flex; flex-wrap:wrap; position:relative; z-index:1; }
    .left{
        flex:0 0 360px; padding:44px 26px; text-align:center; position:relative;
        background: linear-gradient(180deg, rgba(10,38,71,.45), rgba(10,38,71,.25));
        border-right:1px dashed rgba(255,255,255,.18);
    }
    .right{
        flex:1; background:#ffffff; color:var(--ink); padding:34px 28px;
    }

    .avatar-wrap{
        position:relative; width:184px; height:184px; margin:0 auto 14px; border-radius:50%;
        padding:7px; background:linear-gradient(180deg, rgba(255,255,255,.28), rgba(255,255,255,.06));
        box-shadow:inset 0 1px 0 rgba(255,255,255,.25), 0 12px 30px rgba(0,0,0,.4);
    }
    .avatar{ width:100%; height:100%; border-radius:50%; object-fit:cover; display:block; }
    .ring{
        position:absolute; inset:-6px; border-radius:50%;
        background: conic-gradient(from 0deg, #ffe58a, #ffd76a, #fff2b3, #ffe58a);
        -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 6px), #000 0);
        mask: radial-gradient(farthest-side, transparent calc(100% - 6px), #000 0);
        animation: halo 10s linear infinite;
        opacity:.8;
    }
    @keyframes halo{ to{ transform: rotate(360deg); } }

    .name{ font-weight:900; font-size:1.6rem; letter-spacing:.2px; }
    .nip-badge{
        display:inline-flex; align-items:center; gap:8px; margin-top:10px;
        padding:6px 12px; border-radius:999px; font-weight:800; font-size:.9rem;
        background: linear-gradient(135deg, var(--bnn-gold), #ffe58a); color:#3a2a00;
        box-shadow:0 10px 22px rgba(0,0,0,.35);
    }
    .role-chip{
        display:inline-block; margin-top:10px; padding:6px 12px; border-radius:999px;
        background: rgba(255,255,255,.14); border:1px solid rgba(255,255,255,.25);
        font-weight:700; font-size:.85rem; color:#eaf2ff;
    }

    .meta-grid{ display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-top:14px; }
    .meta{
        padding:14px; border-radius:14px;
        background: linear-gradient(180deg, #f5f9ff, #ffffff);
        border:1px solid #e7eef8;
        box-shadow:0 10px 22px rgba(16,24,40,.08);
    }
    .meta .t{ font-size:.78rem; color:#0a2647; font-weight:800; text-transform:uppercase; }
    .meta .v{ font-weight:900; color:#1e293b; font-size:1.05rem; margin-top:2px; }

    .quick{ display:flex; flex-direction:column; gap:12px; margin-top:20px; }
    .btn-pill{ border:none; border-radius:14px; padding:12px 16px; font-weight:800;
        display:flex; align-items:center; justify-content:center; gap:10px;
        box-shadow:0 14px 28px rgba(16,24,40,.18); transition: transform .08s ease, filter .2s ease;
    }
    .btn-pill:hover{ transform:translateY(-1px); filter:brightness(1.03); }
    .btn-edit{ background: linear-gradient(135deg, #ffd76a, var(--bnn-gold)); color:#3a2a00; }
    .btn-print{ background: linear-gradient(135deg, #e2e8f0, #ffffff); color:#0a2647; border:1px solid #e2e8f0; }
    .btn-back { background:#111827; color:#fff; }

    .title{ display:inline-flex; align-items:center; gap:10px; padding:8px 14px;
        background:#e7eef9; color:#0a2647; border-radius:12px; font-weight:900; }
    .grid{ display:grid; grid-template-columns:1fr 1fr; gap:18px 22px; margin-top:18px; }
    .info{ background:#ffffff; border:1px solid #eef2f6; border-radius:16px; padding:14px 16px;
        box-shadow:0 12px 24px rgba(16,24,40,.06); }
    .label{ font-size:.75rem; color:var(--muted); font-weight:800; text-transform:uppercase; }
    .value{ font-size:1rem; color:var(--ink); font-weight:700; margin-top:4px; display:flex; align-items:center; gap:8px; }

    .copy-btn{ margin-left:auto; border-radius:8px; padding:4px 8px; font-size:.78rem;
        border:1px solid #e4e7ec; background:#f9fafb; color:#111827; }

    .footnote{ color:#667085; font-size:.9rem; margin-top:10px; }

    @media (max-width: 992px){
        .row-hero{ flex-direction:column; }
        .left{ border-right:0; border-bottom:1px dashed rgba(255,255,255,.18); }
        .grid{ grid-template-columns:1fr; }
    }
</style>

<div class="container-fluid stage">
    <div class="aurora"></div>

    <div class="hero">
        <div class="row-hero">
            <!-- LEFT -->
            <div class="left">
                <div class="avatar-wrap">
                    <div class="ring"></div>
                    @if ($user->foto)
                        <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto Profil" class="avatar">
                    @else
                        <img src="{{ asset('adminlte/dist/img/avatar.png') }}" alt="Foto Profil" class="avatar">
                    @endif
                </div>

                <div class="name">{{ $user->nama }}</div>
                <div class="nip-badge"><i class="fas fa-id-badge"></i> {{ $user->nip }}</div>
                <div class="role-chip"><i class="fas fa-user-tag mr-1"></i> {{ $user->level->level_name ?? 'Pegawai' }}</div>

                <div class="meta-grid">
                    <div class="meta">
                        <div class="t">Jabatan</div>
                        <div class="v">{{ $user->jabatan->refJabatan->nama_jabatan ?? '-' }}</div>
                    </div>
                    <div class="meta">
                        <div class="t">Pangkat</div>
                        <div class="v">{{ $user->pangkat->refPangkat->golongan_pangkat ?? '-' }}</div>
                    </div>
                </div>

                <div class="quick">
                    <a href="{{ route('profil.edit') }}" class="btn btn-pill btn-edit">
                        <i class="fas fa-edit"></i> Edit Profil
                    </a>
                    <button type="button" onclick="window.print()" class="btn btn-pill btn-print">
                        <i class="fas fa-print"></i> Cetak
                    </button>
                    <a href="{{ url()->previous() }}" class="btn btn-pill btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="right">
                <span class="title"><i class="fas fa-hashtag"></i> Biodata Pegawai</span>

                <div class="grid">
                    <div class="info">
                        <div class="label">Jenis Kelamin</div>
                        <div class="value"><i class="fas fa-venus-mars"></i> {{ $user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                    </div>

                    <div class="info">
                        <div class="label">Email</div>
                        <div class="value">
                            <i class="far fa-envelope"></i>
                            <span id="emailVal">{{ $user->email }}</span>
                            <button class="copy-btn ml-auto" data-target="#emailVal">Salin</button>
                        </div>
                    </div>

                    <div class="info">
                        <div class="label">No. Telepon</div>
                        <div class="value">
                            <i class="fas fa-phone"></i>
                            <span id="telVal">{{ $user->no_tlp ?? '-' }}</span>
                            @if($user->no_tlp)
                                <button class="copy-btn ml-auto" data-target="#telVal">Salin</button>
                            @endif
                        </div>
                    </div>

                    <div class="info">
                        <div class="label">Agama</div>
                        <div class="value"><i class="fas fa-mosque"></i> {{ $user->agama ?? '-' }}</div>
                    </div>

                    <div class="info">
                        <div class="label">Unit/Jabatan</div>
                        <div class="value"><i class="fas fa-sitemap"></i> {{ $user->jabatan->refJabatan->nama_jabatan ?? '-' }}</div>
                    </div>

                    <div class="info">
                        <div class="label">Golongan/Pangkat</div>
                        <div class="value"><i class="fas fa-user-shield"></i> {{ $user->pangkat->refPangkat->golongan_pangkat ?? '-' }}</div>
                    </div>

                    <!-- 🔹 Gaji Pokok -->
                    <div class="info">
                        <div class="label">Gaji Pokok</div>
                        <div class="value"><i class="fas fa-money-bill-wave"></i> {{ $gajiPokokF ?? '-' }}</div>
                    </div>

                    <!-- 🔹 Masa Kerja -->
                    <div class="info">
                        <div class="label">Masa Kerja</div>
                        <div class="value">
                            <i class="fas fa-hourglass-half"></i>
                            {{ $masaKerjaLabel ?? (($masaKerjaTahun ?? 0) . ' th ' . ($masaKerjaBulan ?? 0) . ' bln') }}
                        </div>
                    </div>

                    <!-- 🔹 Tunjangan Masa Kerja -->
                    <div class="info">
                        <div class="label">Tunjangan Masa Kerja</div>
                        <div class="value" style="display:flex;flex-direction:column;gap:6px;">
                            <span>
                                <i class="fas fa-coins"></i>
                                {{ $tunjanganMkSafeF ?? '-' }}
                                @if(method_exists($user,'getIsTmkApprovedAttribute') && !$user->is_tmk_approved)
                                    <span class="badge badge-warning ml-2" title="Menunggu persetujuan pimpinan">Menunggu persetujuan</span>
                                @endif
                            </span>

                            {{-- ✅ Tambahan (tanpa mengubah yang lama):
                                 Jika controller juga mengirim $tunjanganMkF (akumulasi TMK yang sudah disetujui),
                                 tampilkan sebagai baris info tambahan. --}}
                            @isset($tunjanganMkF)
                                <span style="font-size:.92rem;color:#475569;margin-left:24px;">
                                    <i class="fas fa-check-circle"></i>
                                    TMK disetujui: <strong>{{ $tunjanganMkF }}</strong>
                                </span>
                            @endisset
                        </div>
                    </div>
                </div>

                <div class="footnote">
                    Terakhir diperbarui: <strong>{{ $user->updated_at?->format('d M Y H:i') ?? '-' }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Salin ke clipboard
    $(document).on('click', '.copy-btn', function(){
        const target = $(this).data('target');
        const text = $(target).text().trim();
        if(!text || text==='-') return;
        navigator.clipboard.writeText(text).then(()=>{
            const self = $(this);
            self.text('Tersalin ✓');
            setTimeout(()=> self.text('Salin'), 1200);
        });
    });
</script>
@endpush
