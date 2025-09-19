<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Tampilkan halaman daftar semua notifikasi user.
     */
    public function semua()
    {
        $user = Auth::user();

        $notifications = Notification::where('id_user', $user->id_user)
            ->latest()
            ->paginate(20);

        $unreadCount = Notification::where('id_user', $user->id_user)
            ->where('is_read', 0)
            ->count();

        $breadcrumb = (object)[
            'title' => 'Semua Notifikasi',
            'list'  => ['Dashboard', 'Notifikasi'],
        ];

        return view('notifikasi.index', compact('notifications', 'unreadCount', 'breadcrumb'))
            ->with('activeMenu', 'notifikasi');
    }

    /**
     * Tandai satu notifikasi sebagai dibaca lalu redirect ke URL terkait.
     */
    public function baca($id)
    {
        $notif = Notification::where('id', $id)
            ->where('id_user', Auth::user()->id_user)
            ->firstOrFail();

        $notif->is_read = 1;
        $notif->save();

        return redirect($notif->url ?? '/');
    }

    /**
     * Tandai semua notifikasi sebagai dibaca.
     * - Jika request AJAX / expects JSON -> kembalikan JSON
     * - Jika bukan -> redirect back dengan flash message
     */
    public function tandaiSemua(Request $request)
    {
        Notification::where('id_user', Auth::user()->id_user)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sebagai telah dibaca.');
    }

    /**
     * (Opsional) Fallback GET untuk kasus ketika tombol memicu GET.
     * Rekomendasi tetap gunakan POST + fetch/axios.
     */
    public function tandaiSemuaGet(Request $request)
    {
        // Panggil method utama agar satu pintu
        return $this->tandaiSemua($request);
    }
}
