<?php

namespace App\Livewire\Service\Cleaning;

use App\Models\TransactionDetail;
use App\Models\TransactionHeader;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class Payment extends Component
{
    public $showPembayaranCleaningModal = false;

    public $categoryCleaning = "";
    public $nobuktiCleaning = "";
    public $roomCleaning = "";
    public $tanggalCleaning = "";
    public $typePaymentCleaning = "";
    public $priceCleaning = "";
    public $paymentCleaning = "";
    public $rechargeCleaning = "";

    public function render()
    {
        return view('livewire.service.cleaning.payment');
    }

    #[On('cleaning.showModalPembayaran')]
    function showModalPembayaran($nobukti)
    {
        $cleaning = TransactionDetail::with(['categoryCleaning'])->where('nobukti', $nobukti)
            ->first();
        $this->categoryCleaning = $cleaning->categoryCleaning ? $cleaning->categoryCleaning->description : "";
        $this->nobuktiCleaning = $nobukti;
        $this->roomCleaning = $cleaning->no_room;
        $this->tanggalCleaning = Carbon::parse($cleaning->tgl_request_cleaning)->isoFormat("DD-MM-YYYY HH:mm");
        $this->typePaymentCleaning = $cleaning->tipe_pembayaran ? $cleaning->tipe_pembayaran : 'transfer';
        $this->priceCleaning = $cleaning->harga_cleaning;
        $this->paymentCleaning = 0;
        $this->rechargeCleaning = 0;
        $this->showPembayaranCleaningModal = true;
    }

    function onUpdatePayment()
    {
        $chargeCleaning = intval($this->paymentCleaning) - $this->priceCleaning;
        $this->rechargeCleaning = $chargeCleaning < 0 ? 0 : $chargeCleaning;
    }

    function savePembayaran()
    {
        if ($this->paymentCleaning == 0) {
            $this->dispatch('payment-cleaning.swal-modal', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan',
                'text' => 'Total pembayaran tidak boleh 0'
            ]);

            return false;
        }

        DB::beginTransaction();

        if (TransactionDetail::where('nobukti', $this->nobuktiCleaning)->update([
            'tipe_pembayaran'   =>   $this->typePaymentCleaning,
            'pembayaran'    =>  $this->paymentCleaning,
            'kembalian' =>  $this->paymentCleaning - $this->priceCleaning,
            'is_payment'    =>  true,
        ])) {
            if (TransactionHeader::where('nobukti', $this->nobuktiCleaning)->update([
                'tipe_pembayaran'   =>   $this->typePaymentCleaning,
                'pembayaran'    =>  $this->paymentCleaning,
                'kembalian' =>  $this->paymentCleaning - $this->priceCleaning,
            ])) {
                DB::commit();

                $this->dispatch('payment-cleaning.swal-modal', [
                    'type' => 'success',
                    'message' => 'Berhasil',
                    'text' => 'Transaksi sudah lunas'
                ]);

                $this->showPembayaranCleaningModal = false;
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
        $this->showPembayaranCleaningModal = false;
    }
}
