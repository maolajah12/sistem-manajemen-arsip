<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessRequest extends Model
{
    use HasFactory;

    // 1. Tambahkan expires_at di sini
    protected $fillable = ['user_id', 'archive_id', 'reason', 'status', 'expires_at'];

    // 2. Tambahkan casting agar dibaca sebagai format waktu/tanggal oleh Laravel
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function archive() { return $this->belongsTo(Archive::class); }
}