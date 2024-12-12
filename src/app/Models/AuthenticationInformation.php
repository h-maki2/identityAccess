<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class AuthenticationInformation extends Authenticatable
{
    use HasApiTokens, HasFactory;
    
    protected $table = 'authentication_informations';

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'email',
        'password',
        'login_restriction',
        'login_restriction_status',
        'failed_login_count',
        'next_login_allowed_at',
        'verification_status',
    ];

    public function authConfirmation()
    {
        return $this->hasOne(AuthConfirmation::class, 'user_id', 'user_id');
    }

    public function userProfile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'user_id');
    }

    public function getAuthIdentifierName()
    {
        return $this->primaryKey;
    }
}
