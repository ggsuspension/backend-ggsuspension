<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $fillable = [
        'username',
        'password',
        'gerai_id',
        'role',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function gerai()
    {
        return $this->belongsTo(Gerai::class);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'username' => $this->username,
            'geraiId' => $this->gerai_id,
            'gerai' => $this->gerai ? ['name' => $this->gerai->name] : null,
            'role' => $this->role,
        ];
    }
}
