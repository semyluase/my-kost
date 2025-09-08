<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryOrder extends Model
{
    protected $guarded = ['id'];

    function getRouteKeyName()
    {
        return 'slug';
    }
}
