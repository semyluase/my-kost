<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    use HasFactory, Sluggable;

    protected $guarded = ['id'];
    protected $with = ['sharedFacility', 'rule', 'pictures'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    function getRouteKeyName()
    {
        return 'slug';
    }

    function sharedFacility()
    {
        return $this->hasMany(SharedFacility::class);
    }

    function rule()
    {
        return $this->hasMany(HomeRule::class);
    }

    function pictures()
    {
        return $this->hasMany(HomePicture::class);
    }

    function scopeSearch($query, array $params)
    {
        $query->when($params['search'] ?? false, fn ($query, $search) => ($query->where('name', "LIKE", "%$search%")->orWhere('city', "LIKE", "%$search%")->orWhere('address', "LIKE", "%$search%")));

        return $query;
    }
}
