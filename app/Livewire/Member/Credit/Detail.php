<?php

namespace App\Livewire\Member\Credit;

use App\Models\TransactionHeader;
use Livewire\Attributes\On;
use Livewire\Component;

class Detail extends Component
{
    public $showDetailModal = false;
    public $transaksi = [];

    public function render()
    {
        return view('livewire.member.credit.detail');
    }

    #[On('detail.showModal')]
    function showModal($userID)
    {
        $this->transaksi = TransactionHeader::where('user_id', $userID)
            ->whereRaw("(tipe_pembayaran = 'saldo' OR is_topup = true)")
            ->get();

        $this->showDetailModal = true;
    }

    function closeDetailModal()
    {
        $this->showDetailModal = false;
    }
}
