<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryFacility extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $with = ['facility'];

    function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}
