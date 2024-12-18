<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefinitiveRegistrationConfirmation extends Model
{
    use HasFactory;

    protected $table = 'definitive_registration_confirmations';

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',                
        'one_time_token_value',
        'one_time_token_expiration',
        'one_time_password',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
