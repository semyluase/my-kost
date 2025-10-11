<?php

namespace App\Models;

use App\Models\Master\CategoryLaundry;
use App\Models\Master\Service\Cleaning;
use App\Models\Master\Service\Laundry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'tr_transaction_d';
    protected $guarded = ['id'];

    function foodSnack()
    {
        return $this->belongsTo(FoodSnack::class, 'code_item', 'code_item');
    }

    function stock()
    {
        return $this->belongsTo(Stock::class, 'code_item', 'code_item');
    }

    function categorylaundry()
    {
        return $this->belongsTo(Laundry::class, 'laundry_id', 'id');
    }

    function categoryCleaning()
    {
        return $this->belongsTo(Cleaning::class, 'code_item', 'kode_item');
    }

    function scopeGetReceipt($query, $nobukti)
    {
        return $query->select('*')
            ->fromRaw("(select ttd.id, ttd.nobukti, ttd.code_item, fs.name, sum(ttd.qty) as jumlah,
                        sum(ttd.qty) * ttd.harga_jual as sub_total from tr_transaction_d ttd
                        left join food_snacks fs
                        on ttd.code_item = fs.code_item
                        where nobukti = '$nobukti'
                        group by ttd.nobukti, ttd.code_item, fs.name ) tb");
    }
}
