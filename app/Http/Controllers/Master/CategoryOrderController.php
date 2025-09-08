<?php

namespace App\Http\Controllers\Master;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CategoryOrder;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Pages.Master.CategoryOrder.index', [
            'title' =>  'Kategori Item',
            'pageTitle' =>  'Kategori Item',
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
            'code'  =>  'required',
            'name'  =>  'required'
        ], [
            'code.required' =>  'Kode Item wajib diisi',
            'name.required' =>  'Nama wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors(),
                ]
            ]);
        }

        $slug = Str::slug($request->name);

        $validator = Validator::make(
            ['slug' => $slug],
            ['slug' =>  'unique:category_orders,slug'],
            ['slug.unique'  =>  'Data ini sudah terdaftar']
        );

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors(),
                ]
            ]);
        }

        $data = [
            'name'  =>  Str::upper($request->name),
            'short_name'  =>  Str::upper($request->code),
            'slug'  =>  $slug,
            'user_created'  =>  Auth::user()->username,
        ];

        DB::beginTransaction();

        if (CategoryOrder::create($data)) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "Data berhasil disimpan",
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Data gagal disimpan",
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoryOrder $category_order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoryOrder $category_order)
    {
        return response()->json($category_order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CategoryOrder $category_order)
    {
        $validator = Validator::make($request->all(), [
            'code'  =>  'required',
            'name'  =>  'required'
        ], [
            'code.required' =>  'Kode Item wajib diisi',
            'name.required' =>  'Nama wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors(),
                ]
            ]);
        }

        $slug = Str::slug($request->name);

        if ($slug != $category_order->slug) {
            $validator = Validator::make(
                ['slug' => $slug],
                ['slug' =>  'unique:category_orders,slug'],
                ['slug.unique'  =>  'Data ini sudah terdaftar']
            );

            if ($validator->fails()) {
                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  $validator->errors(),
                    ]
                ]);
            }
        }

        $data = [
            'name'  =>  Str::upper($request->name),
            'short_name'  =>  Str::upper($request->code),
            'slug'  =>  $slug,
            'user_updated'  =>  Auth::user()->username,
        ];

        DB::beginTransaction();

        if (CategoryOrder::find($category_order->id)->update($data)) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  "Data berhasil diubah",
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  "Data gagal diubah",
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoryOrder $category_order)
    {
        DB::beginTransaction();

        if (CategoryOrder::find($category_order->id)->update([
            'is_active' =>  false,
            'user_updated'  =>  Auth::user()->username
        ])) {
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

    function getAllData(Request $request)
    {
        $categoryOrders = CategoryOrder::where('is_active', true)
            ->get();

        $no = 1;
        $results = array();

        if ($categoryOrders) {
            foreach ($categoryOrders as $key => $value) {
                $btnAction = '<div class="d-flex gap-2">
                                        <button class="btn btn-warning" title="Ubah Data" onclick="fnCategoryItem.onEdit(\'' . $value->slug . '\')">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                        </button>
                                        <button class="btn btn-danger" title="Hapus Data" onclick="fnCategoryItem.onDelete(\'' . $value->slug . '\',\'' . csrf_token() . '\')">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                        </button>
                                    </div>';
                $results[] = [
                    $no,
                    $value->short_name,
                    $value->name,
                    $btnAction,
                ];

                $no++;
            }
        }

        return response()->json([
            'data'  =>  $results
        ]);
    }
}
