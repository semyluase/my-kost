<?php

namespace App\Livewire\Service\Order;

use App\Models\TransactionHeader;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class Payment extends Component
{
    public $showPembayaranModal = false;

    public $order = null;
    public $noBukti = "";
    public $noRoomOrder = "";
    public $typePaymentOrder = 'transfer';
    public $totalPriceOrder = 0;
    public $totalPaymentOrder = 0;
    public $rechargeOrder = 0;
    public function render()
    {
        return view('livewire.service.order.payment');
    }

    #[On('order.showModalPembayaran')]
    function showModalPembayaran($nobukti)
    {
        $this->order = TransactionHeader::with(['details', 'details.foodSnack'])->where('nobukti', $nobukti)
            ->first();

        $this->noBukti = $nobukti;
        $this->noRoomOrder = $this->order->details->first()->no_room;
        $this->typePaymentOrder = $this->order->tipe_pembayaran;
        if ($this->order->details()) {
            foreach ($this->order->details as $key => $value) {
                $this->totalPriceOrder += ($value->qty * $value->harga_jual);
            }
        }
        $this->totalPaymentOrder = $this->order->tipe_pembayaran == 'transfer' ? $this->totalPriceOrder : 0;
        $this->rechargeOrder = 0;
        $this->showPembayaranModal = true;
    }

    function onUpdatePayment()
    {
        $chargeOrder = intval($this->totalPaymentOrder) - $this->totalPriceOrder;
        $this->rechargeOrder = $chargeOrder < 0 ? 0 : $chargeOrder;
    }

    function onUpdatePaymentType()
    {
        $this->totalPaymentOrder = $this->typePaymentOrder == 'transfer' ? $this->totalPriceOrder : 0;
    }

    function savePembayaran()
    {
        if ($this->totalPaymentOrder == 0) {
            $this->dispatch('swal-modal', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan',
                'text' => 'Total pembayaran tidak boleh 0'
            ]);

            return false;
        }

        DB::beginTransaction();

        if (TransactionHeader::where('nobukti', $this->noBukti)->update([
            'tipe_pembayaran'   =>   $this->typePaymentOrder,
            'pembayaran'    =>  $this->totalPaymentOrder,
            'kembalian' =>  $this->totalPaymentOrder - $this->totalPriceOrder,
        ])) {
            DB::commit();

            $this->dispatch('order-payment.swal-modal', [
                'type' => 'success',
                'message' => 'Berhasil',
                'text' => 'Transaksi sudah lunas'
            ]);

            $this->showPembayaranModal = false;
            $this->dispatch('service.render');

            return true;
        };
        DB::rollback();

        $this->dispatch('swal-modal', [
            'type' => 'error',
            'message' => 'Terjadi kesalahan',
            'text' => 'Transaksi gagal dibayar'
        ]);

        return false;
    }

    function closeModalPembayaran()
    {
        $this->showPembayaranModal = false;
    }
}
