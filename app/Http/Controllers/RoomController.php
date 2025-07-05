<?php

namespace App\Http\Controllers;

use App\Models\CategoryPicture;
use App\Models\Room;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Pages.Master.Room.index', [
            'title' =>  'Kamar',
            'pageTitle' =>  'Kamar',

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
            'category'  =>  'required',
            'home'  =>  'required',
        ], [
            'name.required' =>  "Nomor Kamar wajib diisi",
            'category.required' =>  "Kategori Kamar wajib dipilih",
            'home.required' =>  "Lokasi Kost wajib dipilih",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        $slug = SlugService::createSlug(Room::class, 'slug', $request->name, ['unique' => false]);

        $validator = Validator::make(['slug' => $slug], [
            'slug'  =>  'unique:rooms,slug'
        ], [
            'slug.unique'   =>  'Kamar ini sudah terdaftar'
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
            'home_id'   =>  $request->home,
            'category_id'   =>  $request->category,
            'number_room'   =>  $request->name,
            'slug'  =>  $slug,
        ];

        if (Room::create($data)) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Kamar berhasil disimpan',
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Kamar gagal disimpan',
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        //
    }

    public function showPicture(Room $room)
    {
        $room->load('category');
        $picture = CategoryPicture::where('category_id', $room->category->id)->get();

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
    public function edit(Room $room)
    {
        return response()->json($room);
    }

    public function getDataUpload(Room $room)
    {
        return response()->json($room);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'name'  =>  'required',
            'category'  =>  'required',
            'home'  =>  'required',
        ], [
            'name.required' =>  "Nomor Kamar wajib diisi",
            'category.required' =>  "Kategori Kamar wajib dipilih",
            'home.required' =>  "Lokasi Kost wajib dipilih",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        $slug = SlugService::createSlug(Room::class, 'slug', $request->name, ['unique' => false]);

        if ($slug != $room->slug) {
            $validator = Validator::make(['slug' => $slug], [
                'slug'  =>  'unique:rooms,slug'
            ], [
                'slug.unique'   =>  'Kamar ini sudah terdaftar'
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
            'home_id'   =>  $request->home,
            'category_id'   =>  $request->category,
            'number_room'   =>  $request->name,
            'slug'  =>  $slug,
        ];

        if (Room::find($room->id)->update($data)) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Kamar berhasil diubah',
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Kamar gagal diubah',
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        DB::beginTransaction();

        if (Room::find($room->id)->delete()) {
            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Kamar berhasil dihapus'
                ]
            ]);
        }

        DB::rollBack();

        return response()->json([
            'data'  =>  [
                'status'    =>  true,
                'message'   =>  'Kamar gagal dihapus, terjadi kesalahan saat menghapus harga kamar'
            ]
        ]);
    }

    function getAllData(Request $request)
    {
        $rooms = collect(Room::with(['rent', 'rent.member', 'rent.member.user'])->searchCategory($request->category)
            ->orderBy('number_room')
            ->get())->chunk(10);

        $results = array();
        $no = 1;

        if ($rooms) {
            foreach ($rooms as $key => $chunk) {
                foreach ($chunk as $key => $value) {
                    // dd($value);
                    $btnUpload = '';
                    if (collect($value->category->pictures)->count() > 0) {
                        $btnUpload = '<a href="javascript:;" class="btn-action text-primary" title="Lihat Foto" onclick="fnRoom.viewPicture(\'' . $value->slug . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-album"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M12 4v7l2 -2l2 2v-7" /></svg>
                                </a>';
                    }

                    $btnAction = '<div class="d-flex gap-2">
                                    ' . $btnUpload . '
                                    <a href="javascript:;" class="btn-action text-warning" title="Ubah Kamar" onclick="fnRoom.onEdit(\'' . $value->slug . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    </a>
                                    <a href="javascript:;" class="btn-action text-danger" title="Hapus Kamar" onclick="fnRoom.onDelete(\'' . $value->slug . '\',\'' . csrf_token() . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-eraser"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 20h-10.5l-4.21 -4.3a1 1 0 0 1 0 -1.41l10 -10a1 1 0 0 1 1.41 0l5 5a1 1 0 0 1 0 1.41l-9.2 9.3" /><path d="M18 13.3l-6.3 -6.3" /></svg>
                                    </a>
                                </div>';

                    $status = '<span class="badge bg-blue-lt">Tersedia</span>';

                    if ($value->rent) {
                        if ($value->rent->is_approve) {
                            $status = '<span class="badge bg-green-lt">Sudah Disewa</span>';
                        } else {
                            $status = '<span class="badge bg-red-lt">Perlu Approval</span>';
                        }
                    }

                    $results[] = [
                        $no,
                        $value->number_room,
                        $value->home->name,
                        $value->category->name,
                        $value->rent ? ($value->rent->member->user ? $value->rent->member->user->name : "") : "-",
                        $value->rent ? Carbon::parse($value->rent->start_date)->isoFormat("DD MMMM YYYY") . ' - ' . Carbon::parse($value->rent->end_date)->isoFormat("DD MMMM YYYY") : "-",
                        $status,
                        $btnAction
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
