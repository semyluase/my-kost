<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionRent extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'tr_rent';
    protected $guarded = ['id'];
    protected $with = ['member'];

    function member()
    {
        return $this->belongsTo(Member::class);
    }

    function room()
    {
        return $this->belongsTo(Room::class);
    }

    function oldRoom()
    {
        return $this->belongsTo(Room::class, 'old_room_id', 'id');
    }

    function scopeFilterByRoom($query, $roomID)
    {
        $query->when($roomID ?? false, fn($query, $room) => ($query->where('room_id', $room)));

        return $query;
    }
}
