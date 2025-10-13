<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta name="theme-color" content="#0a2647">
  <title>Login - SIAP BNN</title>

  <link rel="icon" href="{{ asset('adminlte/dist/img/bnn.jpg') }}" type="image/png">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root{
      --bnn-navy:#0a2647; --bnn-blue:#144272; --bnn-cyan:#2c74b3; --bnn-gold:#f4c430;
      --glass: rgba(255,255,255,.08); --stroke: rgba(255,255,255,.16);
      --safe-pad: max(12px, env(safe-area-inset-top));
    }
    *,*::before,*::after{ box-sizing:border-box }
    html,body{ height:100% }
    body{
      margin:0; color:#e9f1ff;
      font-family: ui-sans-serif, system-ui, -apple-system,"Segoe UI",Roboto,Helvetica,Arial;
      display:block;
      background: radial-gradient(1400px 900px at 80% -10%, rgba(44,116,179,.35), transparent 60%),
                  radial-gradient(1000px 700px at -10% 110%, rgba(20,66,114,.45), transparent 55%),
                  linear-gradient(120deg, #061326 0%, #0b1f39 55%, #0a1a30 100%);
      overflow:hidden;
      padding: var(--safe-pad) max(12px, env(safe-area-inset-right))
               max(12px, env(safe-area-inset-bottom)) max(12px, env(safe-area-inset-left));
      -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
    }

    /* Ambient layers */
    .grid{ position:fixed; inset:0; pointer-events:none; opacity:.12; background-image: linear-gradient(to right, #fff 1px, transparent 1px), linear-gradient(to bottom, #fff 1px, transparent 1px); background-size: 46px 46px; z-index:1; mask-image: radial-gradient(1300px 800px at 50% 40%, black 60%, transparent 100%); }
    .orb{ position:fixed; border-radius:50%; filter:blur(80px); opacity:.35; pointer-events:none; animation: float 16s ease-in-out infinite alternate; z-index:0; will-change: transform, opacity; }
    .orb.blue{  width:520px; height:520px; background: radial-gradient(circle at 30% 30%, #2c74b3, transparent 60%); top:-120px; left:-160px; }
    .orb.cyan{  width:420px; height:420px; background: radial-gradient(circle at 70% 70%, #3b82f6, transparent 60%); bottom:-140px; right:-160px; animation-duration:20s; }
    .orb.gold{  width:340px; height:340px; background: radial-gradient(circle at 50% 50%, #f4c430, transparent 70%); bottom:20%; left:50%; transform:translateX(-50%); animation-duration:24s; }
    @keyframes float{ 0%{ transform:translateY(0) translateX(0) scale(1) } 50%{ transform:translateY(-40px) translateX(20px) scale(1.05) } 100%{ transform:translateY(0) translateX(-20px) scale(1) } }

    .sprites{ position:fixed; inset:0; pointer-events:none; z-index:2; }
    .sprite{ position:absolute; font-size:18px; opacity:.18; color:#cfe6ff; filter: drop-shadow(0 0 6px rgba(44,116,179,.45)) drop-shadow(0 2px 10px rgba(0,0,0,.35)); animation-timing-function: linear; animation-iteration-count: infinite; }
    .sprite.gold { color:#ffe28a; opacity:.16; filter:drop-shadow(0 0 6px rgba(244,196,48,.35)); }
    .sprite.cyan { color:#bfe7ff; }
    @keyframes drift-diag   { from{ transform: translate(-10vw, 0) rotate(0deg) } to{ transform: translate(110vw, 60vh) rotate(360deg) } }
    @keyframes drift-diag-2 { from{ transform: translate(110vw, -10vh) rotate(0deg) } to{ transform: translate(-10vw, 50vh) rotate(-360deg) } }
    @keyframes drift-h      { from{ transform: translate(-10vw, 0) } to{ transform: translate(110vw, 0) } }
    @keyframes drift-v      { from{ transform: translate(0, -10vh) } to{ transform: translate(0, 110vh) } }
    .s1 { top:10vh;  left:-6vw;  animation: drift-diag 38s infinite; }
    .s2 { top:22vh;  left:85vw;  animation: drift-diag-2 46s infinite; }
    .s3 { top:70vh;  left:-8vw;  animation: drift-diag 52s infinite; }
    .s4 { top:15vh;  left:15vw;  animation: drift-h 60s infinite; }
    .s5 { top:-6vh;  left:60vw;  animation: drift-v 48s infinite; }
    .s6 { top:55vh;  left:95vw;  animation: drift-diag-2 40s infinite; }
    .s7 { top:35vh;  left:-5vw;  animation: drift-diag 56s infinite; }
    .sparkle{ position:fixed; inset:0; pointer-events:none; z-index:3; opacity:.15; background: radial-gradient(2px 2px at 20% 30%, #fff, transparent 40%), radial-gradient(2px 2px at 70% 60%, #fff, transparent 40%), radial-gradient(2px 2px at 40% 80%, #fff, transparent 40%), radial-gradient(2px 2px at 85% 25%, #fff, transparent 40%); animation: twinkle 5s ease-in-out infinite alternate; }
    @keyframes twinkle{ from{ opacity:.05; transform:translateY(0) } to{ opacity:.18; transform:translateY(-6px) } }

    /* Page grid */
    .page{ position:relative; z-index:10; display:grid; grid-template-columns: 1.1fr 0.9fr; gap: clamp(16px, 3vw, 28px); align-items:center; justify-content:center; max-width: min(1200px, 96vw); margin-inline:auto; min-height: calc(100dvh - 32px); }

    /* LEFT: text */
    .hero{ display:flex; flex-direction:column; gap:14px; padding: clamp(8px, 2.5vw, 16px); position:relative; }
    .hero h1{
      margin:0; font-size: clamp(28px, 5vw, 44px); line-height:1.05; letter-spacing:.2px; font-weight:900;
      background: linear-gradient(90deg, #ffffff 0%, #f4c430 50%, #ffffff 100%);
      background-size: 200% 100%;
      -webkit-background-clip: text; background-clip: text; color: transparent;
      animation: shimmer 4s linear infinite;
      position: relative; text-shadow: none;
    }
    .hero p{
      margin:0; font-size: clamp(14px, 2.2vw, 16.5px); color:#dbe9ff; text-wrap: balance;
      opacity:0; transform: translateY(8px);
      animation: riseIn .7s ease-out forwards;
    }
    .hero p:nth-of-type(1){ animation-delay:.25s }

    .hero p.type{
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
      white-space: normal;
      overflow: visible;
      border: none;
      width: auto;
      animation: none;
      opacity: 1;
      transform: none;
      color:#e9f1ff;
    }

    @keyframes shimmer{ 0%{ background-position:0% 50% } 100%{ background-position:200% 50% } }
    @keyframes riseIn{ to{ opacity:1; transform: translateY(0) } }

    /* RIGHT: login card */
    .login{ inline-size: 100%; max-width: 520px; margin-left:auto; background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.04)); border:1px solid var(--stroke); backdrop-filter: blur(12px) saturate(140%); border-radius: 26px; box-shadow: 0 22px 60px rgba(0,0,0,.50), inset 0 1px 0 rgba(255,255,255,.08); padding: clamp(18px, 2.8vw, 30px); position:relative; transform-style: preserve-3d; will-change: transform; transition: transform .18s ease, box-shadow .18s ease; }
    .login::before{ content:""; position:absolute; inset:0; border-radius:26px; pointer-events:none; background: radial-gradient(60% 60% at 10% 0%, rgba(255,255,255,.12), transparent 60%); transform: translateZ(40px); }

    .head{ display:flex; gap:14px; align-items:center; justify-content:center; margin-bottom: 18px; transform: translateZ(30px); }
    .head img{ width:70px; height:70px; border-radius:14px; box-shadow:0 6px 18px rgba(0,0,0,.35) }
    .title{ font-weight:900; letter-spacing:.3px; font-size: clamp(20px, 2.6vw, 28px); line-height:1.05; }
    .subtitle{ display:block; font-weight:600; font-size:12px; color:#cfe0ff; opacity:.95; }

    .panel{ border:1px solid var(--stroke); background: rgba(255,255,255,.04); border-radius: 18px; padding: clamp(14px, 2.5vw, 20px); transform: translateZ(24px); }

    .control{ display:flex; align-items:center; gap:10px; background: var(--glass); border:1px solid var(--stroke); border-radius: 14px; padding: 12px 14px; transition:.2s border-color, .2s box-shadow, .2s transform; margin-block: 10px; }
    .control:focus-within{ border-color: rgba(44,116,179,.85); box-shadow: 0 0 0 .15rem rgba(44,116,179,.25); transform: translateY(-1px); }
    .control i{ color:#eaf2ff; opacity:.95 }
    .control input{ flex:1; min-width:0; border:0; outline:0; background:transparent; color:#eef4ff; font-size: clamp(15px, 2.1vw, 16px); padding:6px 0; }
    .control input::placeholder{ color:#c9d6ee }
    .toggle{ background:transparent; border:0; color:#eaf2ff; cursor:pointer; padding:0 2px; }

    .row-actions{ display:flex; align-items:center; justify-content:space-between; gap:10px; margin: 6px 2px 12px; color:#cfe0ff; font-size: 13px; flex-wrap:wrap; transform: translateZ(20px); }
    .row-actions a{ color:#d9e7ff; text-decoration:none }
    .row-actions a:hover{ text-decoration:underline }

    .btn{ inline-size:100%; padding:14px 16px; border:0; border-radius:14px; cursor:pointer; color:#07202b; font-weight:900; letter-spacing:.2px; background: linear-gradient(180deg, #ffd76a, #f4c430 60%, #e6b316 100%), radial-gradient(120% 180% at 50% -60%, rgba(255,255,255,.75), transparent 40%); box-shadow: 0 14px 28px rgba(244,196,48,.28), 0 4px 0 rgba(0,0,0,.15), inset 0 2px 0 rgba(255,255,255,.7), inset 0 -1px 0 rgba(0,0,0,.08); position:relative; overflow:hidden; transition: transform .12s ease, filter .25s ease, box-shadow .25s ease; font-size: clamp(15px, 2.2vw, 16px); transform: translateZ(22px); }
    .btn i{ margin-right:.55rem }
    .btn::after{ content:""; position:absolute; inset:0; background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,.28) 30%, transparent 60%); transform: translateX(-120%); transition:.55s transform; }
    .btn:hover{ filter:brightness(1.03); transform: translateZ(22px) translateY(-1px); box-shadow: 0 18px 36px rgba(244,196,48,.34), 0 5px 0 rgba(0,0,0,.18), inset 0 2px 0 rgba(255,255,255,.8), inset 0 -1px 0 rgba(0,0,0,.1); }
    .btn:hover::after{ transform: translateX(100%) }
    .btn:active{ transform: translateZ(22px) translateY(0) }
    .btn:disabled{ opacity:.7; filter:saturate(.6); cursor:not-allowed }

    .note{ text-align:center; color:#cfe0ff; opacity:.9; font-size:12px; margin-top:10px; transform: translateZ(16px); }

    /* Footer */
    .footer{
      position: fixed;
      left: 0; right: 0;
      bottom: max(8px, env(safe-area-inset-bottom));
      text-align: center;
      color:#cfe0ff;
      font-size:12px;
      z-index:15;
      opacity:.9;
      pointer-events: none;
    }

    /* Responsive */
    @media (max-width: 900px){
      body{ overflow:auto; }
      .page{ grid-template-columns: 1fr; align-items:start; gap: 14px; min-height:auto; padding-bottom: 90px; }
      .hero{ order:1; text-align:center; padding-top:10px; }
      .login{ order:2; margin-inline:auto; max-width: 92%; border-radius: 20px; padding: 16px; }
      .head{ justify-content:flex-start; gap:10px; }
      .head img{ width:56px; height:56px; border-radius:12px; }
      .title{ font-size:18px }
      .panel{ padding:12px }
      .control{ padding:10px 12px; border-radius:12px; }
      .row-actions{ font-size:13px; gap:8px }
      .btn{ padding:13px; font-size:16px; border-radius:12px }
      .sprite{ display:none } /* reduce noise on mobile */
      .orb{ opacity:.18 }
      .sparkle{ display:none }
    }

    /* Small/mobile optimizations */
    @media (max-width: 480px){
      .orb{ display:none } /* hide heavy blurred orbs on small screens */
      .grid{ opacity:.06 }
      .hero h1{ font-size: clamp(22px, 6vw, 28px) }
      .hero p{ font-size:14px }
      .login{ border-radius:14px; padding:12px; box-shadow: 0 14px 38px rgba(0,0,0,.48) }
      .head{ justify-content:center }
      .title{ text-align:center }
      .panel{ padding:10px }
      .control input{ font-size:15px }
      .btn{ padding:12px; font-size:15px }
      .footer{ font-size:11px }
      .page{ gap:12px }
    }

    /* Very small screens (compact) */
    @media (max-width: 360px){
      .hero p{ display:none } /* keep UI clean and compact */
      .hero p.type{ display:block; font-size:13px }
      .head img{ width:48px; height:48px }
      .title{ font-size:16px }
    }

    @media (prefers-reduced-motion: reduce){
      .orb, .sprite, .sparkle{ animation: none }
      .login{ transition:none }
      .hero h1{ animation: none; }
    }
  </style>
</head>
<body>
  <!-- ambient -->
  <div class="orb blue" aria-hidden="true"></div>
  <div class="orb cyan" aria-hidden="true"></div>
  <div class="orb gold" aria-hidden="true"></div>
  <div class="grid" aria-hidden="true"></div>
  <div class="sprites" aria-hidden="true">
    <i class="sprite cyan s1 fas fa-shield-alt" aria-hidden="true"></i>
    <i class="sprite s2 fas fa-fingerprint" aria-hidden="true"></i>
    <i class="sprite gold s3 fas fa-key" aria-hidden="true"></i>
    <i class="sprite s4 fas fa-database" aria-hidden="true"></i>
    <i class="sprite cyan s5 fas fa-user-shield" aria-hidden="true"></i>
    <i class="sprite s6 fas fa-lock" aria-hidden="true"></i>
    <i class="sprite gold s7 fas fa-network-wired" aria-hidden="true"></i>
  </div>
  <div class="sparkle" aria-hidden="true"></div>

  <div class="page">
    <!-- LEFT -->
    <section class="hero" aria-label="Informasi SIMASTER UGM">
      <h1>SIAP BNN KOTA KEDIRI</h1>
      <p><strong>Sistem Informasi Administrasi Pegawai</strong></p>
      <p>Untuk mendukung tercapainya instansi yang berakar kuat dan menjulang tinggi 
        melalui tata kelola yang transparan, pelayanan pegawai yang responsif, serta 
        pemanfaatan teknologi informasi yang terintegrasi dan berkelanjutan.</p>
      <p class="type"><br>Dibuat dengan ❤️ dan ☕ oleh Humas BNN Kota Kediri</p>
    </section>

    <!-- RIGHT -->
    <main class="login" id="card" role="main" aria-label="Form Login SIAP BNN">
      <div class="head">
        <img src="{{ asset('adminlte/dist/img/bnn.jpg') }}" alt="Logo BNN">
        <div>
          <div class="title">SIAP BNN</div>
          <small class="subtitle">Sistem Informasi Administrasi Pegawai</small>
        </div>
      </div>

      <section class="panel">
        <form id="formLogin" method="POST" autocomplete="on" action="{{ url('/login') }}">
          @csrf
          <div class="control" role="group" aria-label="Input username">
            <i class="fas fa-user" aria-hidden="true"></i>
            <input id="username" name="username" type="text" placeholder="Username" required autofocus aria-label="Username">
          </div>
          <div class="control" role="group" aria-label="Input password">
            <i class="fas fa-lock" aria-hidden="true"></i>
            <input id="password" name="password" type="password" placeholder="Password" required aria-label="Password">
            <button class="toggle" type="button" aria-label="Tampilkan/Sembunyikan password">
              <i class="far fa-eye"></i>
            </button>
          </div>

          <div class="row-actions">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
              <input type="checkbox" name="remember" style="accent-color:#2c74b3"> Ingat saya
            </label>
            <a href="#" id="linkForgot">Lupa password?</a>
          </div>

          <button type="submit" class="btn" id="btnLogin" aria-label="Masuk ke sistem">
            <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Masuk
          </button>

          <p class="note">Gunakan kredensial yang diberikan Admin. Aktivitas Anda diawasi demi keamanan.</p>
        </form>
      </section>
    </main>
  </div>

  <!-- Footer -->
  <footer class="footer" role="contentinfo">
    Copyright © 2025 Badan Narkotika Nasional
  </footer>

  <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

  <script>
    // Toggle show/hide password
    document.addEventListener('click', function(e){
      if(e.target.closest('.toggle')){
        const btn = e.target.closest('.toggle');
        const pwd = document.getElementById('password');
        const icon = btn.querySelector('i');
        const show = pwd.type === 'password';
        pwd.type = show ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
        pwd.focus();
      }
    });

    // Parallax tilt pada kartu login
    (function(){
      const card = document.getElementById('card');
      if(!card) return;

      // Disable parallax for small screens or touch devices to improve UX and performance
      const disableParallax = ('ontouchstart' in window) || window.matchMedia('(max-width:900px)').matches;

      if(disableParallax){
        card.style.transform = 'none';
        return;
      }

      const maxTilt = 10; let raf = null;
      function handle(e){
        const r = card.getBoundingClientRect();
        const px = (e.clientX - r.left) / r.width - 0.5;
        const py = (e.clientY - r.top)  / r.height - 0.5;
        const rx = (+py * maxTilt), ry = (-px * maxTilt);
        cancelAnimationFrame(raf);
        raf = requestAnimationFrame(()=> {
          card.style.transform = `perspective(900px) rotateX(${rx}deg) rotateY(${ry}deg) translateZ(0)`;
          card.style.boxShadow = `0 ${Math.abs(rx)+18}px ${Math.abs(ry)+48}px rgba(0,0,0,.50)`;
        });
      }
      function reset(){
        card.style.transform = `perspective(900px) rotateX(0) rotateY(0)`;
        card.style.boxShadow = `0 22px 60px rgba(0,0,0,.50)`;
      }
      card.addEventListener('mousemove', handle);
      card.addEventListener('mouseleave', reset);
      // touch fallback: small tilt only
      card.addEventListener('touchmove', (e)=>{ const t = e.touches[0]; if(t) handle(t); }, {passive:true});
      card.addEventListener('touchend', reset);
    })();

    // Randomisasi kecil durasi & ukuran sprites (kehilangan efek pada small screens via CSS)
    (function(){
      document.querySelectorAll('.sprite').forEach((el, i)=>{
        const d = parseFloat(getComputedStyle(el).animationDuration) || (40 + i*3);
        el.style.animationDuration = (d + (Math.random()*10 - 5)) + 's';
        el.style.fontSize = (16 + Math.random()*6) + 'px';
        el.style.opacity = (0.12 + Math.random()*0.08).toFixed(2);
      });
    })();

    // Ajax login (graceful fallback to HTML form post if JS disabled)
    $(function () {
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

      $('#formLogin').on('submit', function (e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $('#btnLogin');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses…');

        // Use fetch-style progressive enhancement: try ajax, if server expects same-origin form post it will still accept
        $.post($form.attr('action') || "{{ url('/login') }}", $form.serialize())
          .done(function (res) {
            if (res && res.status) {
              Swal.fire({ icon:'success', title:'Berhasil', text:res.message, timer:1200, showConfirmButton:false });
              setTimeout(() => { if(res.redirect) window.location.href = res.redirect; else location.reload(); }, 1100);
            } else {
              Swal.fire({ icon:'error', title:'Gagal', text: (res && res.message) || 'Username/Password salah.' });
            }
          })
          .fail(function (xhr) {
            // If server returned HTML (non-json), fallback to normal submit to let backend handle
            const jsonMsg = xhr.responseJSON?.message;
            if (jsonMsg) {
              Swal.fire({ icon:'error', title:'Terjadi Kesalahan', text: jsonMsg });
            } else {
              // fallback: try native submit (useful when server returns redirect/html)
              $btn.prop('disabled', false).html('<i class="fas fa-sign-in-alt"></i> Masuk');
              // If we detect HTML response, submit the form normally
              if (xhr.responseText && /<!doctype html/i.test(xhr.responseText)) {
                $form.off('submit'); // remove ajax handler then submit
                $form.submit();
                return;
              }
              Swal.fire({ icon:'error', title:'Terjadi Kesalahan', text: 'Server tidak merespon.' });
            }
          })
          .always(function(){
            $btn.prop('disabled', false).html('<i class="fas fa-sign-in-alt"></i> Masuk');
          });
      });

      // Lupa password -> pop up info
      $(document).on('click', '#linkForgot', function (e) {
        e.preventDefault();
        Swal.fire({ icon: 'info', title: 'Lupa Password', text: 'Silahkan menghubungi admin.', confirmButtonText: 'OK' });
      });
    });
  </script>
</body>
</html>
