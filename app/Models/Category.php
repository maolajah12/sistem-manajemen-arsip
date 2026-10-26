<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description'];

    // Tambahkan relasi ini
    public function archives()
    {
        return $this->hasMany(Archive::class);
    }
}