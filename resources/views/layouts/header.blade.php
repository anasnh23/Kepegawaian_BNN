<nav class="main-header navbar navbar-expand navbar-dark" style="background: linear-gradient(90deg, #001F3F, #003366);">
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
          <span class="badge badge-pill"
                style="background-color:#ffc107; color:#001F3F; font-weight:700; position:absolute; top:5px; right:5px;">
            {{ $unreadCount }}
          </span>
        @endif
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow-lg border-0" 
           style="max-width: 380px; min-width: 320px; border-radius: 14px; overflow:hidden;">
        
        <!-- Header -->
        <div style="background: linear-gradient(90deg,#001F3F,#003366); padding:12px 16px;" class="text-white">
          <div class="d-flex justify-content-between align-items-center">
            <span class="font-weight-bold"><i class="fas fa-bell mr-2 text-warning"></i>{{ $unreadCount }} Notifikasi</span>
            @if($unreadCount > 0)
              <a href="{{ route('notifikasi.tandaiSemua') }}" 
                 class="btn btn-sm btn-light text-dark font-weight-bold px-2 py-1"
                 style="border-radius:6px;">Tandai Semua</a>
            @endif
          </div>
        </div>

        <!-- List Notifikasi -->
        <div style="max-height: 360px; overflow-y: auto;">
          @forelse($notifications as $notif)
            <a href="{{ route('notifikasi.baca.id', $notif->id) }}" 
               class="dropdown-item px-3 py-2"
               style="transition: all .2s ease;
                      border-left: 5px solid {{ $notif->is_read ? '#fff' : '#ffc107' }};
                      background: {{ $notif->is_read ? '#fff' : '#eaf2ff' }};">
              <div class="d-flex align-items-start">
                <!-- Icon -->
                <div class="mr-3 pt-1">
                  <i class="{{ 
                      $notif->type == 'cuti' ? 'fas fa-calendar-check text-primary' : 
                      ($notif->type == 'presensi' ? 'fas fa-user-clock text-success' : 'fas fa-info-circle text-warning') 
                  }} fa-lg"></i>
                </div>
                <!-- Pesan -->
                <div class="flex-grow-1 small text-wrap" style="white-space: normal;">
                  <div class="font-weight-semibold">{!! $notif->message !!}</div>
                  <div class="text-muted text-xs mt-1">
                    <i class="far fa-clock"></i> {{ $notif->created_at->diffForHumans() }}
                  </div>
                </div>
              </div>
            </a>
            <div class="dropdown-divider my-0"></div>
          @empty
            <div class="p-3 text-center text-muted">Tidak ada notifikasi</div>
          @endforelse
        </div>

        <!-- Footer -->
        <div class="text-center p-2 bg-light">
          <a href="{{ route('notifikasi.semua') }}" 
             class="font-weight-bold text-primary">
             <i class="fas fa-list mr-1"></i> Lihat Semua Notifikasi
          </a>
        </div>
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
      <div class="dropdown-menu dropdown-menu-right shadow border-0" style="border-radius: 12px;">
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
