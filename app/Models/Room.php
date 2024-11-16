<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Room extends Model
{
    use HasFactory, Sluggable;

    protected $guarded = ['id'];
    // protected $with = ['roomPrice', 'roomFacility', 'roomPicture', 'home', 'category'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'number_room'
            ]
        ];
    }

    function roomPrice()
    {
        return $this->hasMany(RoomPrice::class);
    }

    function roomFacility()
    {
        return $this->hasMany(RoomFacility::class);
    }

    function roomPicture()
    {
        return $this->hasMany(RoomPicture::class);
    }

    function home()
    {
        return $this->belongsTo(Home::class);
    }

    function category()
    {
        return $this->belongsTo(Category::class);
    }

    function rent()
    {
        return $this->belongsTo(TransactionRent::class, 'id', 'room_id')->where("end_date", '>=', Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM"));
    }

    function getRouteKeyName()
    {
        return 'slug';
    }

    function scopeSearch($query, array $params)
    {
        $query->when($params['search'] ?? false, fn($query, $search) => ($query->where('number_room', "LIKE", "%$search%")->orWhereHas('home', function ($query) use ($search) {
            return $query->where("name", "LIKE", "%$search%");
        })->orWhereHas('category', function ($query) use ($search) {
            return $query->where("name", "LIKE", "%$search%");
        })));

        return $query;
    }
}
