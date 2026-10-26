<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'archive_id', 'borrower_name', 'division', 
        'loan_date', 'return_date', 'status'
    ];

    public function archive()
    {
        return $this->belongsTo(Archive::class);
    }
}