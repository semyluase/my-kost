<?php

namespace App\Models\Member;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopUp extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'tb_member_credit';
    protected $guarded = ['id'];
}
