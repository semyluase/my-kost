<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\TransactionRent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Pages.Setting.User.index', [
            'title' =>  'User',
            'pageTitle' =>  'User'
        ]);
    }

    public function profile()
    {
        return view('Pages.Setting.Profile.index', [
            'title' =>  'Profile',
            'pageTitle' =>  'Profile'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  =>  'required',
            'username'  =>  'required|unique:users,username',
            'home'  =>  'required',
            'role'  =>  'required',
            'password'  =>  'required'
        ], [
            'name.required' =>  'Nama User wajib diisi',
            'username.required' =>  'Username User wajib diisi',
            'password.required' =>  'Password User wajib diisi',
            'username.unique' =>  'User ini sudah terdaftar',
            'home.required' =>  'Lokasi User wajib dipilih',
            'role.required' =>  'Role User wajib dipilih',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        $data = [
            'name'  =>  Str::title($request->name),
            'username'  =>  $request->username,
            'home_id'   =>  $request->home,
            'role_id'   =>  $request->role,
            'password'  =>  bcrypt($request->password),
            'password_text'  =>  $request->password
        ];

        if (User::create($data)) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "User berhasil disimpan"
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "User gagal disimpan"
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name'  =>  'required',
            'home'  =>  'required',
            'role'  =>  'required',
        ], [
            'name.required' =>  'Nama User wajib diisi',
            'username.required' =>  'Username User wajib diisi',
            'username.unique' =>  'User ini sudah terdaftar',
            'home.required' =>  'Lokasi User wajib dipilih',
            'role.required' =>  'Role User wajib dipilih',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        $data = [
            'name'  =>  Str::title($request->name),
            'username'  =>  $request->username,
            'home_id'   =>  $request->home,
            'role_id'   =>  $request->role,
        ];

        if (User::find($user->id)->update($data)) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "User berhasil diubah"
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "User gagal diubah"
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->load(['member']);

        if ($user->member) {
            $rent = TransactionRent::where('member_id', $user->member->id)
                ->where('end_date', '>=', Carbon::now('Asia/Jakarta'))
                ->where('is_change_room', false)
                ->where('is_checkout_abnormal', false)
                ->where('is_checkout_normal', false)
                ->first();

            if ($rent) {
                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  "User ini gagal dihapus, kerena masih menyewa kamar!"
                    ]
                ]);
            }
        }

        if (User::find($user->id)->delete()) {
            if ($user->member) {
                Member::where('id', $user->member->id)->delete();
            }

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "User berhasil dihapus"
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "User gagal dihapus"
            ]
        ]);
    }

    function getAllData(Request $request)
    {
        $totalUsers = User::count();

        $filteredUsers = User::search(['search' => $request->search['value']])->count();

        $users = collect(User::search(['search' => $request->search['value']])
            ->skip($request->start)
            ->limit($request->length)
            ->get())->chunk(10);

        $results = array();
        $no = $request->start + 1;

        if ($users) {
            foreach ($users as $key => $chunk) {
                foreach ($chunk as $key => $value) {
                    $btnAction = '<div class="d-flex gap-2">
                                    <a href="javascript:;" class="btn-action text-warning" onclick="fnUser.onEdit(\'' . $value->id . '\')" title="Ubah User">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    </a>
                                    <a href="javascript:;" class="btn-action text-danger" onclick="fnUser.onDelete(\'' . $value->id . '\',\'' . csrf_token() . '\')" title="Hapus User">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-eraser"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 20h-10.5l-4.21 -4.3a1 1 0 0 1 0 -1.41l10 -10a1 1 0 0 1 1.41 0l5 5a1 1 0 0 1 0 1.41l-9.2 9.3" /><path d="M18 13.3l-6.3 -6.3" /></svg>
                                    </a>
                                </div>';
                    $results[] = [
                        $no,
                        $value->name,
                        $value->username,
                        $value->role ? $value->role->name : "",
                        $value->location ? $value->location->city : "",
                        $btnAction
                    ];

                    $no++;
                }
            }
        }

        return response()->json([
            'draw'  =>  $request->draw,
            'recordsTotal'  =>  $totalUsers,
            'recordsFiltered'   =>  $filteredUsers,
            'data'  =>  $results
        ]);
    }
}
