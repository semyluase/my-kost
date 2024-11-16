<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $with = ['menu'];

    function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    function scopeGetMenu($query, $parent = 0, $role)
    {
        return $query->select('*')
            ->fromRaw("(SELECT a.id, a.label, a.url,
            a.active_value, a.icon, a.parent, a.index, a.is_active, IFNULL(menu.jumlah, 0) AS jumlah
            FROM menus a
            LEFT JOIN (
                SELECT COUNT(b.`id`) AS jumlah, b.`parent`
                FROM menus b
                GROUP BY b.`parent`
            ) AS menu ON menu.parent = a.`id`
            INNER JOIN role_menus c ON a.id = c.menu_id
            WHERE a.parent = '$parent'
            AND a.label != 'Logout'
            AND a.is_active = 1
            AND c.role_id = '$role'
            GROUP BY a.id, a.label, a.url,
            a.active_value, a.icon, a.parent, a.index, a.is_active) tb");
    }

    static public function createMenu()
    {
        $menu = '';
        $active = '';
        $dataMenu = RoleMenu::getMenu(0, auth()->user()->role_id)->orderBy('index')->get();

        // dd($dataMenu);
        if ($dataMenu) {
            foreach ($dataMenu as $row) {
                $active = request()->is($row->active_value) ? 'active' : '';
                $show = request()->is($row->active_value) ? 'show' : '';

                switch ($row->url) {
                    case '#':
                        $url = $row->url;
                        break;

                    case null:
                        $url = 'javascript:;';
                        break;

                    case 'dropdown':
                        $url = '#navbar-base';
                        break;

                    default:
                        $url = url('') . $row->url;
                        break;
                }

                $hasSub = $row->jumlah > 0 ? 'dropdown' : '';
                $dropdown = $row->jumlah > 0 ? 'dropdown-toggle' : '';

                $logout = $row->label === 'Logout' ? 'onclick="loggedOut(\'' . csrf_token() . '\')"' : '';

                $attrDropdown = $row->jumlah > 0 ? 'data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false"' . '"' : '';

                $item = '<li class="nav-item ' . $active . ' ' . $hasSub . '" >
                <a class="nav-link ' . $dropdown . '" href="' . $url . '" ' . $attrDropdown . '>
                    <span class="nav-link-icon d-md-none d-lg-inline-block">' . $row->icon . '</span>
                    <span class="nav-link-title">' . $row->label . '</span>
                </a>';

                $child = '';
                if ($row->jumlah > 0) {
                    $child .= static::childMenu($row);
                }
                $item .= $child;
                $item .= '</li>';
                $menu .= $item;
            }
        }

        return $menu;
    }

    public static function childMenu($child)
    {
        $show = request()->is($child->active_value) ? 'show' : '';

        $childMenuData = '<div class="dropdown-menu" data-bs-popper="static">
                                <div class="dropdown-menu-columns">
                                    <div class="dropdown-menu-column">';
        $childData = RoleMenu::getMenu($child->id, auth()->user()->role_id)->orderBy('index')->get();

        if ($childData) {
            foreach ($childData as $kc => $vc) {
                $childUrl = '';

                switch ($vc->url) {
                    case '#':
                        $childUrl = $vc->url;
                        break;

                    case null:
                        $childUrl = 'javascript:;';
                        break;

                    case 'dropdown':
                        $childUrl = '#navbar-base';
                        break;

                    default:
                        $childUrl = url('') . $vc->url;
                        break;
                }

                $childMenuData .= '<a class="dropdown-item" href="' . $childUrl . '">' . $vc->label . '</a>';
            }
        }
        $childMenuData .= '</div>
                            </div>
                        </div>';



        return $childMenuData;
    }
}
