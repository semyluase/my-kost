<?php

namespace App\Http\Controllers;

use App\Models\TransactionDetail;
use App\Models\TransactionHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransactionServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Pages.Services.index', [
            'title' =>  "Order",
            'pageTitle' =>  'Order'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        switch ($request->service) {
            case 'laundry':
                $view = 'Pages.Services.laundry';
                $title = 'Laundry';
                $pageTitle = 'Laundry';

                $data = TransactionDetail::where('is_service', true)
                    ->where('tgl_masuk', Carbon::now('Asia/Jakarta'))
                    ->get();
                break;

            default:
                # code...
                break;
        }

        return view($view, [
            'title' =>  $title,
            'pageTitle' =>  $pageTitle,
            'data'  =>  $data
        ]);
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
    public function show(TransactionHeader $transactionHeader)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionHeader $transactionHeader)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransactionHeader $transactionHeader)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionHeader $transactionHeader)
    {
        //
    }
}
