<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Archive extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_number', 'title', 'category_id', 'location_id', 'status', 'access_level', 'retention_date', 'file_path'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    // Relasi ke seluruh riwayat peminjaman fisik
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    // Mengambil data peminjaman fisik yang sedang aktif berjalan
    public function activeLoan()
    {
        return $this->hasOne(Loan::class)->where('status', 'approved')->latestOfMany();
    }
}