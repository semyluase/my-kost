<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryFacility;
use App\Models\CategoryPicture;
use App\Models\CategoryPrice;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Pages.Master.Category.index', [
            'title' =>  'Kategori',
            'pageTitle' =>  'Kategori',
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
            'name'  =>  'required',
            'categoryFacilities'    =>  'required'
        ], [
            'name.required' =>  'Nama Kategori harus diisi',
            'categoryFacilities.required'   =>  "Fasilitas Kategori harus dipilih"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        $slug = SlugService::createSlug(Category::class, 'slug', $request->name, ['unique' => false]);

        $validator = Validator::make(['slug'    =>  $slug], [
            'slug'  =>  'unique:categories,slug'
        ], [
            'slug.unique' =>  'Kategori ini sudah terdaftar',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        $facilities = array();
        $prices = array();

        $data = [
            'name'  =>  Str::upper($request->name),
            'slug'  =>  $slug,
        ];

        $category = Category::create($data);

        if ($category) {
            if (collect($request->categoryFacilities)->count() > 0) {
                foreach ($request->categoryFacilities as $key => $value) {
                    $facilities[] = [
                        'category_id'   =>  $category->id,
                        'facility_id'   =>  $value,
                        'created_at'    =>  Carbon::now('Asia/Jakarta'),
                        'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                    ];
                }
            }

            if ($request->dailyPrice > 0) {
                $prices[] = [
                    'category_id'   =>  $category->id,
                    'type'  =>  'daily',
                    'price' =>  $request->dailyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if ($request->weeklyPrice > 0) {
                $prices[] = [
                    'category_id'   =>  $category->id,
                    'type'  =>  'weekly',
                    'price' =>  $request->weeklyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if ($request->monthlyPrice > 0) {
                $prices[] = [
                    'category_id'   =>  $category->id,
                    'type'  =>  'monthly',
                    'price' =>  $request->monthlyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if ($request->yearlyPrice > 0) {
                $prices[] = [
                    'category_id'   =>  $category->id,
                    'type'  =>  'yearly',
                    'price' =>  $request->yearlyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if (collect($facilities)->count() > 0) {
                if (CategoryFacility::insert($facilities)) {
                    if (collect($prices)->count() > 0) {
                        if (CategoryPrice::insert($prices)) {
                            DB::commit();

                            return response()->json([
                                'data'  =>  [
                                    'status'    =>  true,
                                    'message'   =>  'Kategori berhasil disimpan'
                                ]
                            ]);
                        }

                        DB::rollBack();

                        return response()->json([
                            'data'  =>  [
                                'status'    =>  false,
                                'message'   =>  'Kategori gagal disimpan'
                            ]
                        ]);
                    }
                }

                DB::rollBack();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  'Kategori gagal disimpan'
                    ]
                ]);
            }

            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Kategori gagal disimpan'
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Kategori gagal disimpan'
            ]
        ]);
    }

    function uploadPicture(Request $request)
    {
        $category = Category::where('slug', $request->slugUploadCategory)->first();

        $files = $request->file('files');
        $data = array();

        foreach ($files as $key => $value) {
            $dataUpload = $value->store('upload/category');

            $data[] = [
                'category_id'   =>  $category->id,
                'blob'  =>  base64_encode(file_get_contents(public_path('assets/' . $dataUpload))),
                'file_location' =>  base_path($dataUpload),
                'file_name' =>  basename($dataUpload),
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ];
        }

        if (CategoryPicture::insert($data)) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Gambar kategori kamar,berhasil diunggah'
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Gambar kategori kamar,gagal diunggah'
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    public function showPicture(Category $category)
    {
        $picture = CategoryPicture::where('category_id', $category->id)->get();

        $data = '<div id="carousel-indicators-thumb" class="carousel slide carousel-fade" data-bs-ride="carousel">
                    <div class="carousel-indicators carousel-indicators-thumb">';
        if ($picture) {
            foreach ($picture as $key => $value) {
                $data .= '<a href="javascript:;" type="button" data-bs-target="#carousel-indicators-thumb" data-bs-slide-to="' . $key . '" class="ratio ratio-4x3 active" style="background-image: url(' . asset('assets/upload/category/' . $value->file_name) . ')"></a>';
            }
        }
        $data .= '</div>
                    <div class="carousel-inner">';
        if ($picture) {
            foreach ($picture as $key => $value) {
                $data .= '<div class="carousel-item ' . ($key == 0 ? 'active' : '') . '">
                                            <img class="d-block w-100" alt="" src="' . asset('assets/upload/category/' . $value->file_name) . '" />
                                        </div>';
            }
        }
        $data .= '</div>';
        $data .= '</div>';

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return response()->json($category->load('facilities', 'prices'));
    }

    public function getDataUpload(Category $category)
    {
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        DB::beginTransaction();

        $category->load(['facilities', 'prices']);
        $validator = Validator::make($request->all(), [
            'name'  =>  'required',
        ], [
            'name.required' =>  'Nama Kategori harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        $slug = SlugService::createSlug(Category::class, 'slug', $request->name, ['unique' => false]);

        if ($slug != $category->slug) {
            $validator = Validator::make(['slug'    =>  $slug], [
                'slug'  =>  'unique:categories,slug'
            ], [
                'slug.unique' =>  'Kategori ini sudah terdaftar',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        $facilities = array();
        $prices = array();

        $data = [
            'name'  =>  Str::upper($request->name),
            'slug'  =>  $slug,
        ];

        if (Category::find($category->id)->update($data)) {
            if (collect($category->facilities)->count() > 0) {
                CategoryFacility::where('category_id', $category->id)->delete();
            }

            if (collect($category->prices)->count() > 0) {
                CategoryPrice::where('category_id', $category->id)->delete();
            }

            if (collect($request->categoryFacilities)->count() > 0) {
                foreach ($request->categoryFacilities as $key => $value) {
                    $facilities[] = [
                        'category_id'   =>  $category->id,
                        'facility_id'   =>  $value,
                        'created_at'    =>  Carbon::now('Asia/Jakarta'),
                        'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                    ];
                }
            }

            if ($request->dailyPrice > 0) {
                $prices[] = [
                    'category_id'   =>  $category->id,
                    'type'  =>  'daily',
                    'price' =>  $request->dailyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if ($request->weeklyPrice > 0) {
                $prices[] = [
                    'category_id'   =>  $category->id,
                    'type'  =>  'weekly',
                    'price' =>  $request->weeklyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if ($request->monthlyPrice > 0) {
                $prices[] = [
                    'category_id'   =>  $category->id,
                    'type'  =>  'monthly',
                    'price' =>  $request->monthlyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if ($request->yearlyPrice > 0) {
                $prices[] = [
                    'category_id'   =>  $category->id,
                    'type'  =>  'yearly',
                    'price' =>  $request->yearlyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if (collect($facilities)->count() > 0) {
                if (CategoryFacility::insert($facilities)) {
                    if (collect($prices)->count() > 0) {
                        if (CategoryPrice::insert($prices)) {
                            DB::commit();

                            return response()->json([
                                'data'  =>  [
                                    'status'    =>  true,
                                    'message'   =>  'Kategori berhasil diubah'
                                ]
                            ]);
                        }

                        DB::rollBack();

                        return response()->json([
                            'data'  =>  [
                                'status'    =>  false,
                                'message'   =>  'Kategori gagal diubah'
                            ]
                        ]);
                    }
                }

                DB::rollBack();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  'Kategori gagal diubah'
                    ]
                ]);
            }

            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Kategori gagal diubah'
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Kategori gagal diubah'
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function activatedData(Category $category)
    {
        $status = $category->is_active ? false : true;

        if (Category::find($category->id)->update([
            'is_active' =>  $status
        ])) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  $category->is_active ? 'Kategori berhasil dinonaktifkan '  : 'Kategori berhasil diaktifkan'
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  $category->is_active ? 'Kategori gagal dinonaktifkan ' : 'Kategori gagak diaktifkan'
            ]
        ]);
    }

    public function destroy(Category $category)
    {
        DB::beginTransaction();

        $category->load(['facilities', 'pictures', 'prices']);

        if (Category::find($category->id)->delete()) {
            if (collect($category->facilities)->count() > 0) {
                if (!CategoryFacility::where('category_id', $category->id)->delete()) {
                    DB::rollBack();

                    return response()->json([
                        'data'  =>  [
                            'status'    =>  false,
                            'message'   =>  'Kategori gagal dihapus, Fasilitas gagal dihapus'
                        ]
                    ]);
                }
            }

            if (collect($category->prices)->count() > 0) {
                if (!CategoryPrice::where('category_id', $category->id)->delete()) {
                    DB::rollBack();

                    return response()->json([
                        'data'  =>  [
                            'status'    =>  false,
                            'message'   =>  'Kategori gagal dihapus, Harga gagal dihapus'
                        ]
                    ]);
                }
            }

            if (collect($category->pictures)->count() > 0) {
                if (!CategoryPicture::where('category_id', $category->id)->delete()) {
                    DB::rollBack();

                    return response()->json([
                        'data'  =>  [
                            'status'    =>  false,
                            'message'   =>  'Kategori gagal dihapus, Gambar gagal dihapus'
                        ]
                    ]);
                }

                foreach ($category->pictures as $key => $picture) {
                    if (File::exists($picture->file_location)) {
                        File::delete($picture->file_location);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Kategori berhasi dihapus'
                ]
            ]);
        }

        DB::rollback();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Kategori gagal dihapus'
            ]
        ]);
    }

    public function destroyPicture(Category $category)
    {
        DB::beginTransaction();

        $dataPicture = CategoryPicture::where('category_id', $category->id)->get();

        if (CategoryPicture::where('category_id', $category->id)->delete()) {
            DB::commit();

            if ($dataPicture) {
                foreach ($dataPicture as $key => $value) {
                    File::delete($value->file_location);
                }
            }

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Gambar Kategori berhasil dihapus'
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  true,
                'message'   =>  'Gambar Kamar gagal dihapus'
            ]
        ]);
    }

    function getAllData(Request $request)
    {
        $totalCategory = Category::count();

        $filteredCategory = Category::search(['search' => $request->search['value']])
            ->count();

        $categories = collect(Category::with(['facilities', 'prices', 'pictures'])->search(['search' => $request->search['value']])
            ->skip($request->start)
            ->limit($request->length)
            ->get())->chunk(10);

        $results = array();
        $no = $request->start + 1;

        if ($categories) {
            foreach ($categories as $key => $chunk) {
                foreach ($chunk as $key => $value) {
                    $facilities = 'Tidak ada fasilitas yang dipilih';
                    $prices = 'Belum ada harga untuk kategori ini';

                    $activated = $value->is_active ?
                        '<a href="javascript:;" class="btn-action btn-info" title="Nonaktifkan Kategori" onclick="fnCategory.onActivated(\'' . $value->slug . '\',\'' . csrf_token() . '\')">
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-bookmark-off"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7.708 3.721a3.982 3.982 0 0 1 2.292 -.721h4a4 4 0 0 1 4 4v7m0 4v3l-6 -4l-6 4v-14c0 -.308 .035 -.609 .1 -.897" /><path d="M3 3l18 18" /></svg>
                    </a>' :
                        '<a href="javascript:;" class="btn-action text-success" title="Aktifkan Kategori" onclick="fnCategory.onActivated(\'' . $value->slug . '\',\'' . csrf_token() . '\')">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-bookmark"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 7v14l-6 -4l-6 4v-14a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4z" /></svg>
                </a>';

                    $btnUpload = '<a href="javascript:;" class="btn-action text-primary" title="Unggah Foto" onclick="fnCategory.uploadPicture(\'' . $value->slug . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-upload"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
                                </a>';

                    if (collect($value->pictures)->count() > 0) {
                        $btnUpload = '<a href="javascript:;" class="btn-action text-primary" title="Unggah Foto" onclick="fnCategory.uploadPicture(\'' . $value->slug . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-upload"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
                                </a>
                                <a href="javascript:;" class="btn-action text-danger" title="Hapus Foto" onclick="fnCategory.deletePicture(\'' . $value->slug . '\',\'' . csrf_token() . '\')">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-photo-minus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M12.5 21h-6.5a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v9" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l4 4" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l2 2" /><path d="M16 19h6" /></svg>
                                </a>
                                <a href="javascript:;" class="btn-action text-primary" title="Lihat Foto" onclick="fnCategory.viewPicture(\'' . $value->slug . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-album"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M12 4v7l2 -2l2 2v-7" /></svg>
                                </a>';
                    }

                    $btnAction = '<button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-layout-dashboard"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" /><path d="M5 16h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" /><path d="M15 12h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" /><path d="M15 4h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" /></svg>
                                    Action
                                </button>
                                <div class="dropdown-menu" style="">
                                ' . $activated . '
                                  <a class="dropdown-item" href="#">
                                    Another action
                                  </a>
                                </div>';




                    $btnAction = '<div class="d-flex gap-2">
                                    ' . $activated . '
                                    ' . $btnUpload . '
                                    <a href="javascript:;" class="btn-action text-warning" title="Ubah Kategori" onclick="fnCategory.onEdit(\'' . $value->slug . '\')">
                                        <svg  vg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    </a>
                                    <a href="javascript:;" class="btn-action text-danger" title="Hapus Kategori" onclick="fnCategory.onDelete(\'' . $value->slug . '\',\'' . csrf_token() . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-eraser"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 20h-10.5l-4.21 -4.3a1 1 0 0 1 0 -1.41l10 -10a1 1 0 0 1 1.41 0l5 5a1 1 0 0 1 0 1.41l-9.2 9.3" /><path d="M18 13.3l-6.3 -6.3" /></svg>
                                    </a>
                                </div>';

                    if ($value->prices) {
                        $prices = '<div class="row g-1">';

                        $categoryPrices = collect($value->prices)->chunk(10);

                        if ($categoryPrices) {
                            foreach ($categoryPrices as $key => $chunkPrice) {
                                foreach ($chunkPrice as $key => $valuePrice) {
                                    $type = 'Harian';

                                    switch ($valuePrice->type) {
                                        case 'weekly':
                                            $type = 'Mingguan';
                                            break;

                                        case 'monthly':
                                            $type = 'Bulanan';
                                            break;

                                        case 'yearly':
                                            $type = 'Tahunan';
                                            break;

                                        default:
                                            # code...
                                            break;
                                    }
                                    $prices .= '<div class="col-12">
                                                <div class="row g-1 align-items-center">
                                                  <div class="col">
                                                    <div class="text-reset d-block">' . $type . '</div>
                                                    <div class="text-secondary mt-n1">' . $valuePrice->price . '</div>
                                                  </div>
                                                </div>
                                              </div>';
                                }
                            }
                        }

                        $prices .= '</div>';
                    }

                    if ($value->facilities) {
                        $facilities = '<div class="row g-1">';

                        $categoryFacilities = collect($value->facilities)->chunk(10);

                        if ($categoryFacilities) {
                            foreach ($categoryFacilities as $key => $chunkFacility) {
                                foreach ($chunkFacility as $key => $valueFacility) {
                                    $facilities .= '<div class="col-6">
                                    <div class="row g-1 align-items-center">
                                      <div class="col">
                                        <div class="text-reset d-block">' . $valueFacility->facility->name . '</div>
                                      </div>
                                    </div>
                                  </div>';
                                }
                            }
                        }

                        $facilities .= '</div>';
                    }

                    $results[] = [
                        $no,
                        $value->name,
                        $facilities,
                        $prices,
                        $value->is_active ? '<span class="badge bg-green text-green-fg">Aktif</span>' : '<span class="badge bg-red text-red-fg">TIdak Aktif</span>',
                        $btnAction
                    ];

                    $no++;
                }
            }
        }

        return response()->json([
            'draw'  =>  $request->draw,
            'data'  =>  $results,
            'recordsTotal'  =>  $totalCategory,
            'recordsFiltered'  =>  $filteredCategory,
        ]);
    }
}
