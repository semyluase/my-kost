<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FoodSnack extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $with = ['picture'];

    function getRouteKeyName()
    {
        return 'code_item';
    }

    function scopeSearch($query, array $params)
    {
        $query->when($params['search'] ?? false, fn($query, $search) => ($query->where('name', "LIKE", "%$search%")->orWhere('category', "LIKE", "%$search%")->orWhere('price', "LIKE", "%$search%")));

        return $query;
    }

    function picture()
    {
        return $this->belongsTo(FoodSnackPicture::class, 'id', 'food_snack_id');
    }

    function categoryOrder()
    {
        return $this->belongsTo(CategoryOrder::class, 'category', 'short_name');
    }

    function stock()
    {
        if (Auth::user()->role->slug == 'super-admin') {
            return $this->belongsTo(Stock::class, 'code_item', 'code_item');
        }

        return $this->belongsTo(Stock::class, 'code_item', 'code_item')->where('home_id', Auth::user()->home_id);
    }

    function scopeGetData($query)
    {
        return $query->select('*')
            ->fromRaw("(select fs.id, fs.code_item, fs.name, fs.category, fs.price,
                        sum(ifnull(a.qty_awal,0)) as qty_awal, sum(ifnull(a.qty_in,0)) as qty_in, sum(ifnull(a.qty_out,0)) as qty_out
                        from food_snacks fs
                        left join (select ttd.code_item,
                        if(tth.tanggal < '" . Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD") . "', sum(ttd.qty), 0) as qty_awal,
                        if(tth.tanggal >= '" . Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD") . "' and tth.tanggal <= '" . Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD") . "' and `type` = 'in', sum(ttd.qty), 0) as qty_in,
                        if(tth.tanggal >= '" . Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD") . "' and tth.tanggal <= '" . Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD") . "' and `type` = 'out', sum(ttd.qty), 0) as qty_out
                        from tr_transaction_d ttd
                        left join tr_transaction_h tth
                        on ttd.nobukti = tth.nobukti
                        group by ttd.code_item) a
                        on fs.code_item = a.code_item
                        group by fs.id, fs.code_item, fs.name, fs.category, fs.price) tb");
    }
}
