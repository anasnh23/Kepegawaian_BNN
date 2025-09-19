<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model Notification
 *
 * Tabel: notifications
 * PK   : id (auto increment)
 *
 * Kolom penting:
 * - id_user  : FK ke m_user.id_user (penerima notifikasi)
 * - type     : string pendek untuk kategori notif (mis: 'cuti', 'kgp')
 * - message  : isi pesan
 * - url      : link tujuan (opsional)
 * - is_read  : 0 = belum dibaca, 1 = sudah dibaca
 * - timestamps: created_at, updated_at
 */
class Notification extends Model
{
    use HasFactory;

    /** =========================
     *  Konfigurasi Model
     *  ========================= */
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'id_user',
        'type',
        'message',
        'url',
        'is_read',
    ];

    // Nilai default ketika create()
    protected $attributes = [
        'is_read' => 0,
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /** (Opsional) Konstanta untuk type notifikasi agar konsisten di seluruh app */
    public const TYPE_CUTI = 'cuti';
    public const TYPE_KGP  = 'kgp';

    /** =========================
     *  Relasi
     *  ========================= */
    public function user()
    {
        // Relasi ke tabel m_user
        return $this->belongsTo(MUser::class, 'id_user', 'id_user');
    }

    /** =========================
     *  Query Scopes
     *  ========================= */

    /** Filter semua notif milik user tertentu */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('id_user', $userId);
    }

    /** Filter notif belum dibaca milik user tertentu */
    public function scopeUnread($query, int $userId)
    {
        return $query->forUser($userId)->where('is_read', 0);
    }

    /** Ambil notif terbaru milik user tertentu (siap untuk paginate/limit) */
    public function scopeLatestFor($query, int $userId)
    {
        return $query->forUser($userId)->latest();
    }

    /** =========================
     *  Static Helpers
     *  ========================= */

    /**
     * Buat notifikasi baru (default is_read = 0).
     */
    public static function pushTo(int $userId, string $type, string $message, ?string $url = null): self
    {
        return static::create([
            'id_user' => $userId,
            'type'    => $type,
            'message' => $message,
            'url'     => $url,
            'is_read' => 0,
        ]);
    }

    /**
     * Tandai semua notifikasi milik user sebagai sudah dibaca.
     * @return int jumlah baris yang diupdate
     */
    public static function markAllReadFor(int $userId): int
    {
        return static::where('id_user', $userId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);
    }

    /**
     * Hitung jumlah notif belum dibaca milik user.
     */
    public static function unreadCountFor(int $userId): int
    {
        return (int) static::where('id_user', $userId)->where('is_read', 0)->count();
    }

    /** =========================
     *  Instance Helpers
     *  ========================= */

    /** Tandai satu notifikasi ini sebagai sudah dibaca */
    public function markAsRead(): bool
    {
        if (!$this->is_read) {
            $this->is_read = 1;
            return $this->save();
        }
        return true;
    }
}
