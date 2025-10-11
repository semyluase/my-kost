<?php

namespace App\Livewire\Receipt;

use App\Models\TransactionDetail;
use App\Models\TransactionHeader;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class ListGoods extends Component
{
    public $noBukti;

    function mount()
    {
        $this->noBukti = request('nobukti');
    }
    #[On('listGoods.render')]
    public function render()
    {
        $dataDetails = TransactionDetail::with(['foodSnack'])->where('nobukti', $this->noBukti)
            ->get();

        return view('livewire.receipt.list-goods', [
            'dataDetails'   =>  $dataDetails,
        ]);
    }

    #[On('listGoods.refreshList')]
    function refreshList($noBukti)
    {
        $this->noBukti = $noBukti;
        $this->render();
    }

    function removeItem($id)
    {
        $detail = TransactionDetail::where('id', $id)
            ->first();

        DB::beginTransaction();

        if (TransactionDetail::where('id', $id)->delete()) {
            $header = TransactionHeader::where('nobukti', $detail->nobukti)
                ->first();
            if (TransactionHeader::where('nobukti', $detail->nobukti)
                ->update([
                    'total' =>  $header->total - ($detail->qty * $detail->harga_beli)
                ])
            ) {
                DB::commit();

                $this->dispatch('listGoods.swal-modal', [
                    'type' => 'success',
                    'message' => 'Berhasil',
                    'text' => 'Data dihapus'
                ]);

                $this->refreshList($this->noBukti);
            } else {

                DB::rollBack();

                $this->dispatch('listGoods.swal-modal', [
                    'type' => 'error',
                    'message' => 'Terjadi kesalahan',
                    'text' => 'Data gagal dihapus'
                ]);
            }
        } else {
            DB::rollBack();

            $this->dispatch('listGoods.swal-modal', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan',
                'text' => 'Data gagal dihapus'
            ]);
        }
    }
}
