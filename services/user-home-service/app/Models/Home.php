<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model; 

class Home extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'homes';

    protected $fillable = ['name', 'owner_id', 'members'];
    public function members()
    {
        return $this->belongsToMany(
            User::class,
            'home_user',   // pivot table name
            'home_id',     // foreign key on pivot table
            'user_id'      // related key on pivot table
        );
    }

    // (Optional but recommended)
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}