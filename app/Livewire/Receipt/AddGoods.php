<?php

namespace App\Livewire\Receipt;

use App\Models\Stock;
use App\Models\TransactionDetail;
use App\Models\TransactionHeader;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

use function App\Helper\generateCounterTransaction;
use function App\Helper\generateNoTrans;

class AddGoods extends Component
{
    public $noBukti;
    public $code;
    public $name;
    public $category;
    public $dateTransaction;
    public $status;
    public int $stock = 0;
    public int $qty = 0;
    public int $price = 0;
    public int $total = 0;
    public $listeners = ["addGoodsSelect" => 'selectGoods'];

    function mount()
    {
        $this->noBukti = request('nobukti');
    }

    public function render()
    {
        $dataReceipt = TransactionHeader::where('nobukti', $this->noBukti)
            ->first();
        $this->dateTransaction = Carbon::now('Asia/Jakarta')->isoFormat("DD-MM-YYYY");
        $this->status = '1';
        if ($dataReceipt) {
            $this->dateTransaction = Carbon::parse($dataReceipt->tanggal)->isoFormat("DD-MM-YYYY");
            $this->status = $dataReceipt->status;
        }

        $this->dispatch('listGoods.render', ["nobukti" => $this->noBukti]);

        return view('livewire.receipt.add-goods');
    }

    function selectGoods($code, $name, $category)
    {
        $this->code = $code;
        $this->name = $name;
        $this->category = $category;
    }

    function calculateTotal()
    {
        if ($this->qty != null && $this->price != null) {
            $this->total = $this->qty * $this->price;
        }
    }
    function saveReceipt()
    {
        $mode = 'update';

        $validator = Validator::make(['kodebrg' => $this->code], [
            'kodebrg'   =>  'required',
        ], [
            'kodebrg.required'  =>  'Barang belum dipilih. Silahkan pilih barang yang ada disebelah kanan'
        ])->validate();

        $noBukti = "";
        if ($this->noBukti == null) {
            $this->noBukti = generateCounterTransaction('RC');
            $noBukti = generateCounterTransaction('RC');
            $mode = "insert";
        }

        DB::beginTransaction();

        $header = [
            "nobukti"   =>  $this->noBukti,
            "tanggal"   =>  Carbon::parse($this->dateTransaction)->isoFormat("YYYY-MM-DD"),
            'user_id'   =>  Auth::id(),
            'is_receipt'    =>  true,
            "total"    =>  $this->qty * $this->price,
            "home_id"   =>  Auth::user()->home_id,
            'tgl_request'   =>  Carbon::now("Asia/Jakarta")
        ];

        $detail = [
            "nobukti"   =>  $this->noBukti,
            "code_item" =>  $this->code,
            "type"  =>  "IN",
            "category"  =>  $this->category,
            "qty"   =>  $this->qty,
            "harga_beli"    =>  $this->price,
        ];

        if ($mode == 'insert') {
            if (TransactionHeader::create($header)) {
                if (TransactionDetail::create($detail)) {
                    DB::commit();

                    $this->reset();
                    $this->noBukti = $noBukti;
                    $this->dispatch('addGoods.swal-modal', [
                        'type' => 'success',
                        'message' => 'Berhasil',
                        'text' => 'Data berhasil disimpan'
                    ]);

                    $this->dispatch("listGoods.refreshList", noBukti: $this->noBukti);
                } else {

                    DB::rollBack();

                    $this->dispatch('addGoods.swal-modal', [
                        'type' => 'error',
                        'message' => 'Terjadi kesalahan',
                        'text' => 'Data gagal disimpan'
                    ]);
                }
            } else {
                DB::rollBack();
                $this->dispatch('addGoods.swal-modal', [
                    'type' => 'error',
                    'message' => 'Terjadi kesalahan',
                    'text' => 'Data gagal disimpan'
                ]);
            }
        } else {
            $dataHeader = TransactionHeader::where('nobukti', $this->noBukti)->first();

            if (TransactionDetail::create($detail)) {
                if (TransactionHeader::where('id', $dataHeader->id)
                    ->update([
                        'total' =>  $dataHeader->total + ($this->qty * $this->price)
                    ])
                ) {
                    DB::commit();

                    $this->reset();
                    $this->noBukti = $dataHeader->nobukti;
                    $this->dispatch('addGoods.swal-modal', [
                        'type' => 'success',
                        'message' => 'Berhasil',
                        'text' => 'Data berhasil disimpan'
                    ]);
                    $this->dispatch("listGoods.refreshList", noBukti: $this->noBukti);
                } else {
                    DB::rollBack();
                    $this->dispatch('addGoods.swal-modal', [
                        'type' => 'error',
                        'message' => 'Terjadi kesalahan',
                        'text' => 'Data gagal disimpan'
                    ]);
                }
            } else {
                DB::rollBack();
                $this->dispatch('addGoods.swal-modal', [
                    'type' => 'error',
                    'message' => 'Terjadi kesalahan',
                    'text' => 'Data gagal disimpan'
                ]);
            }
        }
    }

    function removeAllReceipt()
    {
        DB::beginTransaction();

        if (TransactionHeader::where('nobukti', $this->noBukti)->delete()) {
            if (TransactionDetail::where('nobukti', $this->noBukti)->delete()) {
                DB::commit();

                $this->dispatch('addGoods.swal-modal', [
                    'type' => 'success',
                    'message' => 'Berhasil',
                    'text' => 'Berhasil menghapus data transaksi'
                ]);

                $this->dispatch("listGoodsRefresh", noBukti: null);
                $this->reset();
                $this->redirect(url('/inventories/receipts'));
            } else {
                DB::rollback();

                $this->dispatch('addGoods.swal-modal', [
                    'type' => 'error',
                    'message' => 'Terjadi kesalahan',
                    'text' => 'Gagal menghapus data transaksi'
                ]);
            }
        } else {
            DB::rollback();

            $this->dispatch('addGoods.swal-modal', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan',
                'text' => 'Gagal menghapus data transaksi'
            ]);
        }
    }

    function postingReceipt()
    {
        $detailTransaction = TransactionDetail::where('nobukti', $this->noBukti)
            ->get();

        DB::beginTransaction();

        $totalUpdate = 0;
        if ($detailTransaction) {
            foreach ($detailTransaction as $key => $value) {
                $stock = Stock::where('code_item', $value->code_item)
                    ->where('home_id', Auth::user()->home_id)
                    ->first();

                if (Stock::where('id', $stock->id)->update([
                    'qty'   =>  $stock->qty + $value->qty,
                    'harga_beli'    =>  $value->harga_beli,
                ])) {
                    $totalUpdate++;
                }
            }
        }

        if ($totalUpdate > 0) {
            TransactionHeader::where('nobukti', $this->noBukti)->update([
                'status'    =>  5
            ]);

            DB::commit();

            $this->dispatch('swal-modal', [
                'type' => 'success',
                'message' => 'Berhasil',
                'text' => 'Berhasil memposting data transaksi'
            ]);

            $this->dispatch("listGoodsRefresh", noBukti: null);
            $this->reset();
            $this->redirect(url('/inventories/receipts'));
        } else {
            DB::rollback();

            $this->dispatch('swal-modal', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan',
                'text' => 'Gagal memposting data transaksi'
            ]);
        }
    }
}
