<?php

namespace App\Livewire\Dashboard\Counter;

use App\Models\TransactionHeader;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class CounterOrder extends Component
{
    public $filterHari = 0;

    #[On('counterOrder.render')]
    public function render()
    {
        $counterComplete = TransactionHeader::whereBetween('tgl_request', [Carbon::now('Asia/Jakarta')->startOfDay(), Carbon::now('Asia/Jakarta')->endOfDay()])
            ->where('is_order', true)
            ->where('home_id', Auth::user()->home_id)
            ->where('status', 5)
            ->count();

        $counterTotal = TransactionHeader::whereBetween('tgl_request', [Carbon::now('Asia/Jakarta')->startOfDay(), Carbon::now('Asia/Jakarta')->endOfDay()])
            ->where('is_order', true)
            ->where('home_id', Auth::user()->home_id)
            ->count();

        return view('livewire.dashboard.counter.counter-order', [
            'counterComplete'   =>  $counterComplete,
            'counterTotal'   =>  $counterTotal,
        ]);
    }
}
