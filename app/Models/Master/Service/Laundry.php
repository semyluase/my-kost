<?php

namespace App\Models\Master\Service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laundry extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function getRouteKeyName()
    {
        return 'kode_item';
    }

    function scopeSearch($query, array $params)
    {
        $query->when($params['search'] ?? false, fn($query, $search) => ($query->where('name', 'LIKE', "%$search%")));

        return $query;
    }
}
