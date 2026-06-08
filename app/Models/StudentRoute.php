<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRoute extends Model
{
    use HasFactory;

    protected $table = 'student_routes';

    protected $fillable = [
        'student_id',
        'route_id',
        'pickup_point',
        'drop_off_point',
        'active',
        'assigned_at',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function route()
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }
}
