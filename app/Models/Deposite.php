<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposite extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'tr_deposites';
    protected $guarded = ['id'];
}
