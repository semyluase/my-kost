<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleMenu;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::insert([
            [
                'name'  =>  'Super Admin',
                'slug'  =>  'super-admin',
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'name'  =>  'Admin',
                'slug'  =>  'admin',
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'name'  =>  'Member',
                'slug'  =>  'member',
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
        ]);

        Menu::insert([
            [
                'label'  =>  'Dashboard',
                'url'  =>  '/',
                'active_value'  =>  '/',
                'icon'  =>  '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-dashboard"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 13m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M13.45 11.55l2.05 -2.05" /><path d="M6.4 20a9 9 0 1 1 11.2 0z" /></svg>',
                'parent'  =>  '0',
                'index'  =>  '1',
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'label'  =>  'Logout',
                'url'  =>  'javascript:;',
                'active_value'  =>  '',
                'icon'  =>  '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-logout"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12l-3 -3" /><path d="M18 15l3 -3" /></svg>',
                'parent'  =>  '0',
                'index'  =>  '99999',
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'label'  =>  'Setting',
                'url'  =>  'dropdown',
                'active_value'  =>  'settings*',
                'icon'  =>  '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-settings-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19.875 6.27a2.225 2.225 0 0 1 1.125 1.948v7.284c0 .809 -.443 1.555 -1.158 1.948l-6.75 4.27a2.269 2.269 0 0 1 -2.184 0l-6.75 -4.27a2.225 2.225 0 0 1 -1.158 -1.948v-7.285c0 -.809 .443 -1.554 1.158 -1.947l6.75 -3.98a2.33 2.33 0 0 1 2.25 0l6.75 3.98h-.033z" /><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /></svg>',
                'parent'  =>  '0',
                'index'  =>  '99998',
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'label'  =>  'Master',
                'url'  =>  'dropdown',
                'active_value'  =>  'masters*',
                'icon'  =>  '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-layout-list"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M4 14m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /></svg>',
                'parent'  =>  '0',
                'index'  =>  '2',
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'label'  =>  'Role',
                'url'  =>  '/settings/roles',
                'active_value'  =>  'settings/roles*',
                'icon'  =>  '',
                'parent'  =>  '1',
                'index'  =>  '1',
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'label'  =>  'User',
                'url'  =>  '/settings/users',
                'active_value'  =>  'settings/users*',
                'icon'  =>  '',
                'parent'  =>  '1',
                'index'  =>  '2',
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'label'  =>  'Role Menu',
                'url'  =>  '/settings/role-menus',
                'active_value'  =>  'settings/role-menus*',
                'icon'  =>  '',
                'parent'  =>  '1',
                'index'  =>  '3',
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'label'  =>  'Location',
                'url'  =>  '/masters/locations',
                'active_value'  =>  'masters/locations*',
                'icon'  =>  '',
                'parent'  =>  '2',
                'index'  =>  '1',
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
        ]);

        User::create([
            'name' => 'Super Administrator',
            'role_id' => '1',
            'location_id' => '1',
            'username' => 'admin',
            'password' => bcrypt('password'),
        ]);

        RoleMenu::insert([
            [
                'role_id'   =>  1,
                'menu_id'   =>  1,
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'role_id'   =>  1,
                'menu_id'   =>  2,
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'role_id'   =>  1,
                'menu_id'   =>  3,
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'role_id'   =>  1,
                'menu_id'   =>  4,
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'role_id'   =>  1,
                'menu_id'   =>  5,
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'role_id'   =>  1,
                'menu_id'   =>  6,
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'role_id'   =>  1,
                'menu_id'   =>  7,
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
            [
                'role_id'   =>  1,
                'menu_id'   =>  8,
                'created_at'    =>  Carbon::now('Asia/Jakarta'),
                'updated_at'    =>  Carbon::now('Asia/Jakarta'),
            ],
        ]);
    }
}
