<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'plate_number',
        'driver_name',
        'capacity',
        'status',
    ];

    public function route()
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }
}
