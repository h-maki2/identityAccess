<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;
    
    protected $table = 'users';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'unsubscribe'
    ];

    public function getAuthIdentifierName()
    {
        return $this->primaryKey;
    }

    public function authenticationInformation()
    {
        return $this->hasOne(AuthenticationInformation::class, 'user_id', 'id');
    }

    public function authConfirmation()
    {
        return $this->hasOne(AuthConfirmation::class, 'user_id', 'id');
    }
}
