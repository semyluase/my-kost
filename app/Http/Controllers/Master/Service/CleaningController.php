<?php

namespace App\Http\Controllers\Master\Service;

use App\Http\Controllers\Controller;
use App\Models\Master\Service\Cleaning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Number;

use function App\Helper\generateCounter;

class CleaningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Pages.Master.priceCleaning.index', [
            'title' =>  'Price Cleaning',
            'pageTitle' =>  'Price Cleaning',
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
            'harga' =>  'required'
        ], [
            'harga.required'    =>  'Harga wajib diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }
        $kode = generateCounter('service', 'S');

        $data = [
            'kode_item' =>  $kode,
            'price' =>  $request->harga,
            'user_created'  =>  auth()->user()->username,
        ];

        if (Cleaning::create($data)) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Harga pembersihan kamar berhasil disimpan'
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Harga pembersihan kamar gagal disimpan'
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cleaning $cleaning_price)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cleaning $cleaning_price)
    {
        return response()->json($cleaning_price);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cleaning $cleaning_price)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'harga' =>  'required'
        ], [
            'harga.required'    =>  'Harga wajib diisi'
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
            'price' =>  $request->harga,
            'user_updated'  =>  auth()->user()->username,
        ];

        if (Cleaning::find($cleaning_price->id)->update($data)) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Harga pembersihan kamar berhasil diubah'
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Harga pembersihan kamar gagal diubah'
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cleaning $cleaning_price)
    {
        if (Cleaning::find($cleaning_price->id)->update([
            'is_active' =>  false,
            'user_updated'  =>  auth()->user()->username,
        ])) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Harga pembersihan kamar berhasil dihapus'
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Harga pembersihan kamar gagal dihapus'
            ]
        ]);
    }

    function getAllData(Request $request)
    {
        $totalPriceCleanings = Cleaning::where('is_active', true)
            ->count();

        $filteredPriceCleanings = Cleaning::where('is_active', true)
            ->search(['search' => $request->search['value']])
            ->count();

        $priceCleanings = Cleaning::where('is_active', true)
            ->search(['search' => $request->search['value']])
            ->skip($request->start)
            ->limit($request->length)
            ->get();

        $results = array();
        $no = $request->start + 1;

        if ($priceCleanings) {
            foreach ($priceCleanings as $key => $value) {
                $btnAction = '<div class="d-flex gap-2">
                                    <button class="btn btn-warning" title="Ubah Data" onclick="fnPriceCleaning.onEdit(\'' . $value->kode_item . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    </button>
                                    <button class="btn btn-danger" title="Hapus Data" onclick="fnPriceCleaning.onDelete(\'' . $value->kode_item . '\',\'' . csrf_token() . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                    </button>
                                </div>';

                $results[] = [
                    $no,
                    Number::currency($value->price, in: 'IDR', locale: 'id'),
                    $btnAction
                ];

                $no++;
            }
        }

        return response()->json([
            'draw'  =>  $request->draw,
            'recordsTotal'  =>  $totalPriceCleanings,
            'recordsFiltered'   =>  $filteredPriceCleanings,
            'data'  =>  $results,
        ]);
    }
}
