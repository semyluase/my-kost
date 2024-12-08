<?php

namespace App\Models;

use App\Models\Member\TopUp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $connction = 'mysql';
    protected $table = 'members';
    protected $guarded = ['id'];

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function memberCredit()
    {
        return $this->belongsTo(TopUp::class, 'user_id', 'user_id');
    }
}
