<?php

namespace App\Http\Controllers;

use App\Models\Hotspot;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HotspotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Pages.Master.Hotspot.index', [
            'title' =>  'Hotspot',
            'pageTitle' =>  'Hotspot',
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
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'ssid'  =>  'required',
            'password'  =>  'required',
            'rooms'  => 'required',
        ], [
            'ssid.required' =>  'SSID harus diisi',
            'password.required' =>  'Password harus diisi',
            'rooms.required' =>  'Nomor kamar harus dipilih',
        ]);

        if ($validator->fails()) {
            DB::rollBack();
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        $data = array();

        foreach ($request->rooms as $key => $value) {
            $data[] = [
                'home_id' => Auth::user()->home_id,
                'ssid'  =>  $request->ssid,
                'password'  =>  $request->password,
                'room_number'   =>  $value,
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
            ];
        }

        if (Hotspot::insert($data)) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "Data berhasil disimpan"
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Data gagal disimpan"
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Hotspot $hotspot)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hotspot $hotspot)
    {
        return response()->json($hotspot);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hotspot $hotspot)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'ssid'  =>  'required',
            'password'  =>  'required',
            'room'  => 'required',
        ], [
            'ssid.required' =>  'SSID harus diisi',
            'password.required' =>  'Password harus diisi',
            'room.required' =>  'Nomor kamar harus dipilih',
        ]);

        if ($validator->fails()) {
            DB::rollBack();
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        $data = [
            'home_id' => Auth::user()->home_id,
            'ssid'  =>  $request->ssid,
            'password'  =>  $request->password,
            'room_number'   =>  $request->room,
            'updated_at'    =>  Carbon::now('Asia/Jakarta'),
        ];

        if (Hotspot::find($hotspot->id)->update($data)) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "Data berhasil diubah"
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Data gagal diubah"
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotspot $hotspot)
    {
        DB::beginTransaction();
        if (Hotspot::find($hotspot->id)->delete()) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "Data berhasil dihapus"
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Data gagal dihapus"
            ]
        ]);
    }

    function getAllData(Request $request)
    {
        $hotspots = collect(Hotspot::where('is_active', true)
            ->get())->chunk(100);

        $results = array();
        $no = 1;

        if ($hotspots) {
            foreach ($hotspots as $key => $chunk) {
                foreach ($chunk as $c => $value) {
                    $btnAction = '<div class="d-flex gap-2">
                                    <button class="btn btn-warning" title="Edit Data" onclick="fnHotspot.onEdit(\'' . $value->id . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    </button>
                                    <button class="btn btn-danger" title="Delete Data" onclick="fnHotspot.onDelete(\'' . $value->id . '\',\'' . csrf_token() . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                    </button>
                                </div>';

                    $results[] = [
                        $no,
                        $value->ssid,
                        $value->password,
                        $value->room_number,
                        $btnAction,
                    ];

                    $no++;
                }
            }
        }

        return response()->json([
            'data'  =>  $results
        ]);
    }
}
