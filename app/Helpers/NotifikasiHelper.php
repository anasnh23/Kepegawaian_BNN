<?php

namespace App\Helpers;

use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotifikasiHelper
{
    /**
     * Kirim notifikasi ke user tertentu.
     *
     * @param int $userId
     * @param string $type
     * @param string $message
     * @param string|null $url
     * @return void
     */
    public static function send($userId, $type, $message, $url = null)
    {
        if (!$userId) {
            Log::error("Gagal mengirim notifikasi: id_user kosong", [
                'type' => $type,
                'message' => $message,
                'url' => $url
            ]);
            return;
        }

        try {
            Notification::create([
                'id_user' => $userId,
                'type' => $type,
                'message' => $message,
                'url' => $url,
                'is_read' => 0
            ]);

            Log::info("Notifikasi berhasil dikirim", [
                'id_user' => $userId,
                'type' => $type,
                'message' => $message,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            Log::error("Error saat mengirim notifikasi: " . $e->getMessage(), [
                'id_user' => $userId,
                'type' => $type,
                'message' => $message,
                'url' => $url
            ]);
        }
    }

    /**
     * Tandai semua notifikasi sebagai telah dibaca untuk user tertentu.
     *
     * @param int $userId
     * @return void
     */
    public static function markAllAsRead($userId)
    {
        Notification::where('id_user', $userId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);
    }

    /**
     * Ambil notifikasi belum dibaca
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUnread($userId)
    {
        return Notification::where('id_user', $userId)
            ->where('is_read', 0)
            ->latest()
            ->get();
    }

    /**
     * Hitung jumlah notifikasi belum dibaca
     *
     * @param int $userId
     * @return int
     */
    public static function countUnread($userId)
    {
        return Notification::where('id_user', $userId)
            ->where('is_read', 0)
            ->count();
    }
}
