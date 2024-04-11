<?php

namespace App\Helper;

use App\Models\Counter;
use Illuminate\Support\Str;

class Helper
{
    static function generateCounter($type, $category)
    {
        $dataCounter = Counter::where('type', $type)
            ->where('category', $category)
            ->first();

        if ($dataCounter) {
            Counter::find($dataCounter->id)->update([
                'data' =>  $dataCounter->data + 1
            ]);
            return $dataCounter->category . '-' . Str::padLeft($dataCounter->data + 1, 5, '0');
        }

        Counter::create([
            'type' =>  $type,
            'category' =>  $category,
            'data' =>  1,
        ]);

        return $category . '-' . Str::padLeft(1, 5, '0');
    }
}
