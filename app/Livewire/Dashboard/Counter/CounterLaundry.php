<?php

namespace App\Livewire\Dashboard\Counter;

use App\Models\TransactionHeader;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class CounterLaundry extends Component
{
    #[On('counterLaundry.render')]
    public function render()
    {
        $counterComplete = TransactionHeader::whereBetween('tgl_request', [Carbon::now('Asia/Jakarta')->startOfDay(), Carbon::now('Asia/Jakarta')->endOfDay()])
            ->where('is_laundry', true)
            ->where('status', 5)
            ->count();

        $counterUncomplete = TransactionHeader::whereBetween('tgl_request', [Carbon::now('Asia/Jakarta')->startOfDay(), Carbon::now('Asia/Jakarta')->endOfDay()])
            ->where('is_laundry', true)
            ->where('status', '<>', 5)
            ->count();

        return view('livewire.dashboard.counter.counter-laundry', [
            'counterComplete'   =>  $counterComplete,
            'counterUncomplete'   =>  $counterUncomplete,
        ]);
    }
}
