<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class UserProfile extends Model
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'user_profiles';
}
