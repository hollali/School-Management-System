<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'receipt_number',
        'amount',
        'payment_method',
        'transaction_reference',
        'issued_at',
        'notes',
        'generated_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'issued_at' => 'datetime',
        ];
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
