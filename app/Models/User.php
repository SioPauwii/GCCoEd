<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users'; 
    protected $primaryKey = 'id';
    public $incrementing = false; 

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'secondary_role',
        'email_verified_at',
        'email_verification_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the mentor information associated with the user.
     */
    public function mentor()
    {
        return $this->hasOne(Mentor::class, 'ment_inf_id', 'id');
    }

    /**
     * Get the learner information associated with the user.
     */
    public function learner()
    {
        return $this->hasOne(Learner::class, 'learn_inf_id', 'id');
    }
}



