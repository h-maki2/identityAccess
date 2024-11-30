<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'user_profiles';

    protected $primaryKey = 'user_profile_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public function authenticationInformation()
    {
        return $this->belongsTo(AuthenticationInformation::class, 'user_id', 'user_id');
    }
}
