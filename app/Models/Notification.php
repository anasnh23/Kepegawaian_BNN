<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MUser;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'id_user', 'type', 'message', 'url', 'is_read'
    ];

    // Notifikasi dimiliki oleh satu user
    public function user()
    {
        return $this->belongsTo(MUser::class, 'id_user', 'id_user');
    }
}
