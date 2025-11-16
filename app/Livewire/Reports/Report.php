<?php

namespace App\Livewire\Reports;

use App\Models\Home;
use App\Models\TransactionHeader;
use App\Models\Log\TransactionRent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Report extends Component
{
    // use WithPagination, WithoutUrlPagination;

    public string $startDate;
    public string $endDate;
    public $loading = false;
    public $homeID;
    public $homeList;

    function mount()
    {
        $this->startDate  = Carbon::now('Asia/Jakarta')->startOfDay();
        $this->endDate  = Carbon::now('Asia/Jakarta')->endOfDay();

        $this->homeID = Auth::user()->home_id;
        $this->homeList = Home::where('is_active', true)->get();
    }

    public function render()
    {
        $transactions = TransactionHeader::with(['room', 'details'])->whereBetween('tanggal', [$this->startDate, $this->endDate])
            ->where('home_id', $this->homeID)
            ->get();

        $rents = TransactionRent::with(['room'])->whereBetween('tgl', [$this->startDate, $this->endDate])
            ->where('home_id', $this->homeID)
            ->orderBy('created_at')
            ->get();

        return view('livewire.reports.report', [
            'transactions'  =>  $transactions,
            'rents' =>  $rents,
        ]);
    }

    #[On('report.searchReport')]
    function searchReport($startDate, $endDate, $homeID)
    {

        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate = Carbon::parse($endDate)->endOfDay();
        $this->homeID = $homeID;

        $this->render();
    }

    #[On('report.downloadReport')]
    function downloadReport($startDate, $endDate, $homeID)
    {
        $this->loading();
        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate = Carbon::parse($endDate)->endOfDay();
        $this->homeID = $homeID;

        $this->dispatch('report.generateExcel', [
            'startDate' =>  $this->startDate,
            'endDate' =>  $this->endDate,
            'homeID' =>  $this->homeID,
            'filename'  =>  "Rekap Laporan " . Carbon::parse($this->startDate)->isoFormat("DDMMYYYY") . ' - ' . Carbon::parse($this->endDate)->isoFormat("DDMMYYYY")
        ]);
    }

    #[On('report.loading')]
    function loading()
    {
        $this->loading = $this->loading ? false : true;
    }
}
