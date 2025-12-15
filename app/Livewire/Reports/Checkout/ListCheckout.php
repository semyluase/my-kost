<?php

namespace App\Livewire\Reports\Checkout;

use App\Models\Home;
use App\Models\TransactionRent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ListCheckout extends Component
{
    public $startDateCheckout;
    public $endDateCheckout;
    public $loadingCheckout = false;
    public $homeIDCheckout;
    public $homeListCheckout;

    function mount()
    {
        $this->startDateCheckout  = Carbon::now('Asia/Jakarta')->startOfDay();
        $this->endDateCheckout  = Carbon::now('Asia/Jakarta')->endOfDay();

        $this->homeIDCheckout = Auth::user()->home_id;
        $this->homeListCheckout = Home::where('is_active', true)->get();
    }

    #[On('listCheckout.searchReport')]
    function searchReport($startDate, $endDate, $homeID)
    {

        $this->startDateCheckout = Carbon::parse($startDate)->startOfDay();
        $this->endDateCheckout = Carbon::parse($endDate)->endOfDay();
        $this->homeIDCheckout = $homeID;

        $this->render();
    }

    #[On('listCheckout.downloadReport')]
    function downloadReport($startDate, $endDate, $homeID)
    {
        $this->loading();
        $this->startDateCheckout = Carbon::parse($startDate)->startOfDay();
        $this->endDateCheckout = Carbon::parse($endDate)->endOfDay();
        $this->homeIDCheckout = $homeID;

        $this->dispatch('listCheckout.generateExcel', [
            'startDateCheckout' =>  $this->startDateCheckout,
            'endDateCheckout' =>  $this->endDateCheckout,
            'homeIDCheckout' =>  $this->homeIDCheckout,
            'filename'  =>  "Rekap Laporan Checkout " . Carbon::parse($this->startDateCheckout)->isoFormat("DDMMYYYY") . ' - ' . Carbon::parse($this->endDateCheckout)->isoFormat("DDMMYYYY")
        ]);
    }

    #[On('listCheckout.loading')]
    function loading()
    {
        $this->loadingCheckout = $this->loadingCheckout ? false : true;
    }

    public function render()
    {
        $transactionRent = TransactionRent::with(['room'])->whereBetween('end_date', [Carbon::parse($this->startDateCheckout)->isoFormat("YYYY-MM-DD"), Carbon::parse($this->endDateCheckout)->isoFormat("YYYY-MM-DD")])
            ->whereHas('room', function ($query) {
                $query->where('home_id', $this->homeIDCheckout);
            })
            ->get();

        return view('livewire.reports.checkout.list-checkout', compact('transactionRent'));
    }
}
