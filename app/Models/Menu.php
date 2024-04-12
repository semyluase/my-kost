<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    function scopeMenuData($query, $role, $parent = 0)
    {
        return $query->select('*')
            ->fromRaw("(SELECT a.`id`, a.`label`, a.`url`, a.`icon`,
            a.`parent`, a.`index`, IFNULL(c.jumlah, 0) AS jumlah,
            IF (e.id <> '', 'true', 'false') AS selected
            FROM menus a
            LEFT JOIN (
                SELECT b.id, b.`parent`, COUNT(b.`id`) AS jumlah
                FROM menus b
                WHERE b.`is_active` = true
                GROUP BY b.`parent`
            ) AS c ON c.parent = a.`id`
            LEFT JOIN (
                SELECT *
                FROM role_menus d
                WHERE d.`role_id` = '$role'
            ) AS e ON e.menu_id = a.`id`
            WHERE a.`is_active` = true
            AND a.`parent` = $parent
            ORDER BY a.`index` ASC) tb");
    }
}
