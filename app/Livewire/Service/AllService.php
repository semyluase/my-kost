<?php

namespace App\Livewire\Service;

use Akaunting\Money\Money;
use App\Models\TransactionDetail;
use App\Models\TransactionHeader;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class AllService extends Component
{
    public $categoryService = "";
    public $statusService = "";
    public $search = "";
    public $checkTransaction = [];
    public $checkAllTransaction = false;

    #[On('service.render')]
    public function render()
    {
        $serviceTransaction = TransactionHeader::with(['room'])->where('is_receipt', false)
            ->whereNotNull('room_id')
            ->filterTransactionType($this->categoryService)
            ->filterTransactionStatus($this->statusService)
            ->filterTransaction($this->search)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('livewire.service.all-service', [
            'serviceTransaction'    =>  $serviceTransaction
        ]);
    }

    function printTransaction()
    {
        if (!$this->checkAllTransaction && empty($this->checkTransaction)) {
            $this->dispatch('allService.swal-modal', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan',
                'text' => 'Harap pilih transaksi yang akan dicetak'
            ]);
        }

        $this->dispatch('allService.generate-pdf', [
            'category'  =>  $this->categoryService,
            'search'    =>  $this->search,
            'nobuktiCheck'  =>  $this->checkTransaction,
        ]);
    }

    function receiptOrderLaundry($nobukti)
    {
        DB::beginTransaction();

        if (TransactionHeader::where('nobukti', $nobukti)->update([
            'status'    =>  2
        ])) {
            if (TransactionDetail::where('nobukti', $nobukti)->update([
                'tgl_masuk' =>  Carbon::now('Asia/Jakarta')
            ])) {
                DB::commit();

                $this->dispatch('allService.swal-modal', [
                    'type' => 'success',
                    'message' => 'Berhasil',
                    'text' => 'Laundry diterima'
                ]);

                $this->render();

                return true;
            }

            DB::rollBack();

            $this->dispatch('allService.swal-modal', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan',
                'text' => 'Saat penerimaan order laundry'
            ]);

            $this->render();

            return true;
        }

        DB::rollBack();

        $this->dispatch('allService.swal-modal', [
            'type' => 'error',
            'message' => 'Terjadi kesalahan',
            'text' => 'Saat penerimaan order laundry'
        ]);

        $this->render();
        return true;
    }

    function finishOrderLaundry($nobukti)
    {
        DB::beginTransaction();

        if (TransactionHeader::where('nobukti', $nobukti)->update([
            'status'    =>  5
        ])) {
            if (TransactionDetail::where('nobukti', $nobukti)->update([
                'tgl_selesai' =>  Carbon::now('Asia/Jakarta')
            ])) {
                DB::commit();

                $this->dispatch('allService.swal-modal', [
                    'type' => 'success',
                    'message' => 'Berhasil',
                    'text' => 'Laundry selesai'
                ]);

                $this->render();

                return true;
            }

            DB::rollBack();

            $this->dispatch('allService.swal-modal', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan',
                'text' => 'Saat menyelesaikan order laundry'
            ]);

            $this->render();

            return true;
        }

        DB::rollBack();

        $this->dispatch('allService.swal-modal', [
            'type' => 'error',
            'message' => 'Terjadi kesalahan',
            'text' => 'Saat menyelesaikan order laundry'
        ]);

        $this->render();
        return true;
    }

    function processCleaning($nobukti)
    {
        DB::beginTransaction();

        if (TransactionHeader::where('nobukti', $nobukti)->update([
            'status'    =>  2
        ])) {
            if (TransactionDetail::where('nobukti', $nobukti)->update([
                'tgl_mulai_cleaning' =>  Carbon::now('Asia/Jakarta')
            ])) {
                DB::commit();

                $this->dispatch('allService.swal-modal', [
                    'type' => 'success',
                    'message' => 'Berhasil',
                    'text' => 'Mulai pembersihan'
                ]);

                $this->render();

                return true;
            }

            DB::rollBack();

            $this->dispatch('allService.swal-modal', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan',
                'text' => 'Saat memulai pembersihan'
            ]);

            $this->render();

            return true;
        }

        DB::rollBack();

        $this->dispatch('allService.swal-modal', [
            'type' => 'error',
            'message' => 'Terjadi kesalahan',
            'text' => 'Saat memulai pembersihan'
        ]);

        $this->render();
        return true;
    }

    function finishProcessCleaning($nobukti)
    {
        DB::beginTransaction();

        if (TransactionHeader::where('nobukti', $nobukti)->update([
            'status'    =>  5
        ])) {
            if (TransactionDetail::where('nobukti', $nobukti)->update([
                'tgl_selesai_cleaning' =>  Carbon::now('Asia/Jakarta')
            ])) {
                DB::commit();

                $this->dispatch('allService.swal-modal', [
                    'type' => 'success',
                    'message' => 'Berhasil',
                    'text' => 'Selesai pembersihan'
                ]);

                $this->render();

                return true;
            }

            DB::rollBack();

            $this->dispatch('allService.swal-modal', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan',
                'text' => 'Saat menyelesaikan pembersihan'
            ]);

            $this->render();

            return true;
        }

        DB::rollBack();

        $this->dispatch('allService.swal-modal', [
            'type' => 'error',
            'message' => 'Terjadi kesalahan',
            'text' => 'Saat menyelesaikan pembersihan'
        ]);

        $this->render();
        return true;
    }

    function processOrder($nobukti)
    {
        DB::beginTransaction();

        if (TransactionHeader::where('nobukti', $nobukti)->update([
            'status'    =>  2
        ])) {
            DB::commit();

            $this->dispatch('allService.swal-modal', [
                'type' => 'success',
                'message' => 'Berhasil',
                'text' => 'Proses Pesanan'
            ]);

            $this->render();

            return true;
        }

        DB::rollBack();

        $this->dispatch('allService.swal-modal', [
            'type' => 'error',
            'message' => 'Terjadi kesalahan',
            'text' => 'Saat memproses pesanan'
        ]);

        $this->render();
        return true;
    }

    function finishOrder($nobukti)
    {
        DB::beginTransaction();

        if (TransactionHeader::where('nobukti', $nobukti)->update([
            'status'    =>  5
        ])) {
            DB::commit();

            $this->dispatch('allService.swal-modal', [
                'type' => 'success',
                'message' => 'Berhasil',
                'text' => 'Pesanan Selesai'
            ]);

            $this->render();

            return true;
        }

        DB::rollBack();

        $this->dispatch('allService.swal-modal', [
            'type' => 'error',
            'message' => 'Terjadi kesalahan',
            'text' => 'Saat menyelesaikan pesanan'
        ]);

        $this->render();
        return true;
    }
}
