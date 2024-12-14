<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Rule;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    function index()
    {
        $sharedFacilities = Facility::where('is_shared_facility', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categoryFacilities = Facility::where('is_room_facility', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $rules = Rule::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('Pages.Master.index', [
            'title' =>  'Master',
            'pageTitle' =>  'Master',
            'sharedFacilities'    =>  $sharedFacilities,
            'categoryFacilities'    =>  $categoryFacilities,
            'rules' =>  $rules
        ]);
    }
}
