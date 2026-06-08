<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_score',
        'max_score',
        'grade_point',
        'description',
    ];

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
