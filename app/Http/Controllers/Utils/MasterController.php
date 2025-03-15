<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    function getUserMember(Request $request)
    {
        $users = User::with('member')->whereHas("member")->get();

        $results = array();

        if ($users) {
            foreach ($users as $key => $value) {
                $results[] = [
                    '<a href="javascript:;" class="link" title="Pilih Member" onclick="fnTransaction.onSelectMember(\'0' . $value->phone_number . '\')">0' . $value->phone_number . '</a>',
                    $value->name,
                    $value->member ? '<span class="badge badge-success">Member</span>' : '<span class="badge badge-danger">Belum Member</span>'
                ];
            }
        }

        return response()->json([
            'data'  =>  $results
        ]);
    }
}
