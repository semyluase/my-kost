<?php

namespace App\Livewire\Receipt;

use App\Models\Home;
use App\Models\TransactionHeader;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Receipt extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $lenght = 10;
    public $homeID;
    public $homeList;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    function mount()
    {
        $this->homeID = Auth::user()->home_id;
    }

    public function render()
    {
        $this->homeList = Home::where('is_active', true)->get();

        $receipt = TransactionHeader::where('is_receipt', true)
            ->where('home_id', $this->homeID)
            ->orderBy('tanggal', "desc")
            ->get();

        return view('livewire.receipt.receipt', [
            'receipts'  =>  $receipt,
        ]);
    }
}
