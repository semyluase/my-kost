<?php

namespace App\Http\Controllers;

use App\Models\FoodSnack;
use App\Helper\Helper;
use App\Models\FoodSnackPicture;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class FoodSnackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Pages.Master.FoodSnack.index', [
            'title' =>  'Makanan & Minuman',
            'pageTitle' =>  'Makanan & Minuman',
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
            'hargaFoodSnack' =>  'required',
            'categoryFoodSnack'  =>  'required'
        ], [
            'name.required' =>  "Nama barang wajib diisi",
            'price.required' =>  "Harga barang wajib diisi",
            'category.required' =>  "Kategori barang wajib dipilih",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        $counter = Helper::generateCounter('food-snack', $request->category);

        $data = [
            'name'  =>  Str::title($request->name),
            'code_item' =>  $counter,
            'category'  =>  $request->category,
            'price' =>  $request->price,
        ];

        if (FoodSnack::create($data)) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Data Makanan & Minuman berhasil disimpan'
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Data Makanan & Minuman gagal disimpan'
            ]
        ]);
    }

    function uploadPicture(Request $request)
    {
        $foodSnack = FoodSnack::where('code_item', $request->codeUploadFoodSnack)->first();

        if (!$request->file('files')) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Gambar Barang harus diupload'
                ]
            ]);
        }

        if ($foodSnack->picture) {
            FoodSnackPicture::where('food_snack_id', $foodSnack->id)->delete();

            File::delete(public_path('upload/foodSnack/' . $foodSnack->picture->file_name));
        }

        $files = $request->file('files');
        $dataUpload = $files->store('upload/foodSnack');

        $data = [
            'food_snack_id'   =>  $foodSnack->id,
            'blob'  =>  base64_encode(file_get_contents(public_path('assets/' . $dataUpload))),
            'file_location' =>  base_path($dataUpload),
            'file_name' =>  basename($dataUpload),
        ];

        if (FoodSnackPicture::create($data)) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Gambar Makanan & Minuman,berhasil diunggah'
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Gambar Makanan & Minuman,gagal diunggah'
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(FoodSnack $foodSnack)
    {
        //
    }

    public function getDataUpload(FoodSnack $foodSnack)
    {
        return response()->json($foodSnack);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FoodSnack $foodSnack)
    {
        return response()->json($foodSnack);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FoodSnack $foodSnack)
    {
        $validator = Validator::make($request->all(), [
            'name'  =>  'required',
            'price' =>  'required',
            'category'  =>  'required'
        ], [
            'name.required' =>  "Nama barang wajib diisi",
            'price.required' =>  "Harga barang wajib diisi",
            'category.required' =>  "Kategori barang wajib dipilih",
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
            'code_item' =>  $request->codeFoodSnack,
            'category'  =>  $request->category,
            'price' =>  $request->price,
        ];

        if (FoodSnack::find($foodSnack->id)->update($data)) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Data Makanan & Minuman berhasil diubah'
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Data Makanan & Minuman gagal diubah'
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FoodSnack $foodSnack)
    {
        if (FoodSnack::find($foodSnack->id)->delete()) {
            if ($foodSnack->picture) {
                $dataPicture = FoodSnackPicture::where('food_snack_id', $foodSnack->id)->first();

                if ($dataPicture) {
                    FoodSnackPicture::where('id', $dataPicture->id)->delete();

                    File::delete(public_path('upload/foodSnack/' . $dataPicture->file_name));
                }
            }

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Data Makanan & Minuman berhasil dihapus'
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Data Makanan & Minuman gagal dihapus'
            ]
        ]);
    }

    public function destroyPicture(FoodSnack $foodSnack)
    {
        $dataPicture = FoodSnackPicture::where('food_snack_id', $foodSnack->id)->first();

        if ($dataPicture) {
            File::delete($dataPicture->file_location);
        }

        if (FoodSnackPicture::where('food_snack_id', $foodSnack->id)->delete()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Gambar Makanan & Minuman berhasil dihapus'
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  true,
                'message'   =>  'Gambar Makanan & Minuman gagal dihapus'
            ]
        ]);
    }

    function getAllData(Request $request)
    {
        $totalFoodSnacks = FoodSnack::count();

        $filteredFoodSnacks = FoodSnack::search(['search' => $request->search['value']])
            ->count();

        $foodSnacks = collect(FoodSnack::search(['search' => $request->search['value']])
            ->skip($request->start)
            ->limit($request->length)
            ->get())->chunk(10);

        $results = array();
        $no = $request->start + 1;

        if ($foodSnacks) {
            foreach ($foodSnacks as $key => $chunkFoodSnack) {
                foreach ($chunkFoodSnack as $key => $value) {
                    $btnUpload = '<a href="javascript:;" class="btn-action text-primary" title="Unggah Gambar Makanan & Minuman" onclick="fnFoodSnack.uploadPicture(\'' . $value->code_item . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-upload"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
                                </a>';

                    if ($value->picture) {
                        $btnUpload = '<a href="javascript:;" class="btn-action text-danger" title="Hapus Gambar Makanan & Minuman" onclick="fnFoodSnack.deletePicture(\'' . $value->code_item . '\',\'' . csrf_token() . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-album-off"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 4h10a2 2 0 0 1 2 2v10m-.581 3.41c-.362 .364 -.864 .59 -1.419 .59h-12a2 2 0 0 1 -2 -2v-12c0 -.552 .224 -1.052 .585 -1.413" /><path d="M12 4v4m1.503 1.497l.497 -.497l2 2v-7" /><path d="M3 3l18 18" /></svg>
                                    </a>';
                    }

                    $btnAction = '<div class="d-flex gap-2">
                                    ' . $btnUpload . '
                                    <a href="javascript:;" class="btn-action text-warning" title="Ubah Makanan & Minuman" onclick="fnFoodSnack.onEdit(\'' . $value->code_item . '\')">
                                        <svg  vg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    </a>
                                    <a href="javascript:;" class="btn-action text-danger" title="Hapus Makanan & Minuman" onclick="fnFoodSnack.onDelete(\'' . $value->code_item . '\',\'' . csrf_token() . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-eraser"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 20h-10.5l-4.21 -4.3a1 1 0 0 1 0 -1.41l10 -10a1 1 0 0 1 1.41 0l5 5a1 1 0 0 1 0 1.41l-9.2 9.3" /><path d="M18 13.3l-6.3 -6.3" /></svg>
                                    </a>
                                </div>';

                    $image = $value->picture ? asset('assets/upload/foodSnack/' . $value->picture->file_name) : asset('assets/image/nocontent.jpg');
                    $category = 'Makanan';

                    switch ($value->category) {
                        case 'D':
                            $category = "Minuman";
                            break;

                        case 'S':
                            $category = "Makanan Ringan";
                            break;

                        default:
                            break;
                    }

                    $results[] = [
                        $no,
                        '<img src="' . $image . '" class="img-responsive pt-0" style="width:10rem; height 10rem;">',
                        $value->name,
                        $category,
                        Number::currency($value->price, in: 'IDR', locale: 'id'),
                        $btnAction,
                    ];

                    $no++;
                }
            }
        }

        return response()->json([
            'draw'  =>  $request->draw,
            'recordsTotal'  =>  $totalFoodSnacks,
            'recordsFiltered'   =>  $filteredFoodSnacks,
            'data'  =>  $results
        ]);
    }
}
