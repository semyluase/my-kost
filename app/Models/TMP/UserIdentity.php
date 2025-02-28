<?php

namespace App\Models\TMP;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserIdentity extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // function getFileNameAttribute($value)
    // {
    //     return Str::append("testing");
    // }

    // protected function fileName(string $value): Attribute
    // {
    //     return Attribute::make(
    //         get: fn(string $value) => asset('assets/upload/userIdentity/' . $value),
    //     );
    // }
}
