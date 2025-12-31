<?php

namespace App\Livewire\Service\Order;

use App\Models\TransactionHeader;
use Livewire\Attributes\On;
use Livewire\Component;

class Detail extends Component
{
    public $showModalDetail = false;

    public $order;

    public function render()
    {
        return view('livewire.service.order.detail');
    }

    #[On('order.detailOrder')]
    function getDetailOrder($nobukti)
    {
        $this->order = TransactionHeader::with(['room', 'details', 'details.foodSnack'])->where('nobukti', $nobukti)
            ->first();

        $this->showModalDetail = true;
    }

    function closeModalDetail()
    {
        $this->showModalDetail = false;
    }
}
