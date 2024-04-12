<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\RoleMenu;
use Illuminate\Http\Request;

class RoleMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Pages.Setting.RoleMenu.index', [
            'title' =>  'Role Menu',
            'pageTitle' =>  'Role Menu'
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
        for ($i = 0; $i < count($request->menu); $i++) {
            $data[] = [
                'menu_id' =>    $request->menu[$i],
                'role_id'  =>  $request->role,
                'created_at'    =>  now('Asia/Jakarta'),
                'updated_at'    =>  now('Asia/Jakarta'),
            ];
        }

        RoleMenu::where('role_id', $request->role)->delete();

        if (RoleMenu::insert($data)) {
            return response()->json([
                'data' => [
                    'status'    =>  true,
                    'message'   =>  'Akses Menu berhasil disimpan'
                ]
            ]);
        }
        return response()->json([
            'data' => [
                'status'    =>  false,
                'message'   =>  'Akses Menu gagal disimpan'
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(RoleMenu $roleMenu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoleMenu $roleMenu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RoleMenu $roleMenu)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoleMenu $roleMenu)
    {
        //
    }

    function getMenuData(Request $request)
    {
        $dataMenu = Menu::menuData($request->r, 0)->get();

        $response = [];

        if ($dataMenu) {
            foreach ($dataMenu as $row => $val) {
                $response[$row] = [
                    'id' => $val->id,
                    'text' => $val->label,
                    // 'icon' => $val->icon
                ];

                if ($val->selected == 'true') {
                    $response[$row]['state'] = [
                        'selected' => true,
                        'opened'    =>  true
                    ];
                }

                if ($val->jumlah > 0) {
                    $children = Menu::menuData($request->r, $val->id)->get();

                    if ($children) {
                        $child_menu = [];
                        foreach ($children as $child => $child_val) {
                            $child_menu[$child] = [
                                'id' => $child_val->id,
                                'text' => $child_val->label,
                                // 'icon' => $child_val->icon
                            ];

                            if ($child_val->selected == 'true') {
                                $child_menu[$child]['state'] = [
                                    'selected' => true,
                                    'opened' => true
                                ];
                            }
                        }

                        $response[$row]['children'] = $child_menu;
                    }
                }
            }
        }

        return response()->json($response);
    }
}
