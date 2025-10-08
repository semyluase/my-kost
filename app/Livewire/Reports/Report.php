<?php

namespace App\Livewire\Reports;

use App\Models\TransactionHeader;
use App\Models\Log\TransactionRent;
use Illuminate\Support\Carbon;
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

    function mount()
    {
        $this->startDate  = Carbon::now('Asia/Jakarta')->startOfDay();
        $this->endDate  = Carbon::now('Asia/Jakarta')->endOfDay();
    }

    public function render()
    {
        $transactions = TransactionHeader::with(['room', 'details'])->whereBetween('tanggal', [$this->startDate, $this->endDate])
            ->get();

        $rents = TransactionRent::with(['room'])->whereBetween('tgl', [$this->startDate, $this->endDate])
            ->orderBy('created_at')
            ->get();

        return view('livewire.reports.report', [
            'transactions'  =>  $transactions,
            'rents' =>  $rents,
        ]);
    }

    #[On('report.searchReport')]
    function searchReport($startDate, $endDate)
    {

        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate = Carbon::parse($endDate)->endOfDay();

        $this->render();
    }

    #[On('report.downloadReport')]
    function downloadReport($startDate, $endDate)
    {
        $this->loading();
        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate = Carbon::parse($endDate)->endOfDay();

        $this->dispatch('report.generateExcel', [
            'startDate' =>  $this->startDate,
            'endDate' =>  $this->endDate,
            'filename'  =>  "Rekap Laporan " . Carbon::parse($this->startDate)->isoFormat("DDMMYYYY") . ' - ' . Carbon::parse($this->endDate)->isoFormat("DDMMYYYY")
        ]);
    }

    #[On('report.loading')]
    function loading()
    {
        $this->loading = $this->loading ? false : true;
    }
}
