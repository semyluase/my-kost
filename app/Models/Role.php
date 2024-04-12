<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, Sluggable;

    protected $guarded = ['id'];

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

    function scopeSearch($query, array $params)
    {
        $query->when($params['search'] ?? false, fn ($query, $search) => ($query->where('name', 'LIKE', "%$search%")));

        return $query;
    }
}
