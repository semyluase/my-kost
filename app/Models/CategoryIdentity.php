<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryIdentity extends Model
{
    protected $connection = 'mysql';
    protected $guarded = ['id'];

    function identity()
    {
        return $this->belongsTo(Home::class, 'identity_id', 'id');
    }
}
