<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\Home;
use App\Models\Room;
use App\Models\User;
use App\Models\Member;
use App\Models\Service;
use App\Models\Pembayaran;
use Illuminate\Support\Str;
use App\Models\Member\TopUp;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\TransactionRent;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\TransactionDetail;
use App\Models\TransactionHeader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Master\Service\Laundry;
use App\Models\Master\Service\Cleaning;
use function App\Helper\makePhoneNumber;
use Illuminate\Support\Facades\Validator;
use function App\Helper\generateCounterTransaction;
use function React\Promise\all;

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

    function memberSaldo()
    {
        return view('Pages.Services.indexMemberCredit', [
            'title' =>  "Saldo Member",
            'pageTitle' =>  'Saldo Member'
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

        $laundryPrice = Laundry::where('is_active', true)
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
            'laundryPrice'  =>  $laundryPrice,
        ]);
    }

    public function createCleaning(Request $request)
    {
        $cleaning = TransactionDetail::where('nobukti', $request->nobukti)
            ->where('is_service', true)
            ->where('is_cleaning', true)
            ->first();

        $categoryCleaning = Cleaning::where('is_active', true)
            ->get();

        $home = Home::where('id', auth()->user()->home_id)
            ->first();

        return view('Pages.Services.partials.createCleaning', [
            'title' =>  "Transaksi Cleaning",
            'pageTitle' =>  "Transaksi Cleaning",
            'home'   =>  $home,
            'cleaning'   =>  $cleaning,
            'categoryCleaning'   =>  $categoryCleaning,
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
        $validator = Validator::make($request->all(), [
            'noKamar'   =>  'required',
            'kategori'  =>  'required',
        ], [
            'noKamar.required'  =>  'Harap pilih kamar',
            'kategori.required' =>  'Harap pilih kategori laundry'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }
        DB::beginTransaction();

        $nobukti = $request->nobukti;

        $mode = 'update';

        if ($nobukti == '') {
            $nobukti = generateCounterTransaction('LD');

            $mode = 'insert';
        }

        $room = Room::with(['rent', 'rent.member', 'rent.member.user'])->where('slug', $request->noKamar)->first();

        $price = Laundry::where('kode_item', $request->kategori)->where('is_active', true)->first();

        $saldo = TopUp::where('user_id', $room->rent->member->user->id)
            ->first();

        // dd($saldo);
        if ($saldo) {
            if ($saldo->credit < $price->price) {
                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  "Maaf, Saldo penghuni tidak cukup"
                    ]
                ]);
            }
        }

        $header = [
            'nobukti'   =>  $nobukti,
            'room_id'   =>  $room->id,
            'is_laundry'    =>  true,
            'tanggal'   =>  Carbon::now('Asia/Jakarta'),
            'tgl_request'   =>  Carbon::now('Asia/Jakarta'),
            'total' =>  $price->price,
            'pembayaran'    =>  intval($request->totalPayment),
            'tipe_pembayaran'    =>  $request->payment,
            'user_id'   =>  $room->rent->member->user_id,
            'home_id'   =>  Auth::user()->home_id,
        ];

        $detail = [
            'nobukti'   =>  $nobukti,
            'is_service'    =>  true,
            'room_id'   =>  $room->id,
            'no_room'   =>  $room->number_room,
            'is_laundry'    =>  true,
            'laundry_id' =>  $price->id,
            'harga_laundry' =>  $price->price,
            'qty_laundry'   =>  $request->berat,
            'pembayaran'    =>  $request->totalPayment ? $request->totalPayment : 0,
            'tipe_pembayaran'    =>  $request->payment,
            'kembalian'    =>  $request->totalPayment ? $request->totalPayment - $price->harga : 0,
            'is_verified'   =>  $request->totalPayment ? true : false,
            'is_payment'   =>  $request->totalPayment ? true : false,
            'user_id'   =>  $room->rent->member->user_id,
        ];

        if ($mode == 'insert') {
            if (TransactionHeader::create($header)) {
                if (TransactionDetail::create($detail)) {
                    if ($request->payment == 'saldo') {
                        TopUp::find($saldo->id)->update([
                            'credit'    =>  $saldo->credit - $request->totalPayment
                        ]);
                    }
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
        $validator = Validator::make($request->all(), [
            'noKamar'    =>  'required',
            'jamRequest'    =>  'required',
        ], [
            'noKamar.required'    =>  'Kamar harus dipilih',
            'jamRequest.required'    =>  'Jam Request harus diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        if (!Str::contains($request->jamRequest, ':')) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  "Format Jam Request harus HH:mm"
                ]
            ]);
        }

        $dataCleaning = TransactionHeader::where('tgl_request', Carbon::createFromFormat("Y-m-d H:i", $request->tanggal . ' ' . $request->jamRequest))
            ->first();

        if ($dataCleaning) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Jam pembersihan sudah ada, mohon diisi kembali jam lainnya'
                ]
            ]);
        }


        DB::beginTransaction();

        $nobukti = $request->nobukti;

        $mode = 'update';

        if ($nobukti == '') {
            $nobukti = generateCounterTransaction('CL');

            $mode = 'insert';
        }

        $room = Room::with(['rent', 'rent.member', 'rent.member.user'])->where('slug', $request->noKamar)->first();
        $priceCleaning = Cleaning::where('is_active', true)->first();

        $saldo = TopUp::where('user_id', $room->rent->member->user->id)
            ->first();

        if ($saldo) {
            if ($saldo->credit < $request->totalBayar) {
                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  'Maaf, Saldo tidak mencukupi untuk transaksi ini'
                    ]
                ]);
            }
        }

        $header = [
            'nobukti'   =>  $nobukti,
            'room_id'   =>  $room->id,
            'tanggal'   =>  Carbon::parse($request->tanggal),
            'tipe_pembayaran'   =>  $request->typePayment,
            'pembayaran'   =>  $request->totalBayar,
            'kembalian'   =>  $request->kembalian,
            'total'   =>  $priceCleaning->price,
            'tgl_request' =>  Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($request->tanggal)->isoFormat("Y-M-D") . " " . $request->jamRequest, 'Asia/Jakarta'),
            'user_id'   =>  $room->rent->member->user_id,
            'is_cleaning'    =>  true,
            'home_id'   =>  Auth::user()->home_id,
        ];

        $detail = [
            'nobukti'   =>  $nobukti,
            'code_item'    =>  $request->kategori,
            'is_service'    =>  true,
            'tgl_request_cleaning' =>  Carbon::createFromFormat('Y-m-d H:i', Carbon::parse($request->tanggal)->isoFormat("Y-M-D") . " " . $request->jamRequest, 'Asia/Jakarta'),
            'room_id'   =>  $room->id,
            'no_room'   =>  $room->number_room,
            'price_cleaning' => $priceCleaning->price,
            'tipe_pembayaran'   =>  $request->typePayment,
            'pembayaran'   =>  $priceCleaning->price,
            'kembalian'   =>  $request->kembalian,
            'is_cleaning'    =>  true,
            'is_payment'    =>  true,
            'is_verify'    =>  true,
            'cleaning_id'   =>  $priceCleaning->id,
            'user_id'   =>  $room->rent->member->user_id,
        ];

        if ($mode == 'insert') {
            if (TransactionHeader::create($header)) {
                if (TransactionDetail::create($detail)) {
                    if ($request->typePayment == 'saldo') {
                        TopUp::where('id', $saldo->id)->update([
                            'credit'    =>  $saldo->credit - $request->totalBayar
                        ]);
                    }

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
            if (TransactionHeader::where('nobukti', $nobukti)->update([
                'status'    =>  1
            ])) {
                if (TransactionDetail::where('nobukti', $nobukti)->update($detail)) {
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
    }

    function storeTopup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member'    =>  'required'
        ], [
            'member.required'   =>  "No HP/Email/Username Member harus diisi"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        DB::beginTransaction();

        $member = User::where('phone_number', makePhoneNumber($request->member))
            ->orWhere('email', $request->member)
            ->orWhere('username', Str::lower($request->member))
            ->first();

        if (!$member) {
            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  "Member ini belum terdaftar"
                ]
            ]);
        }

        $rent = TransactionRent::where('member_id', $member->member->id)
            ->where('is_change_room', false)
            ->where('is_checkout_abnormal', false)
            ->where('is_checkout_normal', false)
            ->first();

        $nobukti = generateCounterTransaction('TP');

        $header = [
            'nobukti'   =>  $nobukti,
            'tanggal'   =>  Carbon::now('Asia/Jakarta'),
            'user_id'   =>  $member->id,
            'room_id'   =>  $rent->room_id,
            'tgl_request'   =>  Carbon::now('Asia/Jakarta'),
            'total' =>  $request->jumlahTopup,
            'home_id'   =>  Auth::user()->home_id,
            'pembayaran'    =>  $request->payment,
            'status'    =>  5,
            'is_topup'  =>  true,
            'tipe_pembayaran'    =>  $request->typePayment,
        ];

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
                        $filePath = public_path('assets/invoice/' . $nobukti . '.pdf');

                        Email::create([
                            'to'    =>  $member->email,
                            'subject'   =>  "Receipt " . $nobukti,
                            "attachment"    =>  $filePath,
                            'no_invoice'    =>  $nobukti,
                            'is_order'  =>  true,
                        ]);
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
                    $filePath = public_path('assets/invoice/' . $nobukti . '.pdf');

                    Email::create([
                        'to'    =>  $member->email,
                        'subject'   =>  "Receipt " . $nobukti,
                        "attachment"    =>  $filePath,
                        'no_invoice'    =>  $nobukti,
                        'is_order'  =>  true,
                    ]);
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

    function getDetailLaundry(Request $request)
    {
        $dataLaundry = TransactionDetail::with(['categorylaundry'])->where('nobukti', $request->nobukti)
            ->first();

        return response()->json($dataLaundry);
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
                $btnAction = '<div class="d-flex gap-2">';

                if ($value->status == '1') {
                    $btnAction .= '<button class="btn btn-primary" title="Terima Laundry" onclick="fnLaundry.receiveLaundry(\'' . $value->nobukti . '\',\'' . csrf_token() . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-archive"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10" /><path d="M10 12l4 0" /></svg>
                                </button>';
                }

                if ($value->status == '2') {
                    $btnAction .= '<button class="btn btn-primary" title="Selesai Laundry" onclick="fnLaundry.finishLaundry(\'' . $value->nobukti . '\',\'' . csrf_token() . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-pennant"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 2a1 1 0 0 1 .993 .883l.007 .117v.35l8.406 3.736c.752 .335 .79 1.365 .113 1.77l-.113 .058l-8.406 3.735v7.351h1a1 1 0 0 1 .117 1.993l-.117 .007h-4a1 1 0 0 1 -.117 -1.993l.117 -.007h1v-17a1 1 0 0 1 1 -1z" /></svg>
                                </button>';
                }

                if ($value->status == '4') {
                    $btnAction .= '<button class="btn btn-primary" title="Ambil Laundry" onclick="fnLaundry.onTakeLaundry(\'' . $value->nobukti . '\',\'' . $value->is_payment . '\',\'' . csrf_token() . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-paper-bag"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 3h8a2 2 0 0 1 2 2v1.82a5 5 0 0 0 .528 2.236l.944 1.888a5 5 0 0 1 .528 2.236v5.82a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-5.82a5 5 0 0 1 .528 -2.236l1.472 -2.944v-3a2 2 0 0 1 2 -2z" /><path d="M14 15m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M6 21a2 2 0 0 0 2 -2v-5.82a5 5 0 0 0 -.528 -2.236l-1.472 -2.944" /><path d="M11 7h2" /></svg>
                                </button>';
                }

                if (!$value->is_payment) {
                    $btnAction .= '<button class="btn btn-primary" title="Paymenr Laundry" onclick="fnLaundry.onPayment(\'' . $value->nobukti . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-brand-mastercard"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M12 9.765a3 3 0 1 0 0 4.47" /><path d="M3 5m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /></svg>
                                </button>';
                }

                $btnAction .= '</div>';
                $results[] = [
                    $no,
                    $value->nobukti . ' ' . ($value->is_payment ? '<span class="badge bg-teal text-teal-fg">Lunas</span>' : '<span class="badge bg-pink text-pink-fg">Belum Lunas</span>'),
                    $value->no_room,
                    $value->name,
                    $value->tgl_masuk ? Carbon::parse($value->tgl_masuk)->isoFormat("DD-MM-YYYY HH:mm") : '',
                    $value->tgl_selesai ? Carbon::parse($value->tgl_selesai)->isoFormat("DD-MM-YYYY HH:mm") : '',
                    $value->tgl_ambil ? Carbon::parse($value->tgl_ambil)->isoFormat("DD-MM-YYYY HH:mm") : '',
                    $btnAction,
                ];

                $no++;
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

    function generatePdf(Request $request)
    {
        $serviceTransaction = TransactionHeader::with(['room', 'details', 'details.foodSnack', 'details.categoryCleaning', 'details.categoryLaundry'])->where('is_receipt', false)
            ->whereNotNull('room_id')
            ->where('status', '<>', 5)
            ->filterTransactionType($request->category)
            ->filterTransaction($request->search)
            ->filterByNobukti($request->nobuktiCheck ? explode(',', $request->nobuktiCheck) : [])
            ->orderBy('created_at', 'asc')
            ->get();

        $home = Home::where('id', Auth::user()->home_id)
            ->first();

        $pdf = Pdf::loadView('Pages.Services.PDF.receipt', [
            'transaction'   =>  $serviceTransaction,
            'home'  =>  $home
        ]);
        return $pdf->stream("receipt.pdf");
    }

    function generatePdfEmail(Request $request)
    {
        $dataTransaction = TransactionHeader::where('nobukti', $request->nobukti)
            ->first();

        $path = public_path('assets/image/Asset 1.png');
        $imageData = file_get_contents($path);
        $mimeType = mime_content_type($path);
        $base64 = base64_encode($imageData);
        $dataUri = 'data:' . $mimeType . ';base64,' . $base64;

        $pdf = Pdf::loadView('Pages.Services.PDF.receiptEmailAttach', [
            'data'  =>  $dataTransaction,
            'image' =>  $dataUri
        ]);

        // $filePath = public_path('assets/invoice/' . $dataTransaction->nobukti . '.pdf');
        return $pdf->stream($dataTransaction->nobukti . '.pdf');
        // $pdf->save($filePath);
    }
}
