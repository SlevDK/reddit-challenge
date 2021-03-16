<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends \Illuminate\Foundation\Auth\User implements JWTSubject
{
    use HasFactory, Authenticatable;

    protected $fillable = ['name', 'email', 'password'];

    /**
     * Boards created by user.
     *
     * @return HasMany
     */
    public function boards()
    {
        return $this->hasMany(Board::class, 'owner_id');
    }

    /**
     * Threads created by user.
     *
     * @return HasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class, 'author_id');
    }

    /**
     * Comments created by user.
     *
     * @return HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'author_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
