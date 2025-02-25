<?php

namespace App\Http\Controllers\TMP;

use App\Http\Controllers\Controller;
use App\Models\TMP\UserIdentity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserIdentityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(UserIdentity $userIdentity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserIdentity $userIdentity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserIdentity $userIdentity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserIdentity $userIdentity)
    {
        //
    }

    function uploadIdentity(Request $request)
    {
        DB::beginTransaction();

        $data = array();
        $file = $request->file('userfile');
        if ($file) {
            $dataUpload = $file->store('upload/userIdentity');
            $token = Str::random(32);
            $data = [
                'token' =>  $token,
                'file_location' =>  base_path($dataUpload),
                'file_name' =>  basename($dataUpload),
            ];
        }

        if (UserIdentity::create($data)) {
            DB::commit();

            return response()->json([
                'status'    =>  true,
                'token' =>  $token,
            ]);
        }

        DB::rollback();

        return response()->json([
            'data'  =>  [
                'status'    =>  false,
                'token' =>  null,
            ]
        ]);
    }
}
