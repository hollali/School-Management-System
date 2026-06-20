<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'holiday_date',
        'type',
        'recurring',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'holiday_date' => 'date',
            'recurring' => 'boolean',
        ];
    }

    public function scopeForYear($query, int $year)
    {
        return $query->whereYear('holiday_date', $year);
    }
}
