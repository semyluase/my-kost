<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Pages.Setting.Role.index', [
            'title' =>  'Role',
            'pageTitle' =>  'Role',
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
            'name'  =>  'required'
        ], [
            'name.required' =>  'Nama wajib diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        $slug = SlugService::createSlug(Role::class, 'slug', $request->name, ['unique' => false]);

        $validator = Validator::make(['slug'    =>  $slug], [
            'slug'  =>  'unique:roles,slug'
        ], [
            'slug.unique'   =>  'Role ini sudah terdaftar'
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

        if (Role::create($data)) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Role berhasil disimpan'
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Role gagal disimpan'
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name'  =>  'required'
        ], [
            'name.required' =>  'Nama wajib diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        $slug = SlugService::createSlug(Role::class, 'slug', $request->name, ['unique' => false]);

        if ($slug != $role->slug) {
            $validator = Validator::make(['slug'    =>  $slug], [
                'slug'  =>  'unique:roles,slug'
            ], [
                'slug.unique'   =>  'Role ini sudah terdaftar'
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


        $data = [
            'name'  =>  Str::upper($request->name),
            'slug'  =>  $slug,
        ];

        if (Role::find($role->id)->update($data)) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Role berhasil diubah'
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Role gagal diubah'
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (Role::find($role->id)->delete()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Role berhasil dihapus'
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Role gagal dihapus'
            ]
        ]);
    }

    function getAllData(Request $request)
    {
        $totalRoles = Role::count();

        $filteredRoles = Role::search(['search' =>  $request->search['value']])
            ->count();

        $roles = collect(Role::search(['search' => $request->search['value']])
            ->skip($request->start)
            ->limit($request->length)
            ->get())->chunk($request->length);

        $results = array();
        $no = $request->start + 1;

        if ($roles) {
            foreach ($roles as $key => $chunk) {
                foreach ($chunk as $key => $value) {
                    $btnAction = '<div class="d-flex gap-2">
                                    <a href="javascript:;" class="btn-action text-warning" title="Ubah Role" onclick="fnRole.onEdit(\'' . $value->slug . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    </a>
                                </div>';

                    $results[] = [
                        $no,
                        $value->name,
                        $btnAction
                    ];

                    $no++;
                }
            }

            return response()->json([
                'draw'  =>  $request->draw,
                'data'  =>  $results,
                'recordsTotal'  =>  $totalRoles,
                'recordsFiltered'   =>  $filteredRoles
            ]);
        }
    }
}
