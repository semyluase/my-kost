<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $connection = 'mysql';
    protected $table = 'tb_email';
    protected $guarded = ['id'];
}
