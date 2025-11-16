<?php

namespace App\Livewire\Receipt;

use App\Models\FoodSnack;
use App\Models\Home;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class StockList extends Component
{
    public $showModal = false;
    public $search;
    public $homeID;
    public $homeList;
    public $items;

    function mount()
    {
        $this->homeList = Home::where('is_active', true)->get();
        $this->homeID = Auth::user()->home_id;
    }

    public function render()
    {
        $this->items = FoodSnack::GetDataStock($this->homeID)
            ->search(['search'  =>  $this->search])
            ->get();

        return view('livewire.receipt.stock-list');
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
