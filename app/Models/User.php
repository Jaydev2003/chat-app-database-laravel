<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    protected $keyType = 'string';
    public $incrementing = false;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable ,HasUuids,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'last_seen',
        'phone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen' => 'datetime',
        ];
    }

    public function groups()
{
    return $this->belongsToMany(Group::class, 'group_user');
}
public function isOnline(): bool
{
    return Cache::has('user-online-' . $this->id);
}

public function lastSeen(): string
{
    return $this->last_seen ? $this->last_seen->diffForHumans() : 'Never';
}

}
