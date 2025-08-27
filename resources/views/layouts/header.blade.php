<nav class="main-header navbar navbar-expand navbar-dark" style="background-color: #001F3F;">
  <!-- Sidebar Toggle -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link text-white" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="{{ url('/') }}" class="nav-link text-white font-weight-bold">Sistem Kepegawaian BNN</a>
    </li>
  </ul>

  @php
      use App\Models\Notification;
      use Illuminate\Support\Facades\Auth;

      $notifications = [];
      $unreadCount = 0;

      if (Auth::check()) {
          $user = Auth::user();
          $notifications = Notification::where('id_user', $user->id_user)
              ->latest()
              ->take(10)
              ->get();
          $unreadCount = Notification::where('id_user', $user->id_user)
              ->where('is_read', 0)
              ->count();
      }
  @endphp

  <!-- Right Navbar Items -->
  <ul class="navbar-nav ml-auto align-items-center">

    <!-- Notifikasi -->
    <li class="nav-item dropdown">
      <a class="nav-link text-white position-relative" data-toggle="dropdown" href="#" role="button">
        <i class="far fa-bell fa-lg"></i>
        @if($unreadCount > 0)
          <span class="badge badge-danger navbar-badge">{{ $unreadCount }}</span>
        @endif
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow" style="max-width: 350px; min-width: 300px;">
        <span class="dropdown-header font-weight-bold">{{ $unreadCount }} Notifikasi</span>
        <div class="dropdown-divider"></div>

        @forelse($notifications as $notif)
          <a href="{{ route('notifikasi.baca.id', $notif->id) }}" 
             class="dropdown-item {{ $notif->is_read ? '' : 'bg-lightblue' }}">
            <div class="d-flex align-items-start">
              <div class="mr-3 pt-1">
                <i class="{{ 
                    $notif->type == 'cuti' ? 'fas fa-calendar-alt' : 
                    ($notif->type == 'presensi' ? 'fas fa-user-clock' : 'fas fa-info-circle') 
                }} text-primary"></i>
              </div>
              <div class="flex-grow-1 small text-wrap" style="white-space: normal;">
                <div class="font-weight-semibold">{{ $notif->message }}</div>
                <div class="text-muted text-xs mt-1">{{ $notif->created_at->diffForHumans() }}</div>
              </div>
            </div>
          </a>
          <div class="dropdown-divider"></div>
        @empty
          <span class="dropdown-item text-muted">Tidak ada notifikasi</span>
        @endforelse

        <a href="{{ route('notifikasi.semua') }}" class="dropdown-item dropdown-footer text-center text-primary">
          Lihat Semua Notifikasi
        </a>
      </div>
    </li>

    <!-- Profil User -->
    <li class="nav-item dropdown ml-3">
      <a class="nav-link d-flex align-items-center text-white" data-toggle="dropdown" href="#">
        <img 
          src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : asset('images/default.png') }}" 
          class="img-circle elevation-2 mr-2" 
          alt="User Image" width="30" height="30">
        <span class="d-none d-md-inline">{{ Auth::user()->nama }}</span>
        <i class="fas fa-angle-down ml-1"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-right shadow">
        <a href="{{ route('profil.show') }}" class="dropdown-item">
          <i class="fas fa-user mr-2"></i> Profil Saya
        </a>
        <div class="dropdown-divider"></div>
        <a href="{{ route('logout') }}" class="dropdown-item text-danger">
          <i class="fas fa-sign-out-alt mr-2"></i> Keluar
        </a>
      </div>
    </li>
  </ul>
</nav>
