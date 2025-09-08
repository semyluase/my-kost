<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryOrder;
use App\Models\FoodSnack;
use App\Models\Home;
use App\Models\Location;
use App\Models\Master\Bank;
use App\Models\Master\CategoryLaundry;
use App\Models\Member;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use GuzzleHttp\Client;
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

    function getCategoryForTransaction()
    {
        $categories = collect(Category::whereHas('prices')->where('is_active', true)->get())->chunk(10);

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

    function getRole()
    {
        $roles = collect(Role::whereNot('slug', 'member')->get())->chunk(10);

        $results = array();

        if ($roles) {
            foreach ($roles as $key => $chunk) {
                foreach ($chunk as $key => $value) {
                    $results[] = [
                        'label' =>  $value->name,
                        'value' =>  $value->id,
                    ];
                }
            }
        }

        return response()->json($results);
    }

    function getRoom()
    {
        $rooms = Room::where('home_id', auth()->user()->home_id)
            ->get();

        $results = array();

        if ($rooms) {
            foreach ($rooms as $key => $value) {
                $results[] = [
                    'label' =>  $value->number_room,
                    'value' =>  $value->slug,
                ];
            }
        }

        return response()->json($results);
    }

    function getRoomByCategory(Request $request)
    {
        $rooms = Room::where('home_id', auth()->user()->home_id)
            ->where('category_id', $request->category)
            ->whereDoesntHave('rent')
            ->get();

        $results = array();

        if ($rooms) {
            foreach ($rooms as $key => $value) {
                $results[] = [
                    'label' =>  $value->number_room,
                    'value' =>  $value->slug,
                ];
            }
        }

        return response()->json($results);
    }

    function getMember()
    {
        $members = User::where('role_id', 3)->get();

        $results = array();

        if ($members) {
            foreach ($members as $key => $value) {
                $results[] = [
                    'label' =>  $value->phone_number,
                    'value' =>  $value->phone_number,
                ];
            }
        }

        return response()->json($results);
    }

    function getBank()
    {
        $banks = collect(Bank::get())->chunk(100);

        $results = array();

        if ($banks) {
            foreach ($banks as $key => $chunk) {
                foreach ($chunk as $c => $value) {
                    $results[] = [
                        'label' =>  $value->nama,
                        'value' =>  $value->id,
                    ];
                }
            }
        }

        return response()->json($results);
    }

    function getCategoryLaundry()
    {
        $categoryLaundry = collect(CategoryLaundry::where('is_active', true)->get())->chunk(100);

        $results = array();

        if ($categoryLaundry) {
            foreach ($categoryLaundry as $key => $chunk) {
                foreach ($chunk as $c => $value) {
                    $results[] = [
                        'label' =>  $value->name,
                        'value' =>  $value->id,
                    ];
                }
            }
        }

        return response()->json($results);
    }

    function getItems()
    {
        $items = collect(FoodSnack::get())->chunk(100);

        $results = array();

        if ($items) {
            foreach ($items as $key => $chunk) {
                foreach ($chunk as $c => $value) {
                    $results[] = [
                        'label' =>  $value->name,
                        'value' =>  $value->code_item,
                    ];
                }
            }
        }

        return response()->json($results);
    }

    function getCategoryOrder()
    {
        $categoryOrders = collect(CategoryOrder::where('is_active', true)->get())->chunk(100);

        $results = array();

        if ($categoryOrders) {
            foreach ($categoryOrders as $key => $chunk) {
                foreach ($chunk as $c => $value) {
                    $results[] = [
                        'label' =>  $value->name,
                        'value' =>  $value->short_name,
                    ];
                }
            }
        }

        return response()->json($results);
    }
}
