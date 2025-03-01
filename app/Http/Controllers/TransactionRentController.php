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

        $nobukti = Carbon::now('Asia/Jakarta')->isoFormat("YYYYMMDDHHmmss");

        $user = User::where('phone_number', makePhoneNumber($request->noHP))
            ->first();

        $member = Member::where('phone_number', makePhoneNumber($request->noHP))
            ->first();

        $userIdentify = UserIdentity::where('token', $request->tokenFoto)->first();

        $room = Room::with(['category.prices'])->where('slug', $request->room)
            ->where('home_id', auth()->user()->home_id)
            ->first();

        $rent = null;
        if ($member) {
            $rent = TransactionRent::where('room_id', $room->id)
                ->where('member_id', $member->id)
                ->where('start_date', Carbon::parse($request->startRentDate)->isoFormat("YYYY-MM-DD"))
                ->first();
        }

        $dataUser = array();
        $dataMember = array();
        $dataRent = array();
        $dataDeposit = array();
        if (!$user) {
            $dataUser = [
                'role_id'   =>  3,
                'home_id'   =>  auth()->user()->home_id,
                'phone_number'  =>  makePhoneNumber($request->noHP),
                'name'  =>  Str::title($request->name),
                'password'  =>  bcrypt(Carbon::parse($request->tanggalLahir)->isoFormat("DDMMYYYY")),
            ];
        }

        if (collect($dataUser)->count() > 0) {
            $user = User::create($dataUser);
        } else {
            if ($user->home_id == null) {
                User::find($user->id)->update([
                    'home_id'   =>  auth()->user()->home_id,
                ]);
            }
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
                    $endDateRent = Carbon::parse($request->startRentDate)->addWeek(1)->isoFormat("YYYY-MM-DD");
                    $durasi = 'weekly';
                    break;

                case 'bulanan':
                    $price = $room->category->prices[2]->price;
                    $endDateRent = Carbon::parse($request->startRentDate)->addMonth(1)->isoFormat("YYYY-MM-DD");
                    $durasi = 'monthly';
                    break;

                case 'tahunan':
                    $price = $room->category->prices[3]->price;
                    $endDateRent = Carbon::parse($request->startRentDate)->addYear(1)->isoFormat("YYYY-MM-DD");
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
                                'url'   =>  'transactions/rent-rooms/detail-rents/' . $dataKamarBaru->number_room,
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

    function saveDetailPayment(Room $room)
    {
        DB::beginTransaction();

        $dataRent = TransactionRent::with(['member', 'oldRoom', 'oldRoom.oldRent'])->where('room_id', $room->id)
            ->latest()
            ->first();

        $deposit = Deposite::where('room_id', $dataRent->id)
            ->where('user_id', $dataRent->member->user->id)
            ->first();

        if ($dataRent->oldRoom) {
            if ($dataRent->oldRoom->oldRent->is_upgrade) {
                $deposit = null;
            }
        }

        if (TransactionRent::find($dataRent->id)->update([
            'is_approve'   =>  true,
        ])) {
            if (!$deposit) {
                Deposite::create([
                    'room_id'   =>  $room->id,
                    'user_id'   =>  $dataRent->member->user->id,
                    'rent_id'   =>  $dataRent->id,
                    'jumlah'    =>  $dataRent->price,
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
        $member = User::with(['member', 'member.userIdentity'])->where('phone_number', makePhoneNumber($request->phoneNumber))
            ->first();

        return response()->json(
            $member
        );
    }
}
