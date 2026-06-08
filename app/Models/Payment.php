<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_id',
        'amount',
        'paid_at',
        'method',
        'reference',
        'status',
    ];

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }
}
