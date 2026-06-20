<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructureItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_structure_id',
        'fee_category_id',
        'amount',
        'description',
    ];

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function category()
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category_id');
    }
}
