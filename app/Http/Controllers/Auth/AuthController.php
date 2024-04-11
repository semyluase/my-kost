<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    function index()
    {
        return view('Guest.index', [
            'title' =>  "Login",
        ]);
    }

    function login(Request $request)
    {
        $credentials = $request->validate([
            'username'  =>  'required',
            'password'  =>  'required',
        ]);

        $credentials['remember'] = $request->rememberMe;

        if (Auth::attempt(['username'   =>  $credentials['username'], 'password'    =>  $credentials['password']], $credentials['remember'])) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->with([
            'alert' => 'Failed Login',
        ]);
    }

    function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json([
            'data'  =>  [
                'status'    =>  true,
                'url'       =>  url('/login')
            ]
        ]);
    }
}
