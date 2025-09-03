@extends('layouts.template')
@section('title', 'Semua Notifikasi')

@section('content')

<style>
  .bnn-hero {
    background: linear-gradient(135deg,#001F3F,#003366);
    color:#fff;
    border-radius:14px;
    padding:20px;
    margin-bottom:20px;
    box-shadow:0 8px 24px rgba(0,0,0,.2);
  }
  .bnn-hero h3 { font-weight:700; margin:0; }
  .bnn-hero small { color:#d1d9e6; }

  .notif-card {
    border:1px solid #e6edf6;
    border-radius:12px;
    margin-bottom:12px;
    padding:16px;
    transition:all .25s ease;
    background:#fff;
  }
  .notif-card.unread {
    background:#eaf2ff;
    border-left:6px solid #ffc107;
    animation: glowPulse 1.5s infinite;
  }
  .notif-card:hover {
    transform:translateY(-2px);
    box-shadow:0 6px 16px rgba(0,0,0,.08);
  }

  @keyframes glowPulse {
    0% { box-shadow:0 0 0px rgba(0,51,102,0.4); }
    50% { box-shadow:0 0 12px rgba(0,51,102,0.5); }
    100% { box-shadow:0 0 0px rgba(0,51,102,0.4); }
  }

  .notif-icon {
    width:42px; height:42px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:18px; flex-shrink:0;
  }
  .notif-icon.cuti { background:#eaf2ff; color:#0b4aa0; }
  .notif-icon.presensi { background:#e6f8ed; color:#2e7d32; }
  .notif-icon.info { background:#fff8e1; color:#b28704; }

  .notif-content { flex:1; }
  .notif-message { font-size:.95rem; font-weight:500; color:#2c3e50; }
  .notif-time { font-size:.8rem; color:#6b7a8c; }

  .btn-bnn {
    background:#ffc107; color:#001F3F; font-weight:700;
    border-radius:8px; padding:6px 14px;
  }
  .btn-bnn:hover { background:#e0a800; color:#fff; }

  .pagination .page-link { color:#003366; }
  .pagination .active .page-link { background:#003366; border-color:#003366; color:#fff; }
</style>

<div class="container-fluid">
  <!-- Hero -->
  <div class="bnn-hero d-flex justify-content-between align-items-center">
    <div>
      <h3><i class="fas fa-bell mr-2 text-warning"></i> Semua Notifikasi</h3>
      <small>Lihat daftar lengkap notifikasi terbaru Anda</small>
    </div>
    @if($unreadCount > 0)
      <button type="button" id="btnTandaiSemua" class="btn btn-bnn">
        <i class="fas fa-check-double mr-1"></i> Tandai Semua Dibaca
      </button>
    @endif
  </div>

  <!-- List Notifikasi -->
  <div class="row">
    <div class="col-md-12">
      @forelse($notifications as $notif)
        <a href="{{ route('notifikasi.baca.id',$notif->id) }}" class="text-decoration-none">
          <div class="notif-card {{ $notif->is_read ? '' : 'unread' }} d-flex align-items-start">
            <!-- Icon -->
            <div class="notif-icon {{ $notif->type }}">
              <i class="{{ 
                $notif->type=='cuti' ? 'fas fa-calendar-check' : 
                ($notif->type=='presensi' ? 'fas fa-user-clock' : 'fas fa-info-circle') 
              }}"></i>
            </div>
            <!-- Content -->
            <div class="notif-content ml-3">
              <div class="notif-message">{!! $notif->message !!}</div>
              <div class="notif-time"><i class="far fa-clock mr-1"></i>{{ $notif->created_at->diffForHumans() }}</div>
            </div>
          </div>
        </a>
      @empty
        <div class="notif-card text-center text-muted">
          <i class="fas fa-bell-slash fa-2x mb-2"></i>
          <div>Tidak ada notifikasi</div>
        </div>
      @endforelse
    </div>
  </div>

  <!-- Pagination -->
  <div class="mt-3">
    {{ $notifications->links() }}
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('btnTandaiSemua')?.addEventListener('click', function(){
  Swal.fire({
    title: 'Tandai semua notifikasi?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, Tandai',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#003366'
  }).then((result) => {
    if(result.isConfirmed){
      fetch("{{ route('notifikasi.tandaiSemua') }}", {
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      }).then(res => res.json())
        .then(data => {
          if(data.success){
            Swal.fire('Sukses','Semua notifikasi ditandai dibaca','success')
              .then(()=> location.reload());
          }
        }).catch(()=>{
          Swal.fire('Error','Terjadi kesalahan server','error');
        });
    }
  });
});
</script>
@endpush
