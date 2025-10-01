<?php

namespace App\Livewire\Dashboard\Counter;

use App\Models\TransactionHeader;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CounterCleaning extends Component
{
    public function render()
    {
        $counterComplete = TransactionHeader::whereBetween('tgl_request', [Carbon::now('Asia/Jakarta')->startOfDay(), Carbon::now('Asia/Jakarta')->endOfDay()])
            ->where('is_cleaning', true)
            ->where('home_id', Auth::user()->home_id)
            ->where('status', 5)
            ->count();

        $counterTotal = TransactionHeader::whereBetween('tgl_request', [Carbon::now('Asia/Jakarta')->startOfDay(), Carbon::now('Asia/Jakarta')->endOfDay()])
            ->where('is_cleaning', true)
            ->where('home_id', Auth::user()->home_id)
            ->count();

        return view('livewire.dashboard.counter.counter-cleaning', [
            'counterComplete'   =>  $counterComplete,
            'counterTotal'   =>  $counterTotal,
        ]);
    }
}
