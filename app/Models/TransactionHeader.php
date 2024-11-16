<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionHeader extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'tr_transaction_h';
    protected $guarded = ['id'];

    function getRouteKeyName()
    {
        return 'nobukti';
    }

    function details()
    {
        return $this->hasMany(TransactionDetail::class, 'nobukti', 'nobukti');
    }
}
