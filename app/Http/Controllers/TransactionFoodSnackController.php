<?php

namespace App\Http\Controllers;

use App\Models\CategoryOrder;
use App\Models\FoodSnack;
use App\Models\Member\TopUp;
use App\Models\Room;
use App\Models\Stock;
use App\Models\TransactionDetail;
use App\Models\TransactionHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use function App\Helper\generateCounterTransaction;
use function App\Helper\generateNoTrans;

class TransactionFoodSnackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Pages.Services.partials.indexFoodSnack', [
            'title' =>  "Order",
            'pageTitle' =>  "Order",
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $foodSnack = TransactionHeader::with(['room'])->where('nobukti', request()->nobukti)
            ->first();

        return view('Pages.Services.partials.createFoodSnack', [
            'title' =>  "Order",
            'pageTitle' =>  "Order",
            'foodSnack' =>  $foodSnack,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'noKamar'   =>  'required'
        ], [
            'noKamar.required'  =>  'No Kamar belum dipilih'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors(),
                    'nobukti'   =>  ""
                ]
            ]);
        }

        $nobukti = $request->nobukti;
        $mode = 'update';

        if ($nobukti == '') {
            $nobukti  = generateCounterTransaction('FS', Auth::user()->home_id, Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD"));

            $mode = 'insert';
        }

        $room = Room::with(['rent', 'rent.member', 'rent.member.user'])->where('slug', $request->noKamar)->first();

        $foodSnack = FoodSnack::where('code_item', $request->kodeBarang)
            ->first();

        $stock = Stock::where('code_item', $request->kodeBarang)
            ->first();

        if ($mode == 'insert') {
            $header = [
                'nobukti'   =>  $nobukti,
                'room_id'   =>  $room->id,
                'tanggal'   =>  Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD"),
                'is_order'  =>  true,
                'tgl_request'   =>  Carbon::now('Asia/Jakarta'),
                'total' =>  $foodSnack->price,
                'user_id'   =>  $room->rent->member->user_id,
                'home_id'   =>  Auth::user()->home_id,
            ];

            $detail = [
                'nobukti'   =>  $nobukti,
                'code_item' =>  $foodSnack->code_item,
                'qty'   =>  $request->jumlah,
                'type'  =>  'OUT',
                'category'  =>  $foodSnack->category,
                'room_id'   =>  $room->id,
                'no_room'   =>  $room->number_room,
                'harga_jual'    =>  $foodSnack->price,
                'user_id'   =>  $room->rent->member->user_id,
            ];

            $updateStock = [
                'qty'   =>  $stock->qty - $request->jumlah,
            ];

            if (TransactionHeader::create($header)) {
                if (TransactionDetail::create($detail)) {
                    if (Stock::where('code_item', $foodSnack->code_item)->update($updateStock)) {
                        DB::commit();

                        return response()->json([
                            'data'  =>  [
                                'status'    =>  true,
                                'message'   =>  "Transaksi berhasil diproses",
                                'nobukti'   =>  $nobukti,
                            ]
                        ]);
                    }

                    DB::rollBack();

                    return response()->json([
                        'data'  =>  [
                            'status'    =>  false,
                            'message'   =>  "Transaksi gagal diproses",
                            'nobukti'   =>  "",
                        ]
                    ]);
                }

                DB::rollBack();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  "Transaksi gagal diproses",
                        'nobukti'   =>  "",
                    ]
                ]);
            }

            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  "Transaksi gagal diproses",
                    'nobukti'   =>  "",
                ]
            ]);
        }

        if ($mode == 'update') {
            $dataDetail = TransactionDetail::where('nobukti', $nobukti)
                ->where('code_item', $foodSnack->code_item)
                ->first();

            if ($dataDetail) {
                $detail = [
                    'qty'   =>  $dataDetail->qty + $request->jumlah,
                ];

                $updateHeader = [
                    'total' =>  $stock->harga_jual * ($dataDetail->qty + $request->jumlah),
                    'home_id'   =>  Auth::user()->home_id,
                ];

                $updateStock = [
                    'qty'   =>  $stock->qty - $request->jumlah,
                ];

                if (TransactionDetail::where('id', $dataDetail->id)->update($detail)) {
                    if (Stock::where('code_item', $foodSnack->code_item)->update($updateStock)) {
                        if (TransactionHeader::where('nobukti', $nobukti)->update($updateHeader)) {

                            DB::commit();

                            return response()->json([
                                'data'  =>  [
                                    'status'    =>  true,
                                    'message'   =>  "Transaksi berhasil diproses",
                                    'nobukti'   =>  $nobukti,
                                ]
                            ]);
                        }
                        DB::rollBack();

                        return response()->json([
                            'data'  =>  [
                                'status'    =>  false,
                                'message'   =>  "Transaksi gagal diproses",
                                'nobukti'   =>  "",
                            ]
                        ]);
                    }

                    DB::rollBack();

                    return response()->json([
                        'data'  =>  [
                            'status'    =>  false,
                            'message'   =>  "Transaksi gagal diproses",
                            'nobukti'   =>  "",
                        ]
                    ]);
                }

                DB::rollBack();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  "Transaksi gagal diproses",
                        'nobukti'   =>  "",
                    ]
                ]);
            }

            $detail = [
                'nobukti'   =>  $nobukti,
                'code_item' =>  $foodSnack->code_item,
                'qty'   =>  $request->jumlah,
                'type'  =>  'OUT',
                'category'  =>  $foodSnack->category,
                'room_id'   =>  $room->id,
                'no_room'   =>  $room->number_room,
                'harga_jual'    =>  $foodSnack->price,
                'user_id'   =>  $room->rent->member->user_id,
            ];

            $header = TransactionHeader::where('nobukti', $nobukti)
                ->first();

            $updateHeader = [
                'total' =>  $header->total + ($request->jumlah * $foodSnack->price),
                'home_id'   =>  Auth::user()->home_id,
            ];

            $updateStock = [
                'qty'   =>  $stock->qty - $request->jumlah,
            ];

            if (TransactionDetail::create($detail)) {
                if (Stock::where('code_item', $foodSnack->code_item)->update($updateStock)) {
                    if (TransactionHeader::where('id', $header->id)->update($updateHeader)) {
                        DB::commit();

                        return response()->json([
                            'data'  =>  [
                                'status'    =>  true,
                                'message'   =>  "Transaksi berhasil diproses",
                                'nobukti'   =>  $nobukti,
                            ]
                        ]);
                    }
                    DB::rollBack();

                    return response()->json([
                        'data'  =>  [
                            'status'    =>  false,
                            'message'   =>  "Transaksi gagal diproses",
                            'nobukti'   =>  "",
                        ]
                    ]);
                }

                DB::rollBack();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  "Transaksi gagal diproses",
                        'nobukti'   =>  "",
                    ]
                ]);
            }

            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  "Transaksi gagal diproses",
                    'nobukti'   =>  "",
                ]
            ]);
        }
    }

    function storePayment(Request $request)
    {
        DB::beginTransaction();
        $dataHeader = TransactionHeader::with(['details', 'room', 'room.rent', 'room.rent.member.memberCredit'])->where('nobukti', $request->nobukti)
            ->first();

        if ($request->tipePayment == 'saldo') {
            if ($dataHeader->room->rent->member->memberCredit->credit < $request->totalharga) {
                DB::rollBack();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  "Saldo tidak mencukupi",
                    ]
                ]);
            }

            $header = [
                'total' =>  $request->totalharga,
                'tipe_pembayaran'   =>  $request->tipePayment,
                'pembayaran'    =>  $request->payment,
                'kembalian' =>  $request->kembalian,
                'status'    =>  1
            ];

            $memberCredit = [
                'credit'    =>  $dataHeader->room->rent->member->memberCredit->credit - $request->payment
            ];

            if (TransactionHeader::where('id', $dataHeader->id)->update($header)) {
                if (TopUp::where('id', $dataHeader->room->rent->member->memberCredit->id)->update($memberCredit)) {
                    DB::commit();

                    return response()->json([
                        'data'  =>  [
                            'status'    =>  true,
                            'message'   =>  "Transaksi Lunas",
                        ]
                    ]);
                }

                DB::rollBack();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  "Transaksi Belum Lunas",
                    ]
                ]);
            }

            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  "Transaksi Belum Lunas",
                ]
            ]);
        }

        $header = [
            'total' =>  $request->totalharga,
            'tipe_pembayaran'   =>  $request->tipePayment,
            'pembayaran'    =>  $request->payment,
            'kembalian' =>  $request->kembalian,
            'status'    =>  1,
            'home_id'   =>  Auth::user()->home_id,
        ];

        if (TransactionHeader::where('id', $dataHeader->id)->update($header)) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "Transaksi Lunas",
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Transaksi Belum Lunas",
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionHeader $transactionHeader)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionDetail $id)
    {
        return response()->json($id->load(['foodSnack', 'stock']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        DB::beginTransaction();

        $dataDetail = TransactionDetail::where('nobukti', $request->nobukti)
            ->where('code_item', $request->kodeBarang)
            ->first();

        $stock = Stock::where('code_item', $request->kodeBarang)
            ->first();

        $detail = [
            'qty'   =>  $request->jumlah,
        ];

        $updateStock = [
            'qty'   =>  $stock->qty + ($dataDetail->qty - $request->jumlah),
        ];

        if (TransactionDetail::where('id', $dataDetail->id)->update($detail)) {
            if (Stock::where('id', $stock->id)->update($updateStock)) {
                DB::commit();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  true,
                        'message'   =>  "Transaksi berhasil diproses",
                        'nobukti'   =>  $request->nobukti,
                    ]
                ]);
            }

            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  "Transaksi gagal diproses",
                    'nobukti'   =>  "",
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Transaksi gagal diproses",
                'nobukti'   =>  "",
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionHeader $transactionHeader)
    {
        //
    }

    public function destroyDetail(TransactionDetail $detail)
    {
        DB::beginTransaction();

        $dataHeader = TransactionHeader::where('nobukti', $detail->nobukti)
            ->first();

        $stock = Stock::where('code_item', $detail->code_item)
            ->first();

        $updateStock = [
            'qty'   =>  $stock->qty + ($detail->qty),
        ];

        if (TransactionDetail::where('id', $detail->id)->delete()) {
            if (Stock::where('id', $stock->id)->update($updateStock)) {
                DB::commit();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  true,
                        'message'   =>  'Data berhasil diproses'
                    ]
                ]);
            }

            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Data gagal diproses'
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Data gagal diproses'
            ]
        ]);
    }

    function getAllData(Request $request)
    {
        $orders = TransactionHeader::getDataTransaction(Carbon::parse($request->s)->isoFormat("YYYY-MM-DD"), Carbon::parse($request->e)->isoFormat("YYYY-MM-DD"))
            ->get();

        $no = 1;
        $results = array();

        if ($orders) {
            foreach ($orders as $key => $value) {
                $pembayaran = "-";

                if ($value->tipe_pembayaran) {
                    $pembayaran = '<div>
                                    <div class="badge badge-success h3">' . Str::upper($value->tipe_pembayaran) . '</div>
                                    <div class="text-secondary">Total Bayar : Rp. ' . number_format($value->pembayaran, 0, ",", ".") . '</div>
                                    <div class="text-secondary">Total Kembalian : Rp. ' . number_format($value->kembalian, 0, ",", ".") . '</div>
                                </div>';
                }
                $results[] = [
                    $no,
                    $value->status == 5 ? $value->nobukti : '<a href="' . url('') . '/transactions/orders/food-snack/create?nobukti=' . $value->nobukti . '">' . $value->nobukti . '</a>',
                    $value->number_room,
                    'Rp. ' . number_format($value->total, 0, ",", "."),
                    $pembayaran,
                    $value->status == 5 ? '<div class="badge badge-success h3">' . Str::upper("LUNAS") . '</div>' : '<div class="badge badge-success h3">' . Str::upper("BELUM LUNAS") . '</div>'
                ];

                $no++;
            }
        }

        return response()->json([
            'data'  =>  $results
        ]);
    }

    function getListMenu()
    {
        $kategori = CategoryOrder::where('is_active', true)->get();

        return response()->json(view('Pages.Services.partials.foodSnack.offCanvas.parts.menu', [
            'categories'    =>  $kategori
        ])->render());
    }

    function receipt(Request $request)
    {
        $receipts = collect(TransactionDetail::GetReceipt($request->nobukti)
            ->get())->chunk(100);

        $no = 1;
        $results = array();

        if ($receipts) {
            foreach ($receipts as $key => $chunk) {
                foreach ($chunk as $c => $value) {
                    $btnAction = '<div class="d-flex gap-2">
                                    <button class="btn btn-warning" title="Ubah Transaksi" onclick="fnCreateFoodSnack.onEditReceipt(\'' . $value->id . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    </button>
                                    <button class="btn btn-danger" title="Hapus Transaksi" onclick="fnCreateFoodSnack.onDeleteReceipt(\'' . $value->id . '\',\'' . csrf_token() . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                    </button>
                                </div>';
                    $results[] = [
                        $no,
                        $value->name,
                        $value->jumlah,
                        $value->sub_total,
                        $btnAction
                    ];

                    $no++;
                }
            }
        }

        return response()->json([
            'data'  =>  $results
        ]);
    }

    function getPayment(TransactionHeader $header)
    {
        return response()->json(view('Pages.Services.partials.foodSnack.offCanvas.parts.payment', [
            'header'    =>  $header->load(['details', 'details.foodSnack', 'details.stock']),
        ])->render());
    }
}
