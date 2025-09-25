<?php

namespace App\Livewire\Dashboard\Counter;

use App\Models\TransactionHeader;
use Illuminate\Support\Carbon;
use Livewire\Component;

class CounterCleaning extends Component
{
    public function render()
    {
        $counterComplete = TransactionHeader::whereBetween('tgl_request', [Carbon::now('Asia/Jakarta')->startOfDay(), Carbon::now('Asia/Jakarta')->endOfDay()])
            ->where('is_cleaning', true)
            ->where('status', 5)
            ->count();

        $counterUncomplete = TransactionHeader::whereBetween('tgl_request', [Carbon::now('Asia/Jakarta')->startOfDay(), Carbon::now('Asia/Jakarta')->endOfDay()])
            ->where('is_cleaning', true)
            ->where('status', '<>', 5)
            ->count();

        return view('livewire.dashboard.counter.counter-cleaning', [
            'counterComplete'   =>  $counterComplete,
            'counterUncomplete'   =>  $counterUncomplete,
        ]);
    }
}
