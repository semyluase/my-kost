<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityDetail extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'tr_facility_d';
    protected $guarded = ['id'];
}
