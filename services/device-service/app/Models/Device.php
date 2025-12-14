<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model; // Use official package

class Device extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'devices';

    protected $fillable = [
        'home_id',
        'name',
        'type', // e.g., 'camera', 'lock', 'sensor'
        'status', // e.g., 'online', 'offline'
        'last_activity'
    ];
}