<?php

namespace App\Models;

use App\Models\Member\TopUp;
use App\Models\TMP\UserIdentity;
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

    function userIdentity()
    {
        return $this->belongsTo(UserIdentity::class, 'identity_id', 'id');
    }
}
