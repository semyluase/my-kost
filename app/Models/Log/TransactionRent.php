<?php

namespace App\Models\Log;

use App\Models\Room;
use Illuminate\Database\Eloquent\Model;

class TransactionRent extends Model
{
    protected $connection = 'mysql';
    protected $table = 'log_rents';
    protected $guarded = ['id'];

    function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
}
