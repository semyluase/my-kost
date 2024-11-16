<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityHeader extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'tr_facility_h';
    protected $guarded = ['id'];

    function details()
    {
        return $this->hasMany(FacilityDetail::class, 'nobukti', 'nobukti');
    }
}
