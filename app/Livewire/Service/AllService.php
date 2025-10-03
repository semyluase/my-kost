<?php

namespace App\Livewire\Service;

use Akaunting\Money\Money;
use App\Models\Email;
use App\Models\TransactionDetail;
use App\Models\TransactionHeader;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class AllService extends Component
{
    public $categoryService = "";
    public $statusService = "";
    public $search = "";
    public $startDate;
    public $endDate;
    public $checkTransaction = [];
    public $checkAllTransaction = false;

    function mount()
    {
        $this->startDate = Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD");
        $this->endDate = Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD");
    }

    #[On('service.render')]
    public function render()
    {
        $serviceTransaction = TransactionHeader::with(['room'])->where('is_receipt', false)
            ->whereNotNull('room_id')
            ->filterTransactionType($this->categoryService)
            ->filterTransactionStatus($this->statusService)
            ->filterTransaction($this->search)
            ->filterByBranch()
            ->filterByDate($this->startDate, $this->endDate)
            ->orderBy('tgl_request', 'asc')
            ->get();

        return view('livewire.service.all-service', [
            'serviceTransaction'    =>  $serviceTransaction
        ]);
    }

    #[On('allService.searchData')]
    function searchData($startDate, $endDate)
    {
        $this->startDate = Carbon::parse($startDate)->isoFormat("YYYY-MM-DD");
        $this->endDate = Carbon::parse($endDate)->isoFormat("YYYY-MM-DD");

        $this->render();
    }

    function checkAllOrder()
    {
        if (!$this->checkAllTransaction) {
            $this->checkTransaction = [];
            return false;
        }

        $serviceTransaction = TransactionHeader::with(['room'])->where('is_receipt', false)
            ->whereNotNull('room_id')
            ->filterTransactionType($this->categoryService)
            ->filterTransactionStatus($this->statusService)
            ->filterTransaction($this->search)
            ->filterByBranch()
            ->filterByDate($this->startDate, $this->endDate)
            ->orderBy('tgl_request', 'asc')
            ->get();

        if ($serviceTransaction) {
            foreach ($serviceTransaction as $key => $value) {
                $this->checkTransaction[] = $value->nobukti;
            }
        }
    }

    function printTransaction()
    {
        if (empty($this->checkTransaction)) {
            $this->dispatch('allService.swal-modal', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan',
                'text' => 'Harap pilih transaksi yang akan dicetak'
            ]);
        }

        if ($this->checkAllTransaction) {
            $serviceTransaction = TransactionHeader::with(['room'])->where('is_receipt', false)
                ->whereNotNull('room_id')
                ->filterTransactionType($this->categoryService)
                ->filterTransactionStatus($this->statusService)
                ->filterTransaction($this->search)
                ->orderBy('tgl_request', 'asc')
                ->get();

            if ($serviceTransaction) {
                foreach ($serviceTransaction as $key => $value) {
                    $this->checkTransaction[] = $value->nobukti;
                }
            }
        }

        $this->dispatch('allService.generate-pdf', [
            'category'  =>  $this->categoryService,
            'search'    =>  $this->search,
            'nobuktiCheck'  =>  $this->checkTransaction,
        ]);
    }

    function printTransactionDaily()
    {
        $transactions = TransactionHeader::whereBetween('tgl_request', [
            Carbon::now('Asia/Jakarta')->startOfDay(),
            Carbon::now('Asia/Jakarta')->endOfDay()
        ])->get();

        $this->checkTransaction = $transactions->pluck('nobukti')->toArray();

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

        $transaction = TransactionHeader::with(['user'])->where('nobukti', $nobukti)
            ->first();

        if (TransactionHeader::find($transaction->id)->update([
            'status'    =>  5
        ])) {
            if (TransactionDetail::where('nobukti', $nobukti)->update([
                'tgl_selesai' =>  Carbon::now('Asia/Jakarta')
            ])) {
                $filePath = public_path('assets/invoice/' . $nobukti . '.pdf');

                Email::create([
                    'to'    =>  $transaction->user->email,
                    'subject'   =>  "Receipt " . $nobukti,
                    "attachment"    =>  $filePath,
                    'no_invoice'    =>  $nobukti,
                    'is_order'  =>  true,
                ]);

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

        $transaction = TransactionHeader::with(['user'])->where('nobukti', $nobukti)
            ->first();

        if (TransactionHeader::find($transaction->id)->update([
            'status'    =>  5
        ])) {
            if (TransactionDetail::where('nobukti', $nobukti)->update([
                'tgl_selesai_cleaning' =>  Carbon::now('Asia/Jakarta')
            ])) {
                $filePath = public_path('assets/invoice/' . $nobukti . '.pdf');

                Email::create([
                    'to'    =>  $transaction->user->email,
                    'subject'   =>  "Receipt " . $nobukti,
                    "attachment"    =>  $filePath,
                    'no_invoice'    =>  $nobukti,
                    'is_order'  =>  true,
                ]);

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

        $transaction = TransactionHeader::with(['user'])->where('nobukti', $nobukti)
            ->first();

        if (TransactionHeader::find($transaction->id)->update([
            'status'    =>  5
        ])) {
            $filePath = public_path('assets/invoice/' . $nobukti . '.pdf');

            Email::create([
                'to'    =>  $transaction->user->email,
                'subject'   =>  "Receipt " . $nobukti,
                "attachment"    =>  $filePath,
                'no_invoice'    =>  $nobukti,
                'is_order'  =>  true,
            ]);

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
