<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_id',
        'fee_category_id',
        'description',
        'amount',
        'discount_amount',
        'net_amount',
    ];

    public function invoice()
    {
        return $this->belongsTo(Fee::class, 'fee_id');
    }

    public function category()
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category_id');
    }
}
