<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Home;
use App\Models\HomeRule;
use App\Models\Rule;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Pages.Master.Rules.index', [
            'title' =>  'Aturan Kos',
            'pageTitle' =>  'Aturan Kos',
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
        ], [
            'name.required' =>  "Aturan wajib diisi",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors(),
                ]
            ]);
        }

        $slug = SlugService::createSlug(Rule::class, 'slug', $request->name, ['unique' => false]);

        $validator = Validator::make(['slug' => $slug], [
            'slug'  =>  'unique:rules,slug'
        ], [
            'slug.unique' =>  "Aturan ini sudah ada",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors(),
                ]
            ]);
        }

        $data = [
            'name'  => Str::title($request->name),
            'slug'  =>  $slug,
            'index'     =>  Rule::max('index') + 1,
        ];

        $newRule = Rule::create($data);
        if ($newRule) {
            DB::commit();

            $homes = Home::where('is_active', true)->get();

            foreach ($homes as $key => $value) {
                HomeRule::create([
                    'home_id'   =>  $value->id,
                    'rule_id'   =>  $newRule->id,
                    'index'   =>  $newRule->index,
                ]);
            }

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Data berhasil disimpan'
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Data gagal disimpan'
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Rule $rule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rule $rule)
    {
        return response()->json($rule);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rule $rule)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'name'  =>  'required',
        ], [
            'name.required' =>  "Aturan wajib diisi",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors(),
                ]
            ]);
        }

        $slug = SlugService::createSlug(Rule::class, 'slug', $request->name, ['unique' => false]);

        if ($rule->slug != $slug) {
            $validator = Validator::make(['slug' => $slug], [
                'slug'  =>  'unique:rules,slug'
            ], [
                'slug.unique' =>  "Aturan ini sudah ada",
            ]);

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
            'name'  => Str::title($request->name),
            'slug'  =>  $slug,
            'index'     =>  $rule->index,
        ];

        if (Rule::find($rule->id)->update($data)) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Data berhasil diubah'
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Data gagal diubah'
            ]
        ]);
    }

    function reOrder(Request $request)
    {
        DB::beginTransaction();

        $totalData = 0;

        foreach ($request->dataReorder as $key => $value) {
            $data = [
                'index' =>  $value['newPosition'] + 1,
            ];

            if (Rule::find($value['id'])->update($data)) {
                HomeRule::where('rule_id', $value['id'])
                    ->update([
                        'index' =>  $value['newPosition'] + 1
                    ]);

                $totalData++;
            }
        }

        if (collect($request->dataReorder)->count() == $totalData) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Data berhasil diurutkan'
                ]
            ]);
        }

        DB::rollback();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Data gagal diurutkan'
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rule $rule)
    {
        DB::beginTransaction();

        if (Rule::find($rule->id)->delete()) {
            DB::commit();
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Data berhasil dihapus'
                ]
            ]);
        }

        DB::rollback();
        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Data gagal dihapus'
            ]
        ]);
    }

    function getAllData(Request $request)
    {
        $rules = collect(Rule::where('is_active', true)
            ->orderBy('index')
            ->get())->chunk(100);

        $results = array();
        $no = 1;

        if ($rules) {
            foreach ($rules as $c => $chunk) {
                foreach ($chunk as $key => $value) {
                    $btnAction = '<div class="d-flex gap-2">
                                        <button class="btn btn-warning" title="Ubah Data" onclick="fnAturan.onEdit(\'' . $value->slug . '\')">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                        </button>
                                        <button class="btn btn-danger" title="Hapus Data" onclick="fnAturan.onDelete(\'' . $value->slug . '\',\'' . csrf_token() . '\')">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                        </button>
                                    </div>';
                    $reOrder = '<div class="d-flex">
                                    <button title="Drag to Re-Order Row" class="btn btn-action">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-menu-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6l16 0" /><path d="M4 12l16 0" /><path d="M4 18l16 0" /></svg>
                                        ' . $no . '
                                    </button>
                                </div>';
                    $results[] = [
                        $reOrder,
                        $value->name,
                        $btnAction,
                        $value->id,
                        csrf_token(),
                    ];

                    $no++;
                }
            }
        }

        return response()->json([
            'data'  =>  $results,
        ]);
    }
}
