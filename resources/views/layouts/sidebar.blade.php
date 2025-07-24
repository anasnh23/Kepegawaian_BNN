<!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

    <!-- Dashboard untuk semua -->
    <li class="nav-item">
      <a href="{{ url('/') }}" class="nav-link {{ ($activeMenu == 'dashboard') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
      </a>
    </li>

    <!-- Presensi khusus Pegawai -->
    @if(Auth::user()->id_level == 2)
    <li class="nav-item">
      <a href="{{ url('/presensi') }}" class="nav-link {{ ($activeMenu == 'presensi') ? 'active' : '' }}">
        <i class="fas fa-fingerprint nav-icon text-success"></i>
        <p>Presensi Pegawai</p>
      </a>
    </li>
    @endif

    <!-- Header -->
    <li class="nav-header text-warning">SISTEM KEPEGAWAIAN</li>

    <!-- Menu untuk Admin -->
    @if(Auth::user()->id_level == 1)
    <li class="nav-item {{ in_array($activeMenu, ['pegawai','presensi-admin','jabatan','riwayat-jabatan','pangkat','golongan','kgp','riwayat-gaji','pendidikan','laporan-gaji']) ? 'menu-open' : '' }}">
      <a href="#" class="nav-link {{ in_array($activeMenu, ['pegawai','presensi-admin','jabatan','riwayat-jabatan','pangkat','golongan','kgp','riwayat-gaji','pendidikan','laporan-gaji']) ? 'active' : '' }}">
        <i class="nav-icon fas fa-user-shield"></i>
        <p>
          Manajemen Kepegawaian
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ url('/pegawai') }}" class="nav-link {{ ($activeMenu == 'pegawai') ? 'active' : '' }}">
            <i class="fas fa-id-badge nav-icon"></i>
            <p>Data Pegawai</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/presensi-admin') }}" class="nav-link {{ ($activeMenu == 'presensi-admin') ? 'active' : '' }}">
            <i class="fas fa-fingerprint nav-icon"></i>
            <p>Data Presensi</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/jabatan') }}" class="nav-link {{ ($activeMenu == 'jabatan') ? 'active' : '' }}">
            <i class="fas fa-briefcase nav-icon"></i>
            <p>Data Jabatan</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/riwayat-jabatan') }}" class="nav-link {{ ($activeMenu == 'riwayat-jabatan') ? 'active' : '' }}">
            <i class="fas fa-history nav-icon"></i>
            <p>Riwayat Jabatan</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/pangkat') }}" class="nav-link {{ ($activeMenu == 'pangkat') ? 'active' : '' }}">
            <i class="fas fa-signal nav-icon"></i>
            <p>Pangkat</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/golongan') }}" class="nav-link {{ ($activeMenu == 'golongan') ? 'active' : '' }}">
            <i class="fas fa-layer-group nav-icon"></i>
            <p>Golongan Pangkat</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/kgp') }}" class="nav-link {{ ($activeMenu == 'kgp') ? 'active' : '' }}">
            <i class="fas fa-chart-line nav-icon"></i>
            <p>Kenaikan Gaji Berkala</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/riwayat-gaji') }}" class="nav-link {{ ($activeMenu == 'riwayat-gaji') ? 'active' : '' }}">
            <i class="fas fa-money-bill-wave nav-icon"></i>
            <p>Riwayat Gaji</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/pendidikan') }}" class="nav-link {{ ($activeMenu == 'pendidikan') ? 'active' : '' }}">
            <i class="fas fa-graduation-cap nav-icon"></i>
            <p>Pendidikan</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/laporan-gaji') }}" class="nav-link {{ ($activeMenu == 'laporan-gaji') ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar nav-icon"></i>
            <p>Laporan Gaji</p>
          </a>
        </li>
      </ul>
    </li>
    @endif

    <!-- Menu untuk Pegawai -->
    @if(Auth::user()->id_level == 2)
    <li class="nav-item {{ in_array($activeMenu, ['laporan-gaji','riwayat-gaji','riwayat-jabatan']) ? 'menu-open' : '' }}">
      <a href="#" class="nav-link {{ in_array($activeMenu, ['laporan-gaji','riwayat-gaji','riwayat-jabatan']) ? 'active' : '' }}">
        <i class="nav-icon fas fa-user-shield"></i>
        <p>
          Manajemen Kepegawaian
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ url('/laporan-gaji') }}" class="nav-link {{ ($activeMenu == 'laporan-gaji') ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar nav-icon"></i>
            <p>Laporan Gaji</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/riwayat-gaji') }}" class="nav-link {{ ($activeMenu == 'riwayat-gaji') ? 'active' : '' }}">
            <i class="fas fa-money-bill nav-icon"></i>
            <p>Riwayat Gaji</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/riwayat-jabatan') }}" class="nav-link {{ ($activeMenu == 'riwayat-jabatan') ? 'active' : '' }}">
            <i class="fas fa-briefcase nav-icon"></i>
            <p>Riwayat Jabatan</p>
          </a>
        </li>
      </ul>
    </li>
    @endif

  </ul>
</nav>
</aside>
