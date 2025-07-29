<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Tandai notifikasi sebagai dibaca dan redirect ke URL terkait
     */
    public function baca($id)
    {
        $notif = Notification::where('id', $id)
                    ->where('id_user', Auth::user()->id_user)
                    ->firstOrFail();

        // Tandai sebagai sudah dibaca
        $notif->is_read = 1;
        $notif->save();

        // Redirect ke URL tujuan notifikasi
        return redirect($notif->url ?? '/');
    }

    /**
     * Tandai semua notifikasi sebagai telah dibaca
     */
    public function tandaiSemua()
    {
        Notification::where('id_user', Auth::user()->id_user)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return response()->json(['success' => true]);
    }

    /**
     * Tampilkan halaman daftar semua notifikasi user
     */
    public function semua()
    {
        $user = Auth::user();

        $notifications = Notification::where('id_user', $user->id_user)
                            ->latest()
                            ->paginate(20); // bisa disesuaikan

        $unreadCount = Notification::where('id_user', $user->id_user)
                            ->where('is_read', 0)
                            ->count();

        $breadcrumb = (object)[
            'title' => 'Semua Notifikasi',
            'list' => ['Dashboard', 'Notifikasi']
        ];

        return view('notifikasi.index', compact('notifications', 'unreadCount', 'breadcrumb'))
            ->with('activeMenu', 'notifikasi');
    }
}
