<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function index()
    {
        return view('Pages.Dashboard.index', [
            'title' =>  "Dashboard",
            'pageTitle' =>  "Dashboard"
        ]);
    }
}
