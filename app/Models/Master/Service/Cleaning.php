<?php

namespace App\Models\Master\Service;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cleaning extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function getRouteKeyName()
    {
        return 'kode_item';
    }

    function scopeSearch($query, array $params)
    {
        $query->when($params['search'] ?? false, fn($query, $search) => ($query->where('price', 'LIKE', "%$search%")));

        return $query;
    }
}
