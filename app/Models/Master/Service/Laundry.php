<?php

namespace App\Models\Master\Service;

use App\Models\Master\CategoryLaundry;
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

    function categoryLaundry()
    {
        return $this->belongsTo(CategoryLaundry::class);
    }

    function scopeSearch($query, array $params)
    {
        $query->when($params['search'] ?? false, fn($query, $search) => ($query->whereRelation('categoryLaundry', 'name', 'LIKE', "%$search%")));

        return $query;
    }
}
