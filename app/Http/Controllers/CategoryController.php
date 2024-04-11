<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
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

        $data = [
            'name'  =>  Str::upper($request->name),
            'slug'  =>  $slug,
        ];

        if (Category::create($data)) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Kategori berhasil disimpan'
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Kategori gagal disimpan'
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
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

        $data = [
            'name'  =>  Str::upper($request->name),
            'slug'  =>  $slug,
        ];

        if (Category::find($category->id)->update($data)) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Kategori berhasil diubah'
                ]
            ]);
        }

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
        if (Category::find($category->id)->delete()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Kategori berhasi dihapus'
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Kategori gagal dihapus'
            ]
        ]);
    }

    function getAllData(Request $request)
    {
        $totalCategory = Category::count();

        $filteredCategory = Category::search(['search' => $request->search['value']])
            ->count();

        $categories = collect(Category::search(['search' => $request->search['value']])
            ->skip($request->start)
            ->limit($request->length)
            ->get())->chunk(10);

        $results = array();
        $no = $request->start + 1;

        if ($categories) {
            foreach ($categories as $key => $chunk) {
                foreach ($chunk as $key => $value) {
                    $activated = $value->is_active ?
                        '<a href="javascript:;" class="btn-action btn-info" title="Nonaktifkan Kategori" onclick="fnCategory.onActivated(\'' . $value->slug . '\',\'' . csrf_token() . '\')">
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-bookmark-off"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7.708 3.721a3.982 3.982 0 0 1 2.292 -.721h4a4 4 0 0 1 4 4v7m0 4v3l-6 -4l-6 4v-14c0 -.308 .035 -.609 .1 -.897" /><path d="M3 3l18 18" /></svg>
                    </a>' :
                        '<a href="javascript:;" class="btn-action text-success" title="Aktifkan Kategori" onclick="fnCategory.onActivated(\'' . $value->slug . '\',\'' . csrf_token() . '\')">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-bookmark"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 7v14l-6 -4l-6 4v-14a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4z" /></svg>
                </a>';
                    $btnAction = '<div class="d-flex gap-2">
                                    ' . $activated . '
                                    <a href="javascript:;" class="btn-action text-warning" title="Ubah Kategori" onclick="fnCategory.onEdit(\'' . $value->slug . '\')">
                                        <svg  vg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    </a>
                                    <a href="javascript:;" class="btn-action text-danger" title="Hapus Kategori" onclick="fnCategory.onDelete(\'' . $value->slug . '\',\'' . csrf_token() . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-eraser"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 20h-10.5l-4.21 -4.3a1 1 0 0 1 0 -1.41l10 -10a1 1 0 0 1 1.41 0l5 5a1 1 0 0 1 0 1.41l-9.2 9.3" /><path d="M18 13.3l-6.3 -6.3" /></svg>
                                    </a>
                                </div>';

                    $results[] = [
                        $no,
                        $value->name,
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
