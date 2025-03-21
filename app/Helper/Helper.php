<?php

namespace App\Helper;

use App\Models\Counter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

function generateCounter($type, $category)
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

function makePhoneNumber($phoneNumber)
{
    $newPhoneNumber = $phoneNumber;

    if (Str::startsWith($newPhoneNumber, '0')) {
        $newPhoneNumber = Str::replaceFirst('0', '', $newPhoneNumber);
    }

    $newPhoneNumber = preg_replace('/[^0-9]/', '', $newPhoneNumber);

    return $newPhoneNumber;
}

function viewPhoneNumber($phoneNumber)
{
    return '0' . $phoneNumber;
}

function generateNoTrans($type)
{
    $homeID = "HM" . Str::padLeft(auth()->user()->home_id, 3, "0");

    $noTrans = $type . "-" . Carbon::now("Asia/Jakarta")->isoFormat("YYYY") . "-" . Carbon::now("Asia/Jakarta")->isoFormat("DDMM") . "-" . $homeID . "-" . Carbon::now("Asia/Jakarta")->isoFormat("ssmmHH");

    return $noTrans;
}
