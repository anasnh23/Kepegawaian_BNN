<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

    {{-- ================= Dashboard per Role ================= --}}
    @if(Auth::user()->id_level == 1)
      <li class="nav-item">
        <a href="{{ url('/dashboard-admin') }}" class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }}">
          <i class="nav-icon fas fa-tachometer-alt"></i>
          <p>Dashboard</p>
        </a>
      </li>
    @endif

    @if(Auth::user()->id_level == 2)
      <li class="nav-item">
        <a href="{{ url('/dashboard-pegawai') }}" class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }}">
          <i class="nav-icon fas fa-tachometer-alt"></i>
          <p>Dashboard</p>
        </a>
      </li>
    @endif

    @if(Auth::user()->id_level == 3)
      <li class="nav-item">
        <a href="{{ url('/dashboard-pimpinan') }}" class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }}">
          <i class="nav-icon fas fa-tachometer-alt"></i>
          <p>Dashboard</p>
        </a>
      </li>
    @endif

    {{-- ================= Header ================= --}}
    <li class="nav-header text-warning">SISTEM KEPEGAWAIAN</li>

    {{-- ================= Menu Pegawai ================= --}}
    @if(Auth::user()->id_level == 2)
      <li class="nav-item">
        <a href="{{ url('/presensi') }}" class="nav-link {{ $activeMenu == 'presensi' ? 'active' : '' }}">
          <i class="fas fa-fingerprint nav-icon text-success"></i>
          <p>Presensi Pegawai</p>
        </a>
      </li>

      {{-- Presensi Dinas Luar --}}
      <li class="nav-item">
        <a href="{{ url('/presensi-dinas') }}" class="nav-link {{ $activeMenu == 'presensi-dinas' ? 'active' : '' }}">
          <i class="fas fa-map-marked-alt nav-icon text-warning"></i>
          <p>Presensi Dinas Luar</p>
        </a>
      </li>

      {{-- Cuti --}}
      <li class="nav-item">
        <a href="{{ url('/cutipegawai') }}" class="nav-link {{ $activeMenu == 'cuti' ? 'active' : '' }}">
          <i class="fas fa-calendar-check nav-icon text-primary"></i>
          <p>Pengajuan Cuti</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/riwayat-cuti') }}" class="nav-link {{ $activeMenu == 'riwayat-cuti' ? 'active' : '' }}">
          <i class="fas fa-history nav-icon text-info"></i>
          <p>Riwayat Cuti</p>
        </a>
      </li>

      {{-- Pengajuan KGP (pegawai) --}}
      <li class="nav-item">
        <a href="{{ url('/kgp/pengajuan') }}" class="nav-link {{ $activeMenu == 'pengajuan-kgp' ? 'active' : '' }}">
          <i class="fas fa-money-check-alt nav-icon text-warning"></i>
          <p>Pengajuan KGP</p>
        </a>
      </li>

      {{-- Submenu Manajemen Kepegawaian (Pegawai) --}}
      @php
        $pegawaiSub = ['kgp_riwayat_gaji','riwayat-jabatan'];
      @endphp
      <li class="nav-item {{ in_array($activeMenu, $pegawaiSub) ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ in_array($activeMenu, $pegawaiSub) ? 'active' : '' }}">
          <i class="nav-icon fas fa-user-shield"></i>
          <p>
            Manajemen Kepegawaian
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          {{-- KGP • Riwayat Gaji (untuk pegawai tetap ada) --}}
          <li class="nav-item">
            <a href="{{ url('/kgp/riwayat') }}" class="nav-link {{ $activeMenu == 'kgp_riwayat_gaji' ? 'active' : '' }}">
              <i class="fas fa-money-bill nav-icon"></i>
              <p>KGP • Riwayat Gaji</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ url('/riwayat-jabatan') }}" class="nav-link {{ $activeMenu == 'riwayat-jabatan' ? 'active' : '' }}">
              <i class="fas fa-briefcase nav-icon"></i>
              <p>Riwayat Jabatan</p>
            </a>
          </li>
        </ul>
      </li>
    @endif

    {{-- ================= Menu Pimpinan ================= --}}
    @if(Auth::user()->id_level == 3)
      <li class="nav-item">
        <a href="{{ url('/approval-dokumen') }}" class="nav-link {{ $activeMenu == 'approval-dokumen' ? 'active' : '' }}">
          <i class="fas fa-file-signature nav-icon text-primary"></i>
          <p>Approval Dokumen</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/riwayat-approval') }}" class="nav-link {{ $activeMenu == 'riwayat-approval' ? 'active' : '' }}">
          <i class="fas fa-clipboard-check nav-icon text-success"></i>
          <p>Riwayat Persetujuan</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/approval-kgp') }}" class="nav-link {{ $activeMenu == 'approval-kgp' ? 'active' : '' }}">
          <i class="fas fa-money-check-alt nav-icon text-warning"></i>
          <p>Approval KGP</p>
        </a>
      </li>
    @endif

 {{-- ================= Menu Admin ================= --}}
@if(Auth::user()->id_level == 1)
  @php
    // Disederhanakan: hapus golongan, KGB, pendidikan, laporan gaji
    $adminMenus = [
      'dashboard_info','pegawai','pegawai-index','pegawai-tambah','pegawai-tetap','pegawai-outsourcing','pegawai-magang',
      'presensi-admin','cuti','ref_jabatan','riwayat-jabatan','pangkat'
    ];
  @endphp

  <li class="nav-item {{ in_array($activeMenu, $adminMenus) ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ in_array($activeMenu, $adminMenus) ? 'active' : '' }}">
      <i class="nav-icon fas fa-user-shield"></i>
      <p>
        Manajemen Kepegawaian
        <i class="right fas fa-angle-left"></i>
      </p>
    </a>

    <ul class="nav nav-treeview">

      {{-- Informasi Dashboard (BARU - paling atas) --}}
      <li class="nav-item">
        <a href="{{ url('/dashboard-info') }}" class="nav-link {{ $activeMenu == 'dashboard_info' ? 'active' : '' }}">
          <i class="fas fa-info-circle nav-icon text-primary"></i>
          <p>Informasi Dashboard</p>
        </a>
      </li>

      {{-- Data Pegawai --}}
      <li class="nav-item">
        <a href="{{ route('pegawai.index') }}" class="nav-link {{ $activeMenu=='pegawai' ? 'active' : '' }}">
          <i class="fas fa-id-badge nav-icon"></i>
          <p>Data Pegawai</p>
        </a>
      </li>

      {{-- Data Presensi --}}
      <li class="nav-item">
        <a href="{{ url('/presensi-admin') }}" class="nav-link {{ $activeMenu == 'presensi-admin' ? 'active' : '' }}">
          <i class="fas fa-fingerprint nav-icon"></i>
          <p>Data Presensi</p>
        </a>
      </li>

      {{-- Manajemen Cuti --}}
      <li class="nav-item">
        <a href="{{ url('/cutiadmin') }}" class="nav-link {{ $activeMenu == 'cuti' ? 'active' : '' }}">
          <i class="fas fa-calendar-alt nav-icon text-danger"></i>
          <p>Manajemen Cuti</p>
        </a>
      </li>

      {{-- Data Jabatan --}}
      <li class="nav-item">
        <a href="{{ url('/ref_jabatan') }}" class="nav-link {{ $activeMenu == 'ref_jabatan' ? 'active' : '' }}">
          <i class="fas fa-briefcase nav-icon text-info"></i>
          <p>Data Jabatan</p>
        </a>
      </li>

      {{-- Riwayat Jabatan --}}
      <li class="nav-item">
        <a href="{{ url('/riwayat-jabatan') }}" class="nav-link {{ $activeMenu == 'riwayat-jabatan' ? 'active' : '' }}">
          <i class="fas fa-history nav-icon"></i>
          <p>Riwayat Jabatan</p>
        </a>
      </li>

      {{-- Pangkat --}}
      <li class="nav-item">
        <a href="{{ url('/pangkat') }}" class="nav-link {{ $activeMenu == 'pangkat' ? 'active' : '' }}">
          <i class="fas fa-signal nav-icon"></i>
          <p>Pangkat</p>
        </a>
      </li>

    </ul>
  </li>
@endif

  </ul>
</nav>
<!-- End Sidebar -->
</aside>
