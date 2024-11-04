<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

use Illuminate\Foundation\Auth\User as Authenticatable;


class UserProfile extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'user_profiles';

    protected $primaryKey = 'user_id';

    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    public function getAuthIdentifier()
    {
        return $this->getKey(); // プライマリキーの値を返す
    }
}
