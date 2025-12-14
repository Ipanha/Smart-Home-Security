<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// Use the Authenticatable class from the official MongoDB package
use MongoDB\Laravel\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

// Extend the correct Authenticatable class
class AuthCredential extends Authenticatable implements JWTSubject
{
    use HasFactory;

    // Specify the MongoDB connection name (from config/database.php)
    protected $connection = 'mongodb';
    // Specify the MongoDB collection name
    protected $collection = 'auth_credentials';

    // In MongoDB, the primary key is typically '_id', handled automatically.
    // We don't need to specify $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // Still useful for linking to user-home-service
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * For MongoDB models, getKey() correctly returns the '_id'.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        // Add custom claims here if needed in the future
        return [];
    }
}