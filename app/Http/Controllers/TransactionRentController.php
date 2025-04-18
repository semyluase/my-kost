<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Deposite;
use App\Models\Member;
use App\Models\Room;
use App\Models\TMP\UserIdentity;
use App\Models\TransactionRent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Number;

use function App\Helper\makePhoneNumber;

class TransactionRentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = collect(Category::where('is_active', true)->get())->chunk(10);

        return view('Pages.Transaction.index', [
            'title' =>  'Transaction',
            'pageTitle' =>  'Transaction',
            'categories'    =>  $category
        ]);
    }

    public function changeRoom(Request $request)
    {
        return view('Pages.Transaction.changeRoom', [
            'title' =>  'Transaction - Pindah Kamar',
            'pageTitle' =>  'Transaction - Pindah Kamar',
        ]);
    }

    public function checkout(Request $request)
    {
        $room = Room::with(['category', 'rent', 'rent.member', 'rent.member.user', 'rent.member.user.deposite'])->where('slug', $request->room)->first();

        return view('Pages.Transaction.checkout', [
            'title' =>  'Transaction - Checkout',
            'pageTitle' =>  'Transaction - Checkout',
            'room'  =>  $room,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $room = Room::where('slug', $request->room)->first();

        return view('Pages.Transaction.createRent', [
            'title' =>  'Sewa Kamar',
            'pageTitle' =>  'Sewa Kamar',
            'room'  =>  $room,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        $user = User::where('phone_number', makePhoneNumber($request->noHP))
            ->first();

        $member = Member::where('phone_number', makePhoneNumber($request->noHP))
            ->first();

        $userIdentify = UserIdentity::where('token', $request->tokenFoto)->first();

        $userFoto = UserIdentity::where('token', $request->tokenFotoOrang)->first();

        $room = Room::with(['category.prices'])->where('slug', $request->room)
            ->where('home_id', auth()->user()->home_id)
            ->first();

        $rent = null;
        if ($member) {
            $rent = TransactionRent::where('room_id', $room->id)
                ->where('member_id', $member->id)
                ->where('end_date', Carbon::parse($request->endRentDate)->isoFormat("YYYY-MM-DD"))
                ->first();
        }

        $dataUser = array();
        $dataMember = array();
        $dataRent = array();

        if (!$user) {
            $dataUser = [
                'role_id'   =>  3,
                'home_id'   =>  auth()->user()->home_id,
                'phone_number'  =>  makePhoneNumber($request->noHP),
                'name'  =>  Str::title($request->name),
                'password'  =>  bcrypt(Carbon::parse($request->tanggalLahir)->isoFormat("DDMMYYYY")),
                'foto_identity' =>  $userFoto->id,
            ];
        }

        if (collect($dataUser)->count() == 0) {
            $user = User::create($dataUser);
        } else {
            User::find($user->id)->update([
                'home_id'   =>  $user->home_id == null ? auth()->user()->home_id : $user->home_id,
                'foto_identity' =>  $userFoto->id,
            ]);
        }

        if (!$member) {
            $dataMember = [
                'user_id'   =>  $user->id,
                'type_identity' =>  $request->identity,
                'identity'  =>  $request->identityNumber,
                'address'   =>  $request->address,
                'phone_number'  =>  makePhoneNumber($request->noHP),
                'dob'   =>  Carbon::parse($request->tanggalLahir)->isoFormat("YYYY-MM-DD"),
                'identity_id'   =>  $userIdentify->id,
            ];
        } else {
            $dataMember = [
                'type_identity' =>  $request->identity,
                'identity'  =>  $request->identityNumber,
                'address'   =>  $request->address,
                'phone_number'  =>  makePhoneNumber($request->noHP),
                'dob'   =>  Carbon::parse($request->tanggalLahir)->isoFormat("YYYY-MM-DD"),
                'identity_id'   =>  $userIdentify->id,
            ];
        }

        if (collect($dataMember)->count() > 0) {
            if (!$member) {
                $member = Member::create($dataMember);
            } else {
                Member::find($member->id)->update($dataMember);
            }
        }

        if (!$rent) {
            switch ($request->durasi) {
                case 'mingguan':
                    $price = $room->category->prices[1]->price;
                    $endDateRent = Carbon::parse($request->startRentDate)->addDays(6)->isoFormat("YYYY-MM-DD");
                    $durasi = 'weekly';
                    break;

                case 'bulanan':
                    $price = $room->category->prices[2]->price;
                    $endDateRent = Carbon::parse($request->startRentDate)->addDays(29)->isoFormat("YYYY-MM-DD");
                    $durasi = 'monthly';
                    break;

                case 'tahunan':
                    $price = $room->category->prices[3]->price;
                    $endDateRent = Carbon::parse($request->startRentDate)->addDays(364)->isoFormat("YYYY-MM-DD");
                    $durasi = 'yearly';
                    break;

                default:
                    $price = $room->category->prices[0]->price;
                    $endDateRent = Carbon::parse($request->startRentDate)->addDay(1)->isoFormat("YYYY-MM-DD");
                    $durasi = 'daily';
                    break;
            }

            $dataRent = [
                'room_id'   =>  $room->id,
                'member_id' =>  $member->id,
                'start_date'    =>  Carbon::parse($request->startRentDate)->isoFormat("YYYY-MM-DD"),
                'end_date'  =>  $endDateRent,
                'price' =>  $price,
                'duration'  =>  $durasi
            ];
        }

        if (collect($dataRent)->count() > 0) {
            $rent = TransactionRent::create($dataRent);
        }

        if ($rent) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Transaksi sewa kamar berhasil disimpan',
                    'noKamar'   =>  $room->slug,
                ]
            ]);
        }

        DB::roolback();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Transaksi sewa kamar gagal disimpan'
            ]
        ]);
    }

    function storeChangeRoom(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'noKamarBaru'   =>  'required',
        ], [
            'noKamarBaru.required'   =>  'Kamar Baru harus dipilih'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors(),
                ]
            ]);
        }

        $dataKamar = Room::with(['category', 'category.prices', 'rent', 'rent.member', 'rent.member.user'])
            ->where('number_room', $request->noKamarLama)
            ->first();

        $dataKamarBaru = Room::with(['category', 'category.prices', 'rent', 'rent.member', 'rent.member.user'])
            ->where('number_room', $request->noKamarBaru)
            ->first();

        if ($dataKamar->category->slug == $dataKamarBaru->category->slug) {
            $hargaKamarBaru = $dataKamarBaru->category->prices->where('type', $dataKamar->rent->duration)->first()->price;
            $updateDataKamar = [
                'is_change_room'  =>  true,
                'tanggal_transaksi' =>  Carbon::now('Asia/Jakarta')
            ];

            $insertDataKamarBaru = [
                'room_id'   =>  $dataKamarBaru->id,
                'member_id' =>  $dataKamar->rent->member->id,
                'start_date'    =>  Carbon::parse($dataKamar->rent->start_date),
                'end_date'    =>  Carbon::parse($dataKamar->rent->end_date),
                'price' =>  $hargaKamarBaru,
                'duration'  =>  $dataKamar->rent->duration,
                'old_room_id'   =>  $dataKamar->id,
                'is_approve'    =>  true,
                'tanggal_transaksi' => Carbon::now('Asia/Jakarta'),
            ];

            if (TransactionRent::create($insertDataKamarBaru)) {
                if (TransactionRent::find($dataKamar->rent->id)->update($updateDataKamar)) {
                    DB::commit();

                    return response()->json([
                        'data'  =>  [
                            'status'    =>  true,
                            'message'   =>  'Berhasil simpan transaksi',
                            'url'   =>  '/transactions/rent-rooms',
                        ]
                    ]);

                    DB::rollback();

                    return response()->json([
                        'data'  =>  [
                            'status'    =>  false,
                            'message'   =>  'Gagal simpan transaksi',
                        ]
                    ]);
                }

                DB::rollback();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  'Gagal simpan transaksi',
                    ]
                ]);
            }

            DB::rollback();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Gagal simpan transaksi',
                ]
            ]);
        }

        if ($dataKamar->category->slug != $dataKamarBaru->category->slug) {
            $hargaKamarLama = $dataKamar->category->prices->where('type', $dataKamar->rent->duration)->first()->price;
            $hargaKamarBaru = $dataKamarBaru->category->prices->where('type', $dataKamar->rent->duration)->first()->price;
            if ($hargaKamarLama < $hargaKamarBaru) {
                $sisaSewa = round(Carbon::now('Asia/Jakarta')->diffInDays(Carbon::parse($dataKamar->rent->end_date), true), 0);
                $totalSewa = round(Carbon::parse($dataKamar->rent->start_date)->diffInDays(Carbon::parse($dataKamar->rent->end_date), true), 0);
                $kurangBayar = round((($sisaSewa / $totalSewa) * $hargaKamarBaru) - (($sisaSewa / $totalSewa) * $hargaKamarLama), 0);
                $pembulatan = 0;
                if (substr($kurangBayar, -3) < 500) {
                    $pembulatan = 500 - substr($kurangBayar, -3);
                } else {
                    $pembulatan = 1000 - substr($kurangBayar, -3);
                }

                $updateDataKamar = [
                    'is_change_room'  =>  true,
                    'is_upgrade'  =>  true,
                    'tanggal_transaksi' =>  Carbon::now('Asia/Jakarta')
                ];

                $insertDataKamarBaru = [
                    'room_id'   =>  $dataKamarBaru->id,
                    'member_id' =>  $dataKamar->rent->member->id,
                    'start_date'    =>  Carbon::parse($dataKamar->rent->start_date),
                    'end_date'    =>  Carbon::parse($dataKamar->rent->end_date),
                    'price' =>  $hargaKamarBaru,
                    'duration'  =>  $dataKamar->rent->duration,
                    'old_room_id'   =>  $dataKamar->id,
                    'tanggal_transaksi' => Carbon::now('Asia/Jakarta'),
                    'sisa_hari_sewa'    =>  $sisaSewa,
                    'total_hari_sewa'   =>  $totalSewa,
                    'kurang_bayar'  =>  $kurangBayar,
                    'pembulatan'    =>  $pembulatan,
                ];

                if (TransactionRent::create($insertDataKamarBaru)) {
                    if (TransactionRent::find($dataKamar->rent->id)->update($updateDataKamar)) {
                        DB::commit();

                        return response()->json([
                            'data'  =>  [
                                'status'    =>  true,
                                'message'   =>  'Berhasil Upgrade kamar',
                                'url'   =>  '/transactions/rent-rooms/detail-rents/' . $dataKamarBaru->number_room,
                            ]
                        ]);
                    }

                    DB::rollback();

                    return response()->json([
                        'data'  =>  [
                            'status'    =>  false,
                            'message'   =>  'Gagal Upgrade kamar',
                            'url'   =>  null,
                        ]
                    ]);
                }

                DB::rollback();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  'Gagal Upgrade kamar',
                        'url'   =>  null,
                    ]
                ]);
            } else {
                $hargaKamarBaru = $dataKamarBaru->category->prices->where('type', $dataKamar->rent->duration)->first()->price;
                $updateDataKamar = [
                    'is_change_room'  =>  true,
                    'is_downgrade'  =>  true,
                    'tanggal_transaksi' =>  Carbon::now('Asia/Jakarta')
                ];

                $insertDataKamarBaru = [
                    'room_id'   =>  $dataKamarBaru->id,
                    'member_id' =>  $dataKamar->rent->member->id,
                    'start_date'    =>  Carbon::parse($dataKamar->rent->start_date),
                    'end_date'    =>  Carbon::parse($dataKamar->rent->end_date),
                    'price' =>  $hargaKamarBaru,
                    'duration'  =>  $dataKamar->rent->duration,
                    'old_room_id'   =>  $dataKamar->id,
                    'is_approve'    =>  true,
                    'tanggal_transaksi' => Carbon::now('Asia/Jakarta'),
                ];

                if (TransactionRent::create($insertDataKamarBaru)) {
                    if (TransactionRent::find($dataKamar->rent->id)->update($updateDataKamar)) {
                        DB::commit();

                        return response()->json([
                            'data'  =>  [
                                'status'    =>  true,
                                'message'   =>  'Berhasil simpan transaksi',
                                'url'   =>  '/transactions/rent-rooms',
                            ]
                        ]);

                        DB::rollback();

                        return response()->json([
                            'data'  =>  [
                                'status'    =>  false,
                                'message'   =>  'Gagal simpan transaksi',
                            ]
                        ]);
                    }

                    DB::rollback();

                    return response()->json([
                        'data'  =>  [
                            'status'    =>  false,
                            'message'   =>  'Gagal simpan transaksi',
                        ]
                    ]);
                }

                DB::rollback();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  'Gagal simpan transaksi',
                    ]
                ]);
            }
        }
    }

    function storeCheckout(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'pengembalian'  =>  'required',
        ], [
            'pengembalian.required' =>  'Pengembalian Deposit harus diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        if ($request->jenisPengembalian == 'transfer') {
            $validator = Validator::make($request->all(), [
                'bank'  =>  'required',
                'noRek' =>  'required',
                'pengembalian'  =>  'required',
            ], [
                'bank.required' =>  'Bank harus dipilih',
                'noRek.required'    =>  'No Rekening harus diisi',
                'pengembalian.required' =>  'Pengembalian Deposit harus diisi'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  $validator->errors()
                    ]
                ]);
            }
        }

        $room = Room::with(['rent', 'rent.member', 'rent.member.user'])->where('slug', $request->noKamar)->first();

        // dd($room);
        $dataUpdateDeposit = [
            'pengembalian'  =>  $request->pengembalian,
            'tanggal'   =>  Carbon::now('Asia/Jakarta')->addDay(),
            'jenis_pengembalian'  =>  $request->jenis_pengembalian,
            'bank'  =>  $request->bank,
            'no_rek'    =>  $request->noRek,
            'is_checkout'   =>  true,
        ];

        $dataUpdateRent = [
            'is_checkout_normal'    =>  Carbon::now('Asia/Jakarta')->equalTo(Carbon::parse($room->rent->end_date)),
            'is_checkout_abnormal'    =>  Carbon::now('Asia/Jakarta')->notEqualTo(Carbon::parse($room->rent->end_date)),
        ];

        if (Deposite::where('room_id', $room->id)->where('user_id', $room->rent->member->user->id)->update($dataUpdateDeposit)) {
            if (TransactionRent::where('id', $room->rent->id)->update($dataUpdateRent)) {
                DB::commit();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  true,
                        'message'   =>  'Penghuni berhasil Checkout'
                    ]
                ]);
            }

            DB::rollback();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Penghuni gagal Checkout'
                ]
            ]);
        }

        DB::rollback();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Penghuni gagal Checkout'
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionRent $transactionRent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionRent $transactionRent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransactionRent $transactionRent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionRent $transactionRent)
    {
        //
    }

    function getAllData(Request $request)
    {
        $rooms = collect(Room::with(['rent', 'rent.member', 'rent.member.user'])->searchCategory($request->category)
            ->orderBy('number_room')
            ->get())->chunk(10);

        $results = array();

        if (collect($rooms)->count() > 0) {
            foreach ($rooms as $key => $chunk) {
                foreach ($chunk as $key => $value) {
                    $btnAction = '<div class="d-flex gap-2">';
                    if ($value->rent) {
                        if ($value->rent->is_approve) {
                            $btnAction .= '<a href="' . url("/transactions/rent-rooms/checkout") . '?room=' . $value->slug . '"
                                        class="badge badge-outline text-primary fw-semibold badge-pill">Checkout</a>
                                    <a href="' . url("/transactions/rent-rooms/change-room") . '?room=' . $value->slug . '"
                                        class="badge badge-outline text-primary fw-semibold badge-pill">Pindah
                                        Kamar</a>';
                        } else {
                            $btnAction .= '<a href="' . url('') . '/transactions/rent-rooms/detail-rents/' . $value->slug . '"
                                        class="badge badge-outline text-primary fw-semibold badge-pill">Detail
                                        Pembayaran</a>';
                        }
                    } else {
                        $btnAction .= '<a href="' . url('/transactions/rent-rooms/create') . '?room=' . $value->slug . '"
                                    class="badge badge-outline text-primary fw-semibold badge-pill">Sewa Kamar</a>';
                    }

                    $btnAction .= '</div>';

                    $status = '<span class="badge bg-blue-lt">Tersedia</span>';

                    if ($value->rent) {
                        if ($value->rent->is_approve) {
                            $status = '<span class="badge bg-green-lt">Sudah Disewa</span>';
                        } else {
                            $status = '<span class="badge bg-red-lt">Perlu Approval</span>';
                        }
                    }

                    $results[] = [
                        $value->number_room,
                        $value->home->name,
                        $value->category->name,
                        $value->rent ? '<button class="btn btn-link" title="Detail Penghuni" onclick="fnTransactionRoom.detailGuest(\'' . $value->rent->member->id . '\')">' . $value->rent->member->user->name . '</button>' : "-",
                        $value->rent ? Carbon::parse($value->rent->start_date)->isoFormat("DD MMMM YYYY") . ' - ' . Carbon::parse($value->rent->end_date)->isoFormat("DD MMMM YYYY") : "-",
                        $status,
                        $btnAction
                    ];
                }
            }
        }

        return response()->json([
            'data'  =>  $results
        ]);
    }

    function detailPayment(Room $room)
    {

        $dataRent = TransactionRent::with(['oldRoom', 'oldRoom.oldRent'])->where('room_id', $room->id)
            ->latest()
            ->first();

        return view('Pages.Transaction.detailRent', [
            'title' =>  "Detail Sewa Kamar",
            'pageTitle' =>  "Detail Sewa Kamar",
            'rent'  =>  $dataRent
        ]);
    }

    function saveDetailPayment(Request $request, Room $room)
    {
        DB::beginTransaction();

        $dataRent = TransactionRent::with(['member', 'oldRoom', 'oldRoom.oldRent'])->where('room_id', $room->id)
            ->latest()
            ->first();

        $deposit = Deposite::where('user_id', $dataRent->member->user->id)
            ->where('room_id', $dataRent->room_id)
            ->where('is_checkout', false)
            ->first();

        if (TransactionRent::find($dataRent->id)->update([
            'is_approve'   =>  true,
        ])) {
            if (!$deposit) {
                Deposite::create([
                    'room_id'   =>  $room->id,
                    'user_id'   =>  $dataRent->member->user->id,
                    'jumlah'    =>  $request->deposit,
                ]);
            }

            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "Transaksi berhasil disimpan",
                ]
            ]);
        }

        DB::rollback();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Transaksi gagal disimpan",
            ]
        ]);
    }

    function searchMember(Request $request)
    {
        $member = User::with(['member', 'member.userIdentity', 'foto'])->where('phone_number', makePhoneNumber($request->phoneNumber))
            ->first();

        return response()->json(
            $member
        );
    }
}
