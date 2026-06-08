<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportRoute extends Model
{
    use HasFactory;

    protected $table = 'routes';

    protected $fillable = [
        'name',
        'description',
        'start_point',
        'end_point',
        'active',
    ];

    public function buses()
    {
        return $this->hasMany(Bus::class, 'route_id');
    }

    public function studentRoutes()
    {
        return $this->hasMany(StudentRoute::class, 'route_id');
    }
}
