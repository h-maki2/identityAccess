<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AuthenticationInformation extends Authenticatable
{
    use HasFactory;
    
    protected $table = 'authentication_informations';

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function authConfirmations()
    {
        return $this->hasOne(AuthConfirmation::class, 'user_id', 'user_id');
    }

    public function getAuthIdentifierName()
    {
        return $this->primaryKey;
    }
}
