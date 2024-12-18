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

    protected $fillable = [
        'user_profile_id',
        'user_id',
        'name',
        'user_name',
        'self_introduction_text',
    ];

    public function AuthenticationAccount()
    {
        return $this->belongsTo(AuthenticationAccount::class, 'user_id', 'user_id');
    }
}
