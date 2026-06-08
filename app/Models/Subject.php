<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'teacher_id',
        'credits',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
