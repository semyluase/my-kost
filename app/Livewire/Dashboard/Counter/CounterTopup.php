<?php

namespace App\Livewire\Dashboard\Counter;

use App\Models\TransactionHeader;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class CounterTopup extends Component
{
    #[On('counterTopup.render')]
    public function render()
    {
        $counterComplete = TransactionHeader::whereBetween('tgl_request', [Carbon::now('Asia/Jakarta')->startOfDay(), Carbon::now('Asia/Jakarta')->endOfDay()])
            ->where('is_topup', true)
            ->where('home_id', Auth::user()->home_id)
            ->where('status', 5)
            ->count();

        $counterTotal = TransactionHeader::whereBetween('tgl_request', [Carbon::now('Asia/Jakarta')->startOfDay(), Carbon::now('Asia/Jakarta')->endOfDay()])
            ->where('is_topup', true)
            ->where('home_id', Auth::user()->home_id)
            ->count();

        return view('livewire.dashboard.counter.counter-topup', [
            'counterComplete'   =>  $counterComplete,
            'counterTotal'   =>  $counterTotal,
        ]);
    }
}
