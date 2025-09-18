<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    function index()
    {
        if (Auth::user()->role->slug == 'staff') {
            return view('Pages.Dashboard.Staff.index', [
                'title' =>  "Dashboard",
                'pageTitle' =>  "Dashboard"
            ]);
        }
        return view('Pages.Dashboard.index', [
            'title' =>  "Dashboard",
            'pageTitle' =>  "Dashboard"
        ]);
    }
}
