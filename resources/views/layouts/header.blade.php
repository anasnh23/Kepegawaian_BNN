<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark" style="background-color: #001F3F;">
  <!-- Sidebar Toggle -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="{{ url('/') }}" class="nav-link text-white font-weight-bold">Sistem Kepegawaian BNN</a>
    </li>
  </ul>

 <!-- User Profile -->
<ul class="navbar-nav ml-auto">
  <li class="nav-item dropdown">
    <a class="nav-link d-flex align-items-center text-white" data-toggle="dropdown" href="#" role="button">
      <img 
        src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : asset('images/default.png') }}" 
        class="img-circle elevation-2 mr-2" 
        alt="User Image" width="30" height="30">
      <span class="d-none d-md-inline">{{ Auth::user()->nama }}</span>
      <i class="fas fa-angle-down ml-1"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right shadow" style="min-width: 180px;">
      <a href="{{ route('profil.show') }}" class="dropdown-item">
        <i class="fas fa-user mr-2"></i> Profil Saya
      </a>
      <div class="dropdown-divider"></div>
      <a href="{{ url('/logout') }}" class="dropdown-item text-danger">
        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
      </a>
    </div>
  </li>
</ul>
</nav>
