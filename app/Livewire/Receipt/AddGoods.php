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

use function App\Helper\generateNoTrans;

class AddGoods extends Component
{
    public $noBukti;
    public $code;
    public $name;
    public $category;
    public $dateTransaction;
    public int $stock = 0;
    public int $qty = 0;
    public int $price = 0;
    public int $total = 0;
    public $listeners = ["addGoodsSelect" => 'selectGoods'];

    function mount() {}

    public function render()
    {
        $this->dateTransaction = Carbon::now('Asia/Jakarta')->isoFormat("DD-MM-YYYY");
        return view('livewire.receipt.add-goods');
    }

    function selectGoods($code, $name, $category, $stock)
    {
        $this->code = $code;
        $this->name = $name;
        $this->category = $category;
        $this->stock = $stock;
    }

    function calculateTotal()
    {
        if ($this->qty != null && $this->price != null) {
            $this->total = $this->qty * $this->price;
        }
    }
    function saveReceipt()
    {
        $nobukti = $this->noBukti;

        $mode = 'update';

        $validator = Validator::make(['kodebrg' => $this->code], [
            'kodebrg'   =>  'required',
        ], [
            'kodebrg.required'  =>  'Barang belum dipilih. Silahkan pilih barang yang ada disebelah kanan'
        ])->validate();

        if ($nobukti == '') {
            $nobukti = generateNoTrans('RC');
            $mode = "insert";
        }

        DB::beginTransaction();

        $header = [
            "nobukti"   =>  $nobukti,
            "tanggal"   =>  Carbon::parse($this->dateTransaction)->isoFormat("YYYY-MM-DD"),
            'user_id'   =>  Auth::id(),
            'is_receipt'    =>  true,
        ];

        $detail = [
            "nobukti"   =>  $nobukti,
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
                    $this->dispatch('swal-modal', [
                        'type' => 'success',
                        'message' => 'Berhasil',
                        'text' => 'Data berhasil disimpan'
                    ]);

                    $this->noBukti = $nobukti;
                    $this->dispatch("listGoodsRefresh", noBukti: $this->noBukti);
                } else {

                    DB::rollBack();

                    $this->dispatch('swal-modal', [
                        'type' => 'error',
                        'message' => 'Terjadi kesalahan',
                        'text' => 'Data gagal disimpan'
                    ]);
                }
            } else {
                DB::rollBack();
                $this->dispatch('swal-modal', [
                    'type' => 'error',
                    'message' => 'Terjadi kesalahan',
                    'text' => 'Data gagal disimpan'
                ]);
            }
        } else {
            if (TransactionDetail::create($detail)) {
                DB::commit();

                $this->reset();
                $this->dispatch('swal-modal', [
                    'type' => 'success',
                    'message' => 'Berhasil',
                    'text' => 'Data berhasil disimpan'
                ]);

                $this->noBukti = $nobukti;
                // dd($this->dispatch("listGoodsRefresh.{$this->noBukti}"));
                $this->dispatch("listGoodsRefresh", noBukti: $this->noBukti);
            } else {
                DB::rollBack();
                $this->dispatch('swal-modal', [
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

                $this->dispatch('swal-modal', [
                    'type' => 'success',
                    'message' => 'Berhasil',
                    'text' => 'Berhasil menghapus data transaksi'
                ]);

                $this->dispatch("listGoodsRefresh", noBukti: null);
                $this->reset();
            } else {
                DB::rollback();

                $this->dispatch('swal-modal', [
                    'type' => 'error',
                    'message' => 'Terjadi kesalahan',
                    'text' => 'Gagal menghapus data transaksi'
                ]);
            }
        } else {
            DB::rollback();

            $this->dispatch('swal-modal', [
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
                $stock = Stock::where('code_item', $value->code_item)->first();

                if (Stock::where('id', $stock->id)->update([
                    'qty'   =>  $stock->qty + $value->qty,
                    'harga_beli'    =>  $value->harga_beli,
                ])) {
                    $totalUpdate++;
                }
            }
        }

        if ($totalUpdate > 0) {
            DB::commit();

            $this->dispatch('swal-modal', [
                'type' => 'success',
                'message' => 'Berhasil',
                'text' => 'Berhasil memposting data transaksi'
            ]);

            $this->dispatch("listGoodsRefresh", noBukti: null);
            $this->dispatch("masterGoodsrefresh");
            $this->reset();
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
