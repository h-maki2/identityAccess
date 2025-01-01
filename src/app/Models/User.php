<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Model
{
    use HasFactory;
    
    protected $table = 'users';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'unsubscribe'
    ];

    public function authenticationInformation()
    {
        return $this->hasOne(AuthenticationInformation::class, 'user_id', 'id');
    }

    public function DefinitiveRegistrationConfirmation()
    {
        return $this->hasOne(DefinitiveRegistrationConfirmation::class, 'user_id', 'id');
    }
}
