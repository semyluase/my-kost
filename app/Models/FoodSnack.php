<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodSnack extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $with = ['picture'];

    function getRouteKeyName()
    {
        return 'code_item';
    }

    function scopeSearch($query, array $params)
    {
        $query->when($params['search'] ?? false, fn ($query, $search) => ($query->where('name', "LIKE", "%$search%")->orWhere('category', "LIKE", "%$search%")->orWhere('price', "LIKE", "%$search%")));

        return $query;
    }

    function picture()
    {
        return $this->belongsTo(FoodSnackPicture::class, 'id', 'food_snack_id');
    }
}
