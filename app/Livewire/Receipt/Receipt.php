<?php

namespace App\Livewire\Receipt;

use App\Models\TransactionHeader;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Receipt extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $lenght = 10;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $receipt = TransactionHeader::where('is_receipt', true)
            ->paginate();


        return view('livewire.receipt.receipt', [
            'receipts'  =>  $receipt,
        ]);
    }
}
