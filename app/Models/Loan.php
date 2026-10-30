<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'archive_id', 'reason', 'status', 'return_deadline', 'returned_at'];

    protected $casts = [
        'return_deadline' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function archive() { return $this->belongsTo(Archive::class); }
}