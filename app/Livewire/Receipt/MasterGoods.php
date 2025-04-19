<?php

namespace App\Livewire\Receipt;

use App\Models\FoodSnack;
use Livewire\Component;

class MasterGoods extends Component
{
    protected $listeners = ["masterGoodsrefresh" => 'render'];
    public function render()
    {
        $foodSnacks = FoodSnack::getData()->get();

        return view('livewire.receipt.master-goods', [
            'goods' =>  $foodSnacks
        ]);
    }
}
