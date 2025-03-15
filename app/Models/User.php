<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\TMP\UserIdentity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $with = ['role', 'location'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];

    function role()
    {
        return $this->belongsTo(Role::class);
    }

    function location()
    {
        return $this->belongsTo(Home::class, 'home_id', 'id');
    }

    function member()
    {
        return $this->belongsTo(Member::class, 'id', 'user_id');
    }

    function deposite()
    {
        return $this->belongsTo(Deposite::class, 'id', 'user_id');
    }

    function foto()
    {
        return $this->belongsTo(UserIdentity::class, 'foto_identity', 'id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
        ];
    }

    function scopeSearch($query, array $params)
    {
        $query->when($params['search'] ?? false, fn($query, $search) => ($query->where('name', "LIKE", "%$search%")->orWhere('username', "LIKE", "%$search%")->orWhereHas('role', function ($query) use ($search) {
            return $query->where('name', 'LIKE', "%$search%");
        })->orWhereHas('location', function ($query) use ($search) {
            return $query->where('name', "LIKE", "%$search%")->orWhere('city', 'LIKE', "%$search%");
        })));

        return $query;
    }
}
