<!-- Sidebar Menu --><!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

    <!-- Dashboard Admin -->
    @if(Auth::user()->id_level == 1)
    <li class="nav-item">
      <a href="{{ url('/dashboard-admin') }}" class="nav-link {{ ($activeMenu == 'dashboard') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
      </a>
    </li>
    @endif

    <!-- Dashboard Pegawai -->
    @if(Auth::user()->id_level == 2)
    <li class="nav-item">
      <a href="{{ url('/dashboard-pegawai') }}" class="nav-link {{ ($activeMenu == 'dashboard') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
      </a>
    </li>
    @endif

    <!-- Dashboard Pimpinan -->
    @if(Auth::user()->id_level == 3)
    <li class="nav-item">
      <a href="{{ url('/dashboard-pimpinan') }}" class="nav-link {{ ($activeMenu == 'dashboard') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
      </a>
    </li>
    @endif

    <!-- Header -->
    <li class="nav-header text-warning">SISTEM KEPEGAWAIAN</li>

    <!-- Menu Pegawai -->
    @if(Auth::user()->id_level == 2)
    <li class="nav-item">
      <a href="{{ url('/presensi') }}" class="nav-link {{ ($activeMenu == 'presensi') ? 'active' : '' }}">
        <i class="fas fa-fingerprint nav-icon text-success"></i>
        <p>Presensi Pegawai</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ url('/cutipegawai') }}" class="nav-link {{ ($activeMenu == 'cuti') ? 'active' : '' }}">
        <i class="fas fa-calendar-check nav-icon text-primary"></i>
        <p>Pengajuan Cuti</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ url('/riwayat-cuti') }}" class="nav-link {{ ($activeMenu == 'riwayat-cuti') ? 'active' : '' }}">
        <i class="fas fa-history nav-icon text-info"></i>
        <p>Riwayat Cuti</p>
      </a>
    </li>
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
          <a href="{{ url('/riwayat_gaji') }}" class="nav-link {{ ($activeMenu == 'riwayat_gaji') ? 'active' : '' }}">
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

    <!-- Menu Pimpinan -->
    @if(Auth::user()->id_level == 3)
    <li class="nav-item">
      <a href="{{ url('/approval-dokumen') }}" class="nav-link {{ ($activeMenu == 'approval-dokumen') ? 'active' : '' }}">
        <i class="fas fa-file-signature nav-icon text-primary"></i>
        <p>Approval Dokumen</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ url('/riwayat-approval') }}" class="nav-link {{ ($activeMenu == 'riwayat-approval') ? 'active' : '' }}">
        <i class="fas fa-clipboard-check nav-icon text-success"></i>
        <p>Riwayat Persetujuan</p>
      </a>
    </li>
    @endif

    <!-- Menu Admin -->
    @if(Auth::user()->id_level == 1)
    <li class="nav-item {{ in_array($activeMenu, ['pegawai','presensi-admin','cuti','jabatan','ref_jabatan','riwayat-jabatan','pangkat','golongan','kgp','riwayat-gaji','pendidikan','laporan-gaji']) ? 'menu-open' : '' }}">
      <a href="#" class="nav-link {{ in_array($activeMenu, ['pegawai','presensi-admin','cuti','jabatan','ref_jabatan','riwayat-jabatan','pangkat','golongan','kgp','riwayat-gaji','pendidikan','laporan-gaji']) ? 'active' : '' }}">
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
          <a href="{{ url('/cutiadmin') }}" class="nav-link {{ ($activeMenu == 'cuti') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt nav-icon text-danger"></i>
            <p>Manajemen Cuti</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/ref_jabatan') }}" class="nav-link {{ ($activeMenu == 'ref_jabatan') ? 'active' : '' }}">
            <i class="fas fa-briefcase nav-icon text-info"></i>
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

  </ul>
</nav>
<!-- End Sidebar -->


</aside>
