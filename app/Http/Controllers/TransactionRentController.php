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
                    break;

                case 'bulanan':
                    $price = $room->category->prices[2]->price;
                    $endDateRent = Carbon::parse($request->startRentDate)->addMonth(1)->isoFormat("YYYY-MM-DD");
                    break;

                case 'tahunan':
                    $price = $room->category->prices[3]->price;
                    $endDateRent = Carbon::parse($request->startRentDate)->addYear(1)->isoFormat("YYYY-MM-DD");
                    break;

                default:
                    $price = $room->category->prices[0]->price;
                    $endDateRent = Carbon::parse($request->startRentDate)->addDay(1)->isoFormat("YYYY-MM-DD");
                    break;
            }

            $dataRent = [
                'room_id'   =>  $room->id,
                'member_id' =>  $member->id,
                'start_date'    =>  Carbon::parse($request->startRentDate)->isoFormat("YYYY-MM-DD"),
                'end_date'  =>  $endDateRent,
                'price' =>  $price,
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

        $dataRent = TransactionRent::where('room_id', $room->id)
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

        $dataRent = TransactionRent::with(['member'])->where('room_id', $room->id)
            ->latest()
            ->first();

        $deposit = Deposite::where('room_id', $dataRent->id)
            ->where('user_id', $dataRent->member->user->id)
            ->first();

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
        $member = User::with(['member'])->where('phone_number', makePhoneNumber($request->phoneNumber))
            ->first();


        return response()->json($member);
    }
}
