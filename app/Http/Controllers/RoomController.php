<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Room;
use App\Models\RoomFacility;
use App\Models\RoomPicture;
use App\Models\RoomPrice;
use App\Models\RoomRule;
use App\Models\Rule;
use App\Models\SharedFacility;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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
        $validator = Validator::make($request->all(), [
            'name'  =>  'required',
            'category'  =>  'required',
            'home'  =>  'required',
            'roomFacilities'    =>  'required',
        ], [
            'name.required' =>  "Nomor Kamar wajib diisi",
            'category.required' =>  "Kategori Kamar wajib dipilih",
            'home.required' =>  "Lokasi Kost wajib dipilih",
            'roomFacilities.required' =>  "Fasilitas Kamar wajib dipilih",
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

        $room = Room::create($data);

        if ($room) {
            $dataRoomFacilities = array();
            $dataRoomPrice = array();
            foreach ($request->roomFacilities as $key => $value) {
                $dataRoomFacilities[] = [
                    'room_id'   =>  $room->id,
                    'facility_id'   =>  $value,
                    'created_at' => Carbon::now('Asia/Jakarta'),
                    'updated_at' => Carbon::now('Asia/Jakarta'),
                ];
            }

            if ($request->dailyPrice && $request->dailyPrice > 0) {
                $dataRoomPrice[] = [
                    'room_id'   =>  $room->id,
                    'type'  =>  'daily',
                    'price' =>  $request->dailyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if ($request->weeklyPrice && $request->weeklyPrice > 0) {
                $dataRoomPrice[] = [
                    'room_id'   =>  $room->id,
                    'type'  =>  'weekly',
                    'price' =>  $request->weeklyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if ($request->monthlyPrice && $request->monthlyPrice > 0) {
                $dataRoomPrice[] = [
                    'room_id'   =>  $room->id,
                    'type'  =>  'monthly',
                    'price' =>  $request->monthlyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if ($request->yearlyPrice && $request->yearlyPrice > 0) {
                $dataRoomPrice[] = [
                    'room_id'   =>  $room->id,
                    'type'  =>  'yearly',
                    'price' =>  $request->yearlyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if (RoomFacility::insert($dataRoomFacilities)) {
                if (RoomPrice::insert($dataRoomPrice)) {
                    return response()->json([
                        'data'  =>  [
                            'status'    =>  true,
                            'message'   =>  'Kamar berhasil disimpan',
                        ]
                    ]);
                }

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  'Kamar gagal disimpan, terjadi kesalahan saat menyimpan harga kamar',
                    ]
                ]);
            }

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Kamar gagal disimpan, terjadi kesalahan pada saat menyimpan fasiltias kamar',
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Kamar gagal disimpan',
            ]
        ]);
    }

    function uploadPicture(Request $request)
    {
        $room = Room::where('slug', $request->slugUploadRoom)->first();

        $files = $request->file('files');
        $data = array();
        foreach ($files as $key => $value) {
            $dataUpload = $value->store('upload/room');

            $data[] = [
                'room_id'   =>  $room->id,
                'blob'  =>  base64_encode(file_get_contents(public_path('assets/' . $dataUpload))),
                'file_location' =>  base_path($dataUpload),
                'file_name' =>  basename($dataUpload),
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ];
        }

        if (RoomPicture::insert($data)) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Gambar kamar,berhasil diunggah'
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Gambar kamar,gagal diunggah'
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
        $picture = RoomPicture::where('room_id', $room->id)->get();

        $data = '<div id="carousel-indicators-thumb" class="carousel slide carousel-fade" data-bs-ride="carousel">
                    <div class="carousel-indicators carousel-indicators-thumb">';
        if ($picture) {
            foreach ($picture as $key => $value) {
                $data .= '<a href="javascript:;" type="button" data-bs-target="#carousel-indicators-thumb" data-bs-slide-to="' . $key . '" class="ratio ratio-4x3 active" style="background-image: url(' . asset('assets/upload/room/' . $value->file_name) . ')"></a>';
            }
        }
        $data .= '</div>
                    <div class="carousel-inner">';
        if ($picture) {
            foreach ($picture as $key => $value) {
                $data .= '<div class="carousel-item ' . ($key == 0 ? 'active' : '') . '">
                                            <img class="d-block w-100" alt="" src="' . asset('assets/upload/room/' . $value->file_name) . '" />
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
        $validator = Validator::make($request->all(), [
            'name'  =>  'required',
            'category'  =>  'required',
            'home'  =>  'required',
            'roomFacilities'    =>  'required',
        ], [
            'name.required' =>  "Nomor Kamar wajib diisi",
            'category.required' =>  "Kategori Kamar wajib dipilih",
            'home.required' =>  "Lokasi Kost wajib dipilih",
            'roomFacilities.required' =>  "Fasilitas Kamar wajib dipilih",
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
            RoomFacility::where('room_id', $room->id)->delete();
            RoomPrice::where('room_id', $room->id)->delete();
            $dataRoomFacilities = array();
            $dataRoomPrice = array();
            foreach ($request->roomFacilities as $key => $value) {
                $dataRoomFacilities[] = [
                    'room_id'   =>  $room->id,
                    'facility_id'   =>  $value,
                    'created_at' => Carbon::now('Asia/Jakarta'),
                    'updated_at' => Carbon::now('Asia/Jakarta'),
                ];
            }

            if ($request->dailyPrice && $request->dailyPrice > 0) {
                $dataRoomPrice[] = [
                    'room_id'   =>  $room->id,
                    'type'  =>  'daily',
                    'price' =>  $request->dailyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if ($request->weeklyPrice && $request->weeklyPrice > 0) {
                $dataRoomPrice[] = [
                    'room_id'   =>  $room->id,
                    'type'  =>  'weekly',
                    'price' =>  $request->weeklyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if ($request->monthlyPrice && $request->monthlyPrice > 0) {
                $dataRoomPrice[] = [
                    'room_id'   =>  $room->id,
                    'type'  =>  'monthly',
                    'price' =>  $request->monthlyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if ($request->yearlyPrice && $request->yearlyPrice > 0) {
                $dataRoomPrice[] = [
                    'room_id'   =>  $room->id,
                    'type'  =>  'yearly',
                    'price' =>  $request->yearlyPrice,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if (RoomFacility::insert($dataRoomFacilities)) {
                if (RoomPrice::insert($dataRoomPrice)) {
                    return response()->json([
                        'data'  =>  [
                            'status'    =>  true,
                            'message'   =>  'Kamar berhasil diubah',
                        ]
                    ]);
                }

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  'Kamar gagal diubah, terjadi kesalahan saat menyimpan harga kamar',
                    ]
                ]);
            }

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Kamar gagal diubah, terjadi kesalahan pada saat menyimpan fasiltias kamar',
                ]
            ]);
        }

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
        if (RoomPrice::where('room_id', $room->id)->delete()) {
            if (RoomFacility::where('room_id', $room->id)->delete()) {
                if (Room::find($room->id)->delete()) {
                    return response()->json([
                        'data'  =>  [
                            'status'    =>  true,
                            'message'   =>  'Kamar berhasil dihapus'
                        ]
                    ]);
                }

                return response()->json([
                    'data'  =>  [
                        'status'    =>  true,
                        'message'   =>  'Kamar gagal dihapus'
                    ]
                ]);
            }

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Kamar gagal dihapus, terjadi kesalahan saat menghapus fasilitas kamar'
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  true,
                'message'   =>  'Kamar gagal dihapus, terjadi kesalahan saat menghapus harga kamar'
            ]
        ]);
    }

    public function destroyPicture(Room $room)
    {
        $dataPicture = RoomPicture::where('room_id', $room->id)->get();

        if ($dataPicture) {
            foreach ($dataPicture as $key => $value) {
                File::delete($value->file_location);
            }
        }

        if (RoomPicture::where('room_id', $room->id)->delete()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Gambar Kamar berhasil dihapus'
                ]
            ]);
        }

        return response()->json([
            'data'  =>  [
                'status'    =>  true,
                'message'   =>  'Gambar Kamar gagal dihapus'
            ]
        ]);
    }

    function getAllData(Request $request)
    {
        $totalRooms = Room::count();

        $filterRooms = Room::search(['search' => $request->search['value']])
            ->count();

        $rooms = collect(Room::search(['search' => $request->search['value']])
            ->skip($request->start)
            ->limit($request->length)
            ->orderBy('number_room')
            ->get())->chunk(10);

        $results = array();
        $no = $request->start + 1;

        if ($rooms) {
            foreach ($rooms as $key => $chunk) {
                foreach ($chunk as $key => $value) {
                    $roomFacility = 'Tidak ada fasilitas kamar yang dipilih';
                    $sharedFacility = 'Tidak ada fasilitas kos yang dipilih';
                    $prices = 'Belum ada harga untuk kamar ini';

                    $btnUpload = '<a href="javascript:;" class="btn-action text-primary" title="Unggah Foto" onclick="fnRoom.uploadPicture(\'' . $value->slug . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-upload"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
                                </a>';

                    if (collect($value->roomPicture)->count() > 0) {
                        $btnUpload = '<a href="javascript:;" class="btn-action text-danger" title="Hapus Foto" onclick="fnRoom.deletePicture(\'' . $value->slug . '\',\'' . csrf_token() . '\')">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-photo-minus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M12.5 21h-6.5a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v9" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l4 4" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l2 2" /><path d="M16 19h6" /></svg>
                                </a>
                                <a href="javascript:;" class="btn-action text-primary" title="Lihat Foto" onclick="fnRoom.viewPicture(\'' . $value->slug . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-album"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M12 4v7l2 -2l2 2v-7" /></svg>
                                </a>';
                    }

                    $btnAction = '<div class="d-flex gap-2">
                                    ' . $btnUpload . '
                                    <a href="javascript:;" class="btn-action text-warning" title="Ubah Kamar" onclick="fnRoom.onEdit(\'' . $value->slug . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-photo-minus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M12.5 21h-6.5a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v9" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l4 4" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l2 2" /><path d="M16 19h6" /></svg>
                                    </a>
                                    <a href="javascript:;" class="btn-action text-danger" title="Hapus Kamar" onclick="fnRoom.onDelete(\'' . $value->slug . '\',\'' . csrf_token() . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-eraser"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 20h-10.5l-4.21 -4.3a1 1 0 0 1 0 -1.41l10 -10a1 1 0 0 1 1.41 0l5 5a1 1 0 0 1 0 1.41l-9.2 9.3" /><path d="M18 13.3l-6.3 -6.3" /></svg>
                                    </a>
                                </div>';

                    if ($value->roomPrice) {
                        $prices = '<div class="row g-1">';

                        $roomPrices = collect($value->roomPrice)->chunk(10);

                        if ($roomPrices) {
                            foreach ($roomPrices as $key => $chunkPrice) {
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

                    if ($value->home) {
                        if ($value->home->sharedFacility) {
                            $sharedFacility = '<div class="row g-1">';

                            $sharedFacilities = collect($value->home->sharedFacility)->chunk(10);

                            if ($sharedFacilities) {
                                foreach ($sharedFacilities as $key => $chunkFacility) {
                                    foreach ($chunkFacility as $key => $valueFacility) {
                                        $sharedFacility .= '<div class="col-6">
                                        <div class="row g-1 align-items-center">
                                          <div class="col">
                                            <div class="text-reset d-block">' . $valueFacility->facility->name . '</div>
                                          </div>
                                        </div>
                                      </div>';
                                    }
                                }
                            }

                            $sharedFacility .= '</div>';
                        }
                    }

                    if ($value->roomFacility) {
                        $roomFacility = '<div class="row g-1">';

                        $roomFacilities = collect($value->roomFacility)->chunk(10);

                        if ($roomFacilities) {
                            foreach ($roomFacilities as $key => $chunkFacility) {
                                foreach ($chunkFacility as $key => $valueFacility) {
                                    $roomFacility .= '<div class="col-6">
                                    <div class="row g-1 align-items-center">
                                      <div class="col">
                                        <div class="text-reset d-block">' . $valueFacility->facility->name . '</div>
                                      </div>
                                    </div>
                                  </div>';
                                }
                            }
                        }

                        $roomFacility .= '</div>';
                    }

                    $results[] = [
                        $no,
                        $value->number_room,
                        $value->home->name,
                        $value->category->name,
                        $roomFacility,
                        $sharedFacility,
                        $prices,
                        $btnAction
                    ];

                    $no++;
                }
            }
        }

        return response()->json([
            'draw'  =>  $request->draw,
            'recordsTotal'  =>  $totalRooms,
            'recordsFiltered'   =>  $filterRooms,
            'data'  =>  $results
        ]);
    }
}
