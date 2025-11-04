<?php

namespace App\Livewire\Receipt;

use App\Models\FoodSnack;
use Livewire\Attributes\On;
use Livewire\Component;

class StockList extends Component
{
    public $showModal = false;
    public $search;

    public function render()
    {
        $items = FoodSnack::with(['categoryOrder', 'stock'])
            ->search(['search'  =>  $this->search])
            ->get();

        return view('livewire.receipt.stock-list', compact('items'));
    }

    #[On('stockList.showModal')]
    function showModal()
    {
        $this->showModal = true;
    }

    #[On('stockList.closeModal')]
    function closeModal()
    {
        $this->showModal = false;
    }
}
