<?php

namespace App\Http\Controllers;

use App\Models\FoodSnack;
use App\Models\Stock;
use App\Models\TransactionDetail;
use App\Models\TransactionHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use function App\Helper\generateNoTrans;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stocks = Stock::with(['foodSnack'])->get();
        return view('Pages.Receipt.index', [
            'title' =>  "Barang Masuk",
            'pageTitle' =>  "Barang Masuk",
            'stocks'    =>  $stocks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $foodSnacks = FoodSnack::getData()->get();
        $transaction = TransactionHeader::where('nobukti', $request->nobukti)->first();

        return view('Pages.Receipt.create', [
            'title' =>  "Barang Masuk",
            'pageTitle' =>  "Barang Masuk",
            'foodSnacks'    =>  $foodSnacks,
            'transaction'    =>  $transaction,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $nobukti = $request->nobukti;

        $mode = 'update';

        if ($nobukti == '') {
            $nobukti = generateNoTrans('RC');
            $mode = "insert";
        }

        DB::beginTransaction();

        $header = [
            "nobukti"   =>  $nobukti,
            "tanggal"   =>  Carbon::parse($request->tanggal)->isoFormat("YYYY-MM-DD"),
            'user_id'   =>  auth()->user()->id,
        ];

        $detail = [
            "nobukti"   =>  $nobukti,
            "code_item" =>  $request->kodebrg,
            "type"  =>  "IN",
            "category"  =>  $request->kategori,
            "qty"   =>  $request->jumlah,
            "harga_beli"    =>  $request->hargaBeli,
        ];

        if ($mode == 'insert') {
            if (TransactionHeader::create($header)) {
                if (TransactionDetail::create($detail)) {
                    DB::commit();
                    return response()->json([
                        'data'  =>  [
                            'status'    =>  true,
                            'message'   =>  "Data berhasil disimpan",
                            'nobukti'   =>  $nobukti,
                        ]
                    ]);
                }

                DB::rollBack();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  "Data gagal disimpan - Transaksi Detail",
                        'nobukti'   =>  $nobukti,
                    ]
                ]);
            }

            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  "Data gagal disimpan - Transaksi Header",
                    'nobukti'   =>  $nobukti,
                ]
            ]);
        }


        if (TransactionHeader::create($header)) {
            if (TransactionDetail::create($detail)) {
                DB::commit();
                return response()->json([
                    'data'  =>  [
                        'status'    =>  true,
                        'message'   =>  "Data berhasil disimpan",
                        'nobukti'   =>  $nobukti,
                    ]
                ]);
            }

            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  "Data gagal disimpan - Transaksi Detail",
                    'nobukti'   =>  $nobukti,
                ]
            ]);
        }
    }

    /**
     * Posting a newly transaction resource in storage.
     */
    public function posting(Request $request)
    {
        $detailTransaction = TransactionDetail::where('nobukti', $request->nobukti)
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
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "Data berhasil diposting",
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Data gagal diposting",
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionHeader $receipt)
    {
        DB::beginTransaction();
        if (TransactionDetail::where('nobukti', $receipt->nobukti)->delete()) {
            if (TransactionHeader::find($receipt->id)->delete()) {
                DB::commit();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  true,
                        'message'   =>  "Data berhasil dihapus",
                    ]
                ]);
            }
            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  "Data gagal dihapus",
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Data gagal dihapus",
            ]
        ]);
    }

    public function deleteDetail(TransactionDetail $detail)
    {
        if (TransactionDetail::where('id', $detail->id)->delete()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "Data berhasil dihapus",
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Data gagal dihapus",
            ]
        ]);
    }

    function getDetailData(Request $request)
    {
        $dataHeader = TransactionHeader::where('nobukti', $request->nobukti)
            ->first();

        $dataDetails = collect(TransactionDetail::with(['foodSnack'])->where('nobukti', $request->nobukti)
            ->get())->chunk(10);

        $results = array();
        $no = 1;

        if ($dataDetails) {
            foreach ($dataDetails as $key => $chunk) {
                foreach ($chunk as $c => $value) {
                    $btnList = "#";

                    if ($dataHeader->status == 1) {
                        $btnList = '<div class="d-flex gap-2">
                                        <button class="btn btn-danger" title="Hapus Data" onclick="fnReceipt.onDeleteDetail(\'' . $value->id . '\',\'' . csrf_token() . '\')">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trash-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7h16" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /><path d="M10 12l4 4m0 -4l-4 4" /></svg>
                                        </button>
                                    </div>';
                    }

                    $results[] = [
                        $no,
                        $value->code_item,
                        $value->foodSnack->name,
                        $value->qty,
                        $value->harga_beli,
                        number_format($value->qty * $value->harga_beli),
                        $btnList,
                    ];

                    $no++;
                }
            }
        }

        return response()->json([
            'data'  =>  $results,
        ]);
    }
}
