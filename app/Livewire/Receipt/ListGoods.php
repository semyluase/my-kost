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
    protected $listeners = ['listGoodsRefresh' => 'refreshList'];

    public function render()
    {
        $dataHeader = TransactionHeader::where('nobukti', $this->noBukti)
            ->first();

        $dataDetails = TransactionDetail::with(['foodSnack'])->where('nobukti', $this->noBukti)
            ->get();


        return view('livewire.receipt.list-goods', [
            'dataHeader'    =>  $dataHeader,
            'dataDetails'   =>  $dataDetails,
        ]);
    }

    function refreshList($noBukti)
    {
        $this->noBukti = $noBukti;
        $this->render();
    }

    function removeItem($id)
    {
        DB::beginTransaction();

        if (TransactionDetail::where('id', $id)->delete()) {
            DB::commit();

            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'success',
                'message' => 'Berhasil',
                'text' => 'Data berhasil dihapus'
            ]);

            $this->refreshList($this->noBukti);
        } else {
            DB::rollBack();

            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan',
                'text' => 'Data gagal dihapus'
            ]);
        }
    }
}
