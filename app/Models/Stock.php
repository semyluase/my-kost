<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'tr_stock';
    protected $guarded = ['id'];

    function foodSnack()
    {
        return $this->belongsTo(FoodSnack::class, 'code_item', 'code_item');
    }
}
