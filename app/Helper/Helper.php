<?php

namespace App\Helper;

use App\Models\Counter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

function integerToRoman(int $num): string
{
    if ($num <= 0 || $num > 3999) { // Roman numerals typically don't represent zero or numbers above 3999
        return '';
    }

    $romanNumerals = [
        1000 => 'M',
        900 => 'CM',
        500 => 'D',
        400 => 'CD',
        100 => 'C',
        90 => 'XC',
        50 => 'L',
        40 => 'XL',
        10 => 'X',
        9 => 'IX',
        5 => 'V',
        4 => 'IV',
        1 => 'I'
    ];

    $result = '';

    foreach ($romanNumerals as $value => $roman) {
        while ($num >= $value) {
            $result .= $roman;
            $num -= $value;
        }
    }

    return $result;
}

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

function generateCounterInvoice()
{
    $dataCounter = Counter::where('type', 'invoice')
        ->where('category', 'INV')
        ->where('tahun', Carbon::now('Asia/Jakarta')->isoFormat('YYYY'))
        ->first();

    $month = integerToRoman(Carbon::now('Asia/Jakarta')->month);
    if ($dataCounter) {
        Counter::find($dataCounter->id)->update([
            'data' =>  $dataCounter->data + 1
        ]);

        return 'INV-' . Carbon::now('Asia/Jakarta')->year . '-' . $month . '-' . Str::padLeft($dataCounter->data + 1, 5, '0');
    }

    Counter::create([
        'type' =>  'invoice',
        'category' =>  'INV',
        'data' =>  1,
        'tahun' =>  Carbon::now('Asia/Jakarta')->year
    ]);

    return 'INV-' . Carbon::now('Asia/Jakarta')->year . '-' . $month . '-' . Str::padLeft(1, 5, '0');;
}

function generateCounterTransaction($category)
{
    return $category . Carbon::now('Asia/Jakarta')->timestamp;
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

function formatExcel_Idr()
{
    return 'Rp #,##';
}

function formatExcel_Number()
{
    return '#,##0';
}

function styleExcel_TableBorder($type = 'allBorders')
{
    return [
        'borders' => [
            $type => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            ],
        ],
    ];
}

function styleExcel_TextMiddle()
{
    return [
        'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ]
    ];
};

function styleExcel_Heading()
{
    return [
        'font' => [
            'bold' => true,
            'size' => 22,
            'name' => 'Calibri Light'
        ]
    ];
};

function styleExcel_Calibry($fontSize, $bold)
{
    return [
        'font' => [
            'bold' => $bold,
            'size' => $fontSize,
            'name' => 'Calibri Light'
        ]
    ];
};
