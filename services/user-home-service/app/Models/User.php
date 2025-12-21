<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Eloquent\Model; 
use MongoDB\Laravel\Eloquent\SoftDeletes;
class User extends Model
{
    use HasFactory, Notifiable;
    use HasFactory, SoftDeletes;
    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'role',
        'profile_pic', 
        'face_embedding',
        'nfc_card_uid',
    ];
}