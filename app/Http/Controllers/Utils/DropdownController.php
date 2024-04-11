<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Home;
use App\Models\Location;
use Illuminate\Http\Request;

class DropdownController extends Controller
{
    function getHome()
    {
        $homes = collect(Home::where('is_active', true)->get())->chunk(10);

        $results = array();

        if ($homes) {
            foreach ($homes as $key => $chunk) {
                foreach ($chunk as $key => $value) {
                    $results[] = [
                        'label' =>  $value->name . ' - ' . $value->city,
                        'value' =>  $value->id
                    ];
                }
            }
        }

        return response()->json($results);
    }

    function getCategory()
    {
        $categories = collect(Category::where('is_active', true)->get())->chunk(10);

        $results = array();

        if ($categories) {
            foreach ($categories as $key => $chunk) {
                foreach ($chunk as $key => $value) {
                    $results[] = [
                        'label' =>  $value->name,
                        'value' =>  $value->id
                    ];
                }
            }
        }

        return response()->json($results);
    }
}
