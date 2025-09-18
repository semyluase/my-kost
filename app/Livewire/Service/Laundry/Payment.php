<?php

namespace App\Livewire\Service\Laundry;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\TransactionDetail;
use App\Models\TransactionHeader;
use Illuminate\Support\Facades\DB;
use Masmerise\Toaster\Toaster;

class Payment extends Component
{
    public $showPembayaranLaundryModal = false;

    // laundry
    public $categoryLaundry = "";
    public $nobuktiLaundry = "";
    public $roomLaundry = "";
    public $beratLaundry = "";
    public $typePaymentLaundry = "";
    public $priceLaundry = "";
    public $paymentLaundry = "";
    public $rechargeLaundry = "";


    public function render()
    {
        return view('livewire.service.laundry.payment');
    }

    #[On('laundry.showModalPembayaran')]
    function showModalPembayaran($nobukti)
    {
        $laundry = TransactionDetail::with(['categorylaundry'])->where('nobukti', $nobukti)
            ->first();
        $this->categoryLaundry = $laundry->categorylaundry->name;
        $this->nobuktiLaundry = $nobukti;
        $this->roomLaundry = $laundry->no_room;
        $this->beratLaundry = $laundry->categorylaundry->weight > 0 ? $laundry->categorylaundry->weight : $laundry->qty_laundry;
        $this->typePaymentLaundry = "transfer";
        $this->priceLaundry = $laundry->harga_laundry;
        $this->paymentLaundry = 0;
        $this->rechargeLaundry = 0;
        $this->showPembayaranLaundryModal = true;
    }

    function onUpdatePayment()
    {
        $chargeLaundry = intval($this->paymentLaundry) - $this->priceLaundry;
        $this->rechargeLaundry = $chargeLaundry < 0 ? 0 : $chargeLaundry;
    }

    function savePembayaran()
    {
        if ($this->paymentLaundry == 0) {
            $this->dispatch('swal-modal', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan',
                'text' => 'Total pembayaran tidak boleh 0'
            ]);

            return false;
        }

        DB::beginTransaction();

        if (TransactionDetail::where('nobukti', $this->nobuktiLaundry)->update([
            'tipe_pembayaran'   =>   $this->typePaymentLaundry,
            'pembayaran'    =>  $this->paymentLaundry,
            'kembalian' =>  $this->paymentLaundry - $this->priceLaundry,
            'is_payment'    =>  true,
        ])) {
            if (TransactionHeader::where('nobukti', $this->nobuktiLaundry)->update([
                'tipe_pembayaran'   =>   $this->typePaymentLaundry,
                'pembayaran'    =>  $this->paymentLaundry,
                'kembalian' =>  $this->paymentLaundry - $this->priceLaundry,
            ])) {
                DB::commit();

                $this->dispatch('payment.swal-modal', [
                    'type' => 'success',
                    'message' => 'Berhasil',
                    'text' => 'Transaksi sudah lunas'
                ]);

                $this->showPembayaranLaundryModal = false;
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
        $this->showPembayaranLaundryModal = false;
    }
}
