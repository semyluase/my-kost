<?php

namespace App\Http\Controllers;

use App\Models\Home;
use App\Models\HomePicture;
use App\Models\HomeRule;
use App\Models\SharedFacility;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Pages.Master.Home.index', [
            'title' =>  'Identitas Kos',
            'pageTitle' =>  'Identitas Kos',
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
            'city'  =>  'required',
            'phone'  =>  'required',
            'email'  =>  'required|email',
            'address'   =>  'required'
        ], [
            'name.required' =>  'Nama wajib diisi',
            'city.required' =>  'Kota wajib diisi',
            'phone.required' =>  'No. Telp wajib diisi',
            'email.required' =>  'Email wajib diisi',
            'email.email' =>  'Format Email salah',
            'address.required' =>  'Alamat wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        $slug = SlugService::createSlug(Home::class, 'slug', $request->name, ['unique' => false]);

        $validator = Validator::make(['slug'    =>  $slug], [
            'slug'  =>  'unique:homes,slug'
        ], [
            'slug.unique' =>  'Kos ini sudah terdaftar',
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
            'city'  =>  Str::upper($request->city),
            'phone_number'  =>  $request->phone,
            'email'  =>  $request->email,
            'address'  =>  Str::title($request->address),
        ];

        $home = Home::create($data);
        if ($home) {
            $dataSharedFacilites = array();
            $dataRules = array();

            foreach ($request->sharedFacilities as $key => $value) {
                $dataSharedFacilites[] = [
                    'home_id'   =>  $home->id,
                    'facility_id'   =>  $value,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            foreach ($request->rules as $key => $value) {
                $dataRules[] = [
                    'home_id'   =>  $home->id,
                    'rule_id'   =>  $value,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if (SharedFacility::insert($dataSharedFacilites)) {
                if (HomeRule::insert($dataRules)) {
                    DB::commit();
                    return response()->json([
                        'data'  =>  [
                            'status'    =>  true,
                            'message'   =>  'Identitas Kos berhasil disimpan'
                        ]
                    ]);
                }

                DB::rollBack();
                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  'Identitas Kos gagal disimpan, terjadi kesalahan saat menyimpan aturan kos'
                    ]
                ]);
            }

            DB::rollBack();
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Identitas Kos gagal disimpan, terjadi kesalahan saat menyimpan fasilitas kos'
                ]
            ]);
        }

        DB::rollBack();
        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Identitas Kos gagal disimpan'
            ]
        ]);
    }

    function uploadPicture(Request $request)
    {
        DB::beginTransaction();
        $home = Home::where('slug', $request->slugUploadHome)->first();

        $files = $request->file('files');
        $data = array();
        foreach ($files as $key => $value) {
            $dataUpload = $value->store('upload/home');

            $data[] = [
                'home_id'   =>  $home->id,
                'blob'  =>  base64_encode(file_get_contents(public_path('assets/' . $dataUpload))),
                'file_location' =>  base_path($dataUpload),
                'file_name' =>  basename($dataUpload),
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ];
        }

        if (HomePicture::insert($data)) {
            DB::commit();
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Gambar rumah kost, berhasil diunggah'
                ]
            ]);
        }

        DB::rollBack();
        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Gambar rumah kost, gagal diunggah'
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Home $home)
    {
        //
    }

    public function showPicture(Home $home)
    {
        $picture = HomePicture::where('home_id', $home->id)->get();

        $data = '<div id="carousel-indicators-thumb" class="carousel slide carousel-fade" data-bs-ride="carousel">
                    <div class="carousel-indicators carousel-indicators-thumb">';
        if ($picture) {
            foreach ($picture as $key => $value) {
                $data .= '<a href="javascript:;" type="button" data-bs-target="#carousel-indicators-thumb" data-bs-slide-to="' . $key . '" class="ratio ratio-4x3 active" style="background-image: url(' . asset('assets/upload/home/' . $value->file_name) . ')"></a>';
            }
        }
        $data .= '</div>
                    <div class="carousel-inner">';
        if ($picture) {
            foreach ($picture as $key => $value) {
                $data .= '<div class="carousel-item ' . ($key == 0 ? 'active' : '') . '">
                                            <img class="d-block w-100" alt="" src="' . asset('assets/upload/home/' . $value->file_name) . '" />
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
    public function edit(Home $home)
    {
        return response()->json($home);
    }

    public function getDataUpload(Home $home)
    {
        return response()->json($home);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Home $home)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'name'  =>  'required',
            'city'  =>  'required',
            'phone'  =>  'required',
            'email'  =>  'required|email',
            'address'   =>  'required'
        ], [
            'name.required' =>  'Nama wajib diisi',
            'city.required' =>  'Kota wajib diisi',
            'phone.required' =>  'No. Telp wajib diisi',
            'email.required' =>  'Email wajib diisi',
            'email.email' =>  'Format Email salah',
            'address.required' =>  'Alamat wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  $validator->errors()
                ]
            ]);
        }

        $slug = SlugService::createSlug(Home::class, 'slug', $request->name, ['unique' => false]);

        if ($slug != $home->slug) {
            $validator = Validator::make(['slug'    =>  $slug], [
                'slug'  =>  'unique:homes,slug'
            ], [
                'slug.unique' =>  'Kost ini sudah terdaftar',
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
            'city'  =>  Str::upper($request->city),
            'phone_number'  =>  $request->phone,
            'email'  =>  $request->email,
            'address'  =>  Str::title($request->address),
        ];

        if (Home::find($home->id)->update($data)) {
            $dataSharedFacilites = array();
            $dataRules = array();
            if ($request->sharedFacilities) {
                SharedFacility::where('home_id', $home->id)->delete();
            }

            if ($request->rules) {
                HomeRule::where('home_id', $home->id)->delete();
            }

            foreach ($request->sharedFacilities as $key => $value) {
                $dataSharedFacilites[] = [
                    'home_id'   =>  $home->id,
                    'facility_id'   =>  $value,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            foreach ($request->rules as $key => $value) {
                $dataRules[] = [
                    'home_id'   =>  $home->id,
                    'rule_id'   =>  $value,
                    'created_at'    =>  Carbon::now('Asia/Jakarta'),
                    'updated_at'    =>  Carbon::now('Asia/Jakarta'),
                ];
            }

            if (SharedFacility::insert($dataSharedFacilites)) {
                if (HomeRule::insert($dataRules)) {
                    DB::commit();
                    return response()->json([
                        'data'  =>  [
                            'status'    =>  true,
                            'message'   =>  'Identitas Kos berhasil diubah'
                        ]
                    ]);
                }
                DB::rollBack();

                return response()->json([
                    'data'  =>  [
                        'status'    =>  false,
                        'message'   =>  'Identitas Kos gagal diubah, terjadi kesalahan saat menyimpan aturan kos'
                    ]
                ]);
            }
            DB::rollBack();

            return response()->json([
                'data'  =>  [
                    'status'    =>  false,
                    'message'   =>  'Identitas Kos gagal diubah, terjadi kesalahan saat menyimpan fasilitas kos'
                ]
            ]);
        }

        DB::rollBack();
        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Identitas Kos gagal diubah'
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function activatedData(Home $home)
    {
        DB::beginTransaction();
        $status = $home->is_active ? false : true;

        if (Home::find($home->id)->update([
            'is_active' =>  $status
        ])) {
            DB::commit();
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  $home->is_active ? 'Kost berhasil dinonaktifkan' : 'Kost berhasil diaktifkan'
                ]
            ]);
        }

        DB::rollBack();
        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  $home->is_active ? 'Kost gagal dinonaktifkan ' : 'Kost gagal diaktifkan'
            ]
        ]);
    }

    public function destroy(Home $home)
    {
        DB::beginTransaction();
        if (Home::find($home->id)->delete()) {
            SharedFacility::where('home_id', $home->id)->delete();
            HomeRule::where('home_id', $home->id)->delete();

            if ($home->pictures) {
                foreach ($home->pictures as $key => $value) {
                    File::delete(public_path('assets/uploads/home/' . $value->file_name));
                }

                HomePicture::where('home_id', $home->id)->delete();
            }

            DB::commit();

            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Kost berhasil dihapus'
                ]
            ]);
        }

        DB::rollBack();
        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'message'   =>  'Kost gagal dihapus'
            ]
        ]);
    }

    public function destroyPicture(Home $home)
    {
        DB::beginTransaction();
        $dataPicture = HomePicture::where('home_id', $home->id)->get();


        if (HomePicture::where('home_id', $home->id)->delete()) {
            DB::commit();
            if ($dataPicture) {
                foreach ($dataPicture as $key => $value) {
                    File::delete($value->file_location);
                }
            }
            return response()->json([
                'data'  =>  [
                    'status'    =>  true,
                    'message'   =>  'Gambar Rumah Kost berhasil dihapus'
                ]
            ]);
        }

        DB::rollBack();
        return response()->json([
            'data'  =>  [
                'status'    =>  true,
                'message'   =>  'Gambar Rumah Kost gagal dihapus'
            ]
        ]);
    }

    function getAllData(Request $request)
    {
        $totalHome = Home::count();

        $filteredHome = Home::search(['search' => $request->search['value']])
            ->count();

        $homes = collect(Home::search(['search' => $request->search['value']])
            ->skip($request->start)
            ->limit($request->length)
            ->get())->chunk(10);

        $results = array();
        $no = $request->start + 1;

        if ($homes) {
            foreach ($homes as $key => $chunk) {
                foreach ($chunk as $key => $value) {
                    $homeRules = 'Tidak ada Aturan Kos yang dipilih';

                    $btnUpload = '<a href="javascript:;" class="btn-action text-primary" title="Unggah Foto" onclick="fnHome.uploadPicture(\'' . $value->slug . '\')">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-upload"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
                                </a>';

                    if (collect($value->roomPicture)->count() > 0) {
                        $btnUpload = '<a href="javascript:;" class="btn-action text-danger" title="Hapus Foto" onclick="fnHome.deletePicture(\'' . $value->slug . '\',\'' . csrf_token() . '\')">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-photo-minus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M12.5 21h-6.5a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v9" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l4 4" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l2 2" /><path d="M16 19h6" /></svg>
                                </a>';
                    }

                    if ($value->rule) {
                        $homeRules = '<div class="row g-3">';

                        $rules = collect($value->rule)->chunk(10);

                        $no = 1;
                        if ($rules) {
                            foreach ($rules as $key => $chunkRule) {
                                foreach ($chunkRule as $key => $valueRule) {
                                    if ($valueRule->rule) {
                                        $homeRules .= '<div class="col-6">
                                        <div class="row g-3 align-items-center">
                                          <div class="col">
                                            <div class="text-reset d-block text-truncate">' . $no . '. ' . $valueRule->rule->name . '</div>
                                          </div>
                                        </div>
                                      </div>';

                                        $no++;
                                    }
                                }
                            }
                        }

                        $homeRules .= '</div>';
                    }

                    $btnAction = '<div class="d-flex gap-2">
                                    ' . $btnUpload . '
                                    <a href="javascript:;" class="btn-action text-warning" title="Ubah Kost" onclick="fnHome.onEdit(\'' . $value->slug . '\')">
                                        <svg  vg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    </a>
                                    <a href="javascript:;" class="btn-action text-danger" title="Hapus Kost" onclick="fnHome.onDelete(\'' . $value->slug . '\',\'' . csrf_token() . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-eraser"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 20h-10.5l-4.21 -4.3a1 1 0 0 1 0 -1.41l10 -10a1 1 0 0 1 1.41 0l5 5a1 1 0 0 1 0 1.41l-9.2 9.3" /><path d="M18 13.3l-6.3 -6.3" /></svg>
                                    </a>
                                </div>';

                    $picture = 'Belum ada gambar rumah kos';

                    if (collect($value->pictures)->count() > 0) {
                        $picture = '<a href="javascript:;" class="btn-action text-primary" title="Gambar Rumah Kos" onclick="fnHome.viewPicture(\'' . $value->slug . '\')">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-album"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M12 4v7l2 -2l2 2v-7" /></svg>
                                    </a>
                                    <a href="javascript:;" class="btn-action text-danger" title="Hapus Gambar Rumah Kos" onclick="fnHome.deletePicture(\'' . $value->slug . '\',\'' . csrf_token() . '\')">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-photo-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M13 21h-7a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v7" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l3 3" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0" /><path d="M22 22l-5 -5" /><path d="M17 22l5 -5" /></svg>
                                    </a>';
                    }

                    $results[] = [
                        $no,
                        $value->name . '<div class="text-secondary">' . $value->phone_number . '</div><div class="text-secondary">' . $value->email . '</div>',
                        $value->city . '</br>' . $value->address,
                        $homeRules,
                        $picture,
                        $btnAction
                    ];

                    $no++;
                }
            }
        }

        return response()->json([
            'draw'  =>  $request->draw,
            'data'  =>  $results,
            'recordsTotal'  =>  $totalHome,
            'recordsFiltered'  =>  $filteredHome,
        ]);
    }
}
