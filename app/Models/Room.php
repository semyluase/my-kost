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
        return $this->belongsTo(TransactionRent::class, 'id', 'room_id')
            ->where("end_date", '>=', Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD"))
            ->where('is_change_room', false)
            ->where('is_checkout_abnormal', false)
            ->where('is_checkout_normal', false)
            ->orderBy('start_date', 'desc');
    }

    function rentToday()
    {
        return $this->belongsTo(TransactionRent::class, 'id', 'room_id')->where("start_date", ">=", Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD"))->where("end_date", "<=", Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD"))->where('is_change_room', false)->where('is_checkout_abnormal', false)->where('is_checkout_normal', false);
    }

    function oldRent()
    {
        return $this->belongsTo(TransactionRent::class, 'id', 'room_id')->where("end_date", '>=', Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD"))->where('is_change_room', true)->where('is_checkout_abnormal', false)->where('is_checkout_normal', false);
    }

    function getRouteKeyName()
    {
        return 'slug';
    }

    function scopeSearchCategory($query, $category)
    {
        return $query->when($category ?? false, fn($query, $category) => ($query->where('category_id', $category)));
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

    function scopeFilterAllRoomByCategory($query, $categoryID)
    {
        $query->when($categoryID ?? false, fn($query, $category) => ($query->where('category_id', $category)));

        return $query;
    }
}
