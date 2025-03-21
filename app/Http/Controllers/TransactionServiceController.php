<?php

namespace App\Http\Controllers;

use App\Models\Home;
use App\Models\Master\Service\Laundry;
use App\Models\Member;
use App\Models\Member\TopUp;
use App\Models\Pembayaran;
use App\Models\Room;
use App\Models\Service;
use App\Models\TransactionDetail;
use App\Models\TransactionHeader;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use function App\Helper\generateNoTrans;
use function App\Helper\makePhoneNumber;

class TransactionServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Pages.Services.index', [
            'title' =>  "Order",
            'pageTitle' =>  'Order'
        ]);
    }

    public function indexLaundry()
    {
        return view('Pages.Services.partials.indexLaundry', [
            'title' =>  "Laundry",
            'pageTitle' =>  'Laundry'
        ]);
    }

    public function indexCleaning()
    {
        return view('Pages.Services.partials.indexCleaning', [
            'title' =>  "Room Cleaning",
            'pageTitle' =>  'Room Cleaning'
        ]);
    }

    public function indexTopUp()
    {
        return view('Pages.Services.partials.indexTopUp', [
            'title' =>  "Top Up Saldo",
            'pageTitle' =>  'Top Up Saldo'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        switch ($request->service) {
            case 'laundry':
                $view = 'Pages.Services.laundry';
                $title = 'Laundry';
                $pageTitle = 'Laundry';

                $data = TransactionDetail::where('is_service', true)
                    ->where('tgl_masuk', Carbon::now('Asia/Jakarta'))
                    ->get();
                break;

            default:
                # code...
                break;
        }

        return view($view, [
            'title' =>  $title,
            'pageTitle' =>  $pageTitle,
            'data'  =>  $data
        ]);
    }

    public function createLaundry(Request $request)
    {
        $laundry = TransactionDetail::with(['categoryLaundry'])->where('nobukti', $request->nobukti)
            ->where('is_service', true)
            ->where('is_laundry', true)
            ->first();

        $laundryPriceReguler = Laundry::with(['categoryLaundry'])->where('is_active', true)
            ->whereHas('categoryLaundry', fn($query) => ($query->where('is_express', false)))
            ->orderBy('weight')
            ->get();

        $laundryPriceExpress = Laundry::with(['categoryLaundry'])->where('is_active', true)
            ->whereHas('categoryLaundry', fn($query) => ($query->where('is_express', true)))
            ->orderBy('weight')
            ->get();

        $payments = Pembayaran::where('is_active', true)
            ->get();

        $home = Home::where('id', auth()->user()->home_id)
            ->first();

        return view('Pages.Services.partials.createLaundry', [
            'title' =>  "Transaksi Laundry",
            'pageTitle' =>  "Transaksi Laundry",
            'home'   =>  $home,
            'payments'   =>  $payments,
            'laundry'   =>  $laundry,
            'laundryPriceReguler'  =>  $laundryPriceReguler,
            'laundryPriceExpress'  =>  $laundryPriceExpress,
        ]);
    }

    public function createCleaning(Request $request)
    {
        $cleaning = TransactionDetail::where('nobukti', $request->nobukti)
            ->where('is_service', true)
            ->where('is_cleaning', true)
            ->first();

        $home = Home::where('id', auth()->user()->home_id)
            ->first();

        return view('Pages.Services.partials.createCleaning', [
            'title' =>  "Transaksi Cleaning",
            'pageTitle' =>  "Transaksi Cleaning",
            'home'   =>  $home,
            'cleaning'   =>  $cleaning,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function storeLaundry(Request $request)
    {
        DB::beginTransaction();

        $nobukti = $request->nobukti;

        $mode = 'update';

        if ($nobukti == '') {
            $nobukti = generateNoTrans('LD');

            $mode = 'insert';
        }

        $room = Room::where('slug', $request->noKamar)->first();

        $price = Laundry::where('is_active', true)->get();

        $header = [
            'nobukti'   =>  $nobukti,
            'room_id'   =>  $room->id,
            'tanggal'   =>  Carbon::now('Asia/Jakarta'),
            'user_id'   =>  auth()->user()->id,
        ];

        $detail = [
            'nobukti'   =>  $nobukti,
            'is_service'    =>  true,
            'tgl_masuk' =>  Carbon::now('Asia/Jakarta'),
            'tgl_selesai'   =>  $request->kategori == 'reguler' ? Carbon::now('Asia/Jakarta')->addDays(1) : Carbon::now('Asia/Jakarta')->addHour(6),
            'room_id'   =>  $room->id,
            'no_room'   =>  $room->number_room,
            'is_express'    =>  $request->kategori == 'reguler' ? false : true,
            'is_laundry'    =>  true,
            'harga_laundry' =>  $price->harga,
            'qty_laundry'   =>  $request->berat,
            'pembayaran'    =>  $request->totalPayment ? $request->totalPayment : 0,
            'tipe_pembayaran'    =>  $request->totalPayment ? $request->payment : null,
            'kembalian'    =>  $request->totalPayment ? $request->totalPayment - ($price->harga * $request->berat) : 0,
            'is_verified'   =>  $request->totalPayment ? true : false,
            'is_payment'   =>  $request->totalPayment ? true : false,
        ];

        if ($mode == 'insert') {
            if (TransactionHeader::create($header)) {
                if (TransactionDetail::create($detail)) {
                    DB::commit();

                    return response()->json([
                        'data'  =>  [
                            'status'    =>  true,
                            'message'   =>  'Data berhasil diproses'
                        ]
                    ]);
                }

                DB::rollback();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  'Data gagal diproses'
                    ]
                ]);
            }

            DB::rollback();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Data gagal diproses'
                ]
            ]);
        }

        if ($mode == 'update') {
            if (TransactionDetail::create($detail)) {
                DB::commit();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  true,
                        'message'   =>  'Data berhasil diproses'
                    ]
                ]);
            }

            DB::rollback();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Data gagal diproses'
                ]
            ]);
        }
    }

    public function storeCleaning(Request $request)
    {
        DB::beginTransaction();

        $nobukti = $request->nobukti;

        $mode = 'update';

        if ($nobukti == '') {
            $nobukti = generateNoTrans('CL');

            $mode = 'insert';
        }

        $room = Room::where('slug', $request->noKamar)->first();

        $header = [
            'nobukti'   =>  $nobukti,
            'room_id'   =>  $room->id,
            'tanggal'   =>  Carbon::now('Asia/Jakarta'),
            'user_id'   =>  auth()->user()->id,
        ];

        $now = Carbon::now('Asia/Jakarta')->greaterThan(Carbon::createFromFormat('Y-m-d H:i', Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD") . " 18:00", 'Asia/Jakarta')) ? Carbon::now('Asia/Jakarta')->addDays(1) : Carbon::now('Asia/Jakarta');

        $detail = [
            'nobukti'   =>  $nobukti,
            'is_service'    =>  true,
            'tgl_request_cleaning' =>  Carbon::createFromFormat('Y-m-d H:i', $now->isoFormat("YYYY-MM-DD") . " " . $request->jamRequest, 'Asia/Jakarta'),
            'tgl_mulai_cleaning' =>  $request->jamMulai ? Carbon::createFromFormat('Y-m-d H:i', $now->isoFormat("YYYY-MM-DD") . " " . $request->jamMulai, 'Asia/Jakarta') : null,
            'tgl_selesai_cleaning' =>  $request->jamSelesai ? Carbon::createFromFormat('Y-m-d H:i', $now->isoFormat("YYYY-MM-DD") . " " . $request->jamSelesai, 'Asia/Jakarta') : null,
            'room_id'   =>  $room->id,
            'no_room'   =>  $room->number_room,
            'is_cleaning'    =>  true,
        ];

        if ($mode == 'insert') {
            if (TransactionHeader::create($header)) {
                if (TransactionDetail::create($detail)) {
                    DB::commit();

                    return response()->json([
                        'data'  =>  [
                            'status'    =>  true,
                            'message'   =>  'Data berhasil diproses',
                            'nobukti'   =>  $nobukti,
                        ]
                    ]);
                }

                DB::rollback();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  'Data gagal diproses',
                        'nobukti'   =>  ''
                    ]
                ]);
            }

            DB::rollback();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Data gagal diproses',
                    'nobukti'   =>  ''
                ]
            ]);
        }

        if ($mode == 'update') {
            if (TransactionDetail::create($detail)) {
                DB::commit();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  true,
                        'message'   =>  'Data berhasil diproses',
                        'nobukti'   =>  $nobukti,
                    ]
                ]);
            }

            DB::rollback();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Data gagal diproses',
                    'nobukti'   =>  ''
                ]
            ]);
        }
    }

    function startCleaning(Request $request)
    {
        DB::beginTransaction();

        $transaction = TransactionDetail::where('nobukti', $request->nobukti)->first();

        if (TransactionDetail::where('id', $transaction->id)->update([
            'tgl_mulai_cleaning'    =>  Carbon::now("Asia/Jakarta")
        ])) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "Mulai membersihkan kamar " . $transaction->no_room,
                ]
            ]);
        }

        DB::rollback();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Gagal mulai membersihkan kamar " . $transaction->no_room,
            ]
        ]);
    }

    function stopCleaning(Request $request)
    {
        DB::beginTransaction();

        $transaction = TransactionDetail::where('nobukti', $request->nobukti)->first();

        if (TransactionDetail::where('id', $transaction->id)->update([
            'tgl_selesai_cleaning'    =>  Carbon::now("Asia/Jakarta")
        ])) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "Selesai membersihkan kamar " . $transaction->no_room,
                ]
            ]);
        }

        DB::rollback();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Gagal selesai membersihkan kamar " . $transaction->no_room,
            ]
        ]);
    }

    function storeTopup(Request $request)
    {
        DB::beginTransaction();

        $member = User::where('phone_number', makePhoneNumber($request->member))->first();

        if (!$member) {
            $member = User::where('email', $request->member)->first();
        }

        if (!$member) {
            $member = User::where('username', $request->member)->first();
        }

        if (!$member) {
            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  "Member ini belum terdaftar"
                ]
            ]);
        }

        $nobukti = generateNoTrans('TP');

        $header = [
            'nobukti'   =>  $nobukti,
            'tanggal'   =>  Carbon::now('Asia/Jakarta'),
            'user_id'   =>  auth()->user()->id,
        ];

        $now = Carbon::now('Asia/Jakarta')->greaterThan(Carbon::createFromFormat('Y-m-d H:i', Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD") . " 18:00", 'Asia/Jakarta')) ? Carbon::now('Asia/Jakarta')->addDays(1) : Carbon::now('Asia/Jakarta');

        $detail = [
            'nobukti'   =>  $nobukti,
            'is_service'    =>  true,
            'is_topup'    =>  true,
            'qty'    =>  $request->jumlahTopup,
            'pembayaran'    =>  $request->payment,
            'tipe_pembayaran'    =>  $request->typePayment,
            'kembalian'    =>  $request->kembalian,
            'is_verify'    =>  true,
            'is_payment'    =>  true,
            'user_id'   =>  $member->id,
        ];

        if (TransactionHeader::create($header)) {
            if (TransactionDetail::create($detail)) {
                $credit = TopUp::where('user_id', $member->id)->first();

                if ($credit) {
                    if (TopUp::where('id', $credit->id)->update([
                        'credit'    => $credit->credit + $request->jumlahTopup
                    ])) {
                        DB::commit();

                        return response()->json([
                            'data'  =>  [
                                'status'    =>  true,
                                'message'   =>  'Data berhasil diproses',
                                'nobukti'   =>  $nobukti,
                            ]
                        ]);
                    }

                    DB::rollback();

                    return response()->json([
                        'data'  =>  [
                            'status'    =>  false,
                            'message'   =>  'Data gagal diproses',
                            'nobukti'   =>  ''
                        ]
                    ]);
                }

                if (TopUp::create([
                    'user_id'   => $member->id,
                    'credit'    => $request->jumlahTopup
                ])) {
                    DB::commit();

                    return response()->json([
                        'data'  =>  [
                            'status'    =>  true,
                            'message'   =>  'Data berhasil diproses',
                            'nobukti'   =>  $nobukti,
                        ]
                    ]);
                }

                DB::rollback();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  'Data gagal diproses',
                        'nobukti'   =>  ''
                    ]
                ]);
            }

            DB::rollback();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Data gagal diproses',
                    'nobukti'   =>  ''
                ]
            ]);
        }

        DB::rollback();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Data gagal diproses',
                'nobukti'   =>  ''
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

    function detailTopUp(Request $request)
    {
        $topups = TransactionHeader::getDataTopup(Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD"))
            ->get();

        return response()->json(view('Pages.Services.partials.topup.detailTransaction', [
            'topups'    =>  $topups
        ])->render());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionHeader $transactionHeader)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransactionHeader $transactionHeader)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionHeader $transactionHeader)
    {
        //
    }

    function getAllDataLaundry(Request $request)
    {
        $laundries = TransactionHeader::GetDataLaundry(Carbon::parse($request->s)->isoFormat("YYYY-MM-DD"), Carbon::parse($request->e)->isoFormat("YYYY-MM-DD"))
            ->orderBy('tgl_masuk', 'desc')
            ->get();

        $results = array();
        $no = 1;
        if ($laundries) {
            foreach ($laundries as $key => $value) {
                $btnAction = '<div class="d-flex gap-2">
                                <button class="btn btn-primary" title="Ambil Laundry" onclick="fnLaundry.onTakeLaundry(\'' . $value->nobukti . '\',\'' . csrf_token() . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-paper-bag"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 3h8a2 2 0 0 1 2 2v1.82a5 5 0 0 0 .528 2.236l.944 1.888a5 5 0 0 1 .528 2.236v5.82a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-5.82a5 5 0 0 1 .528 -2.236l1.472 -2.944v-3a2 2 0 0 1 2 -2z" /><path d="M14 15m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M6 21a2 2 0 0 0 2 -2v-5.82a5 5 0 0 0 -.528 -2.236l-1.472 -2.944" /><path d="M11 7h2" /></svg>
                                </button>
                                <button class="btn btn-info" title="Detail Laundry" onclick="fnLaundry.onDetailLaundry(\'' . $value->nobukti . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-eye"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                </button>
                            </div>';
                $results[] = [
                    $no,
                    $value->nobukti,
                    $value->no_room,
                    $value->is_express ? 'Express' : 'Reguler',
                    $value->tgl_masuk ? Carbon::parse($value->tgl_masuk)->isoFormat("DD-MM-YYYY HH:mm") : '',
                    $value->tgl_selesai ? Carbon::parse($value->tgl_selesai)->isoFormat("DD-MM-YYYY HH:mm") : '',
                    $value->tgl_ambil ? Carbon::parse($value->tgl_ambil)->isoFormat("DD-MM-YYYY HH:mm") : '',
                    $btnAction,
                ];
            }
        }

        return response()->json([
            'data'  =>  $results
        ]);
    }

    function getAllDataCleaning(Request $request)
    {
        $cleanings = TransactionHeader::GetDataCleaning(Carbon::parse($request->s)->isoFormat("YYYY-MM-DD"))
            ->orderBy('tgl_request_cleaning', 'desc')
            ->get();

        $results = array();
        $no = 1;
        if ($cleanings) {
            foreach ($cleanings as $key => $value) {
                $btnAction = '<div class="d-flex gap-2">';

                if (!$value->tgl_mulai_cleaning) {
                    $btnAction .= '<button class="btn btn-primary" title="Mulai Pembersihan" onclick="fnCleaning.onStartCleaning(\'' . $value->nobukti . '\',\'' . csrf_token() . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-player-play"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4v16a1 1 0 0 0 1.524 .852l13 -8a1 1 0 0 0 0 -1.704l-13 -8a1 1 0 0 0 -1.524 .852z" /></svg>
                                </button>';
                }

                if ($value->tgl_mulai_cleaning) {
                    $btnAction .= '<button class="btn btn-primary" title="Selesai Pembersihan" onclick="fnCleaning.onStopCleaning(\'' . $value->nobukti . '\',\'' . csrf_token() . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-player-stop"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 4h-10a3 3 0 0 0 -3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3 -3v-10a3 3 0 0 0 -3 -3z" /></svg>
                                </button>';
                }
                $btnAction .= '</div>';

                $results[] = [
                    $no,
                    $value->nobukti,
                    $value->no_room,
                    $value->tgl_request_cleaning ? Carbon::parse($value->tgl_request_cleaning)->isoFormat("HH:mm") : '',
                    $value->tgl_mulai_cleaning ? Carbon::parse($value->tgl_mulai_cleaning)->isoFormat("HH:mm") : '',
                    $value->tgl_selesai_cleaning ? Carbon::parse($value->tgl_selesai_cleaning)->isoFormat("HH:mm") : '',
                    $btnAction,
                ];
            }
        }

        return response()->json([
            'data'  =>  $results
        ]);
    }
}
