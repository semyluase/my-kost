<?php

namespace App\Http\Controllers\Master\Service;

use App\Http\Controllers\Controller;
use App\Models\Master\Service\Cleaning;
use Illuminate\Http\Request;
use Illuminate\Support\Number;

class CleaningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Pages.Master.priceCleaning.index', [
            'title' =>  'Price Cleaning',
            'pageTitle' =>  'Price Cleaning',
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Cleaning $cleaning)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cleaning $cleaning)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cleaning $cleaning)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cleaning $cleaning)
    {
        //
    }

    function getAllData(Request $request)
    {
        $totalPriceCleanings = Cleaning::where('is_active', true)
            ->count();

        $filteredPriceCleanings = Cleaning::with(['category'])->where('is_active', true)
            ->search(['search' => $request->search['value']])
            ->count();

        $priceCleanings = Cleaning::with(['category'])->where('is_active', true)
            ->search(['search' => $request->search['value']])
            ->skip($request->start)
            ->limit($request->length)
            ->get();

        $results = array();
        $no = $request->start + 1;

        if ($priceCleanings) {
            foreach ($priceCleanings as $key => $value) {
                $btnAction = '<div class="d-flex gap-2">
                                    <button class="btn btn-warning" title="Ubah Data" onclick="fnPriceCleaning.onEdit(\'' . $value->kode_item . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    </button>
                                    <button class="btn btn-danger" title="Hapus Data" onclick="fnPriceCleaning.onDelete(\'' . $value->kode_item . '\',\'' . csrf_token() . '\')">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    </button>
                                </div>';

                $results[] = [
                    $no,
                    $value->category->name,
                    Number::currency($value->price, in: 'IDR', locale: 'id'),
                    $btnAction
                ];
            }
        }

        return response()->json([
            'draw'  =>  $request->draw,
            'recordsTotal'  =>  $totalPriceCleanings,
            'recordsFiltered'   =>  $filteredPriceCleanings,
            'data'  =>  $results,
        ]);
    }
}
