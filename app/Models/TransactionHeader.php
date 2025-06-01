<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TransactionHeader extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'tr_transaction_h';
    protected $guarded = ['id'];

    function getRouteKeyName()
    {
        return 'nobukti';
    }

    function details()
    {
        return $this->hasMany(TransactionDetail::class, 'nobukti', 'nobukti');
    }

    function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    function scopeGetDataLaundry($query, $startDate, $endDate)
    {
        return $query->select('*')
            ->fromRaw("(select ttd.*, l.name from tr_transaction_h tth
                left join tr_transaction_d ttd
                on tth.nobukti = ttd.nobukti
                left join laundries l
                on ttd.laundry_id = l.id
                where tth.tanggal between '$startDate' and '$endDate'
                and tth.is_laundry = true) tb");
    }

    function scopeGetDataCleaning($query, $tgl)
    {
        $startDate = Carbon::parse($tgl)->startOfDay();
        $endDate = Carbon::parse($tgl)->endOfDay();

        return $query->select('*')
            ->fromRaw("(select ttd.* from tr_transaction_h tth
                    left join tr_transaction_d ttd
                    on tth.nobukti = ttd.nobukti
                    where ttd.tgl_request_cleaning between '$startDate' and '$endDate'
                    and ttd.is_cleaning = true) tb");
    }

    function scopeGetDataTopup($query, $tgl)
    {
        return $query->select('*')
            ->fromRaw("(select ttd.*, u.name from tr_transaction_h tth
                    left join tr_transaction_d ttd
                    on tth.nobukti = ttd.nobukti
                    left join users u
                    on ttd.user_id = u.id
                    where tth.tanggal = '$tgl'
                    and tth.is_topup = true) tb");
    }

    function scopeGetDataTransaction($query, $startDate, $endDate)
    {
        return $query->select('*')
            ->fromRaw("(select tth.nobukti, tth.tanggal, tth.status, tth.created_at, tth.updated_at,
                    r.number_room, tth.total, tth.tipe_pembayaran, tth.pembayaran,
                    tth.kembalian from tr_transaction_h tth
                    left join rooms r
                    on tth.room_id = r.id
                    where tth.tanggal between '$startDate' and '$endDate'
                    and tth.is_order = true) tb");
    }

    function scopeGetDataReportDetail($query, $startDate, $endDate, $codeItem)
    {
        return $query->select('*')
            ->fromRaw("(select code_item, name, tanggal, sum(qty_in) as qty_in, harga_beli,
                    sum(qty_out) as qty_out, harga_jual
                    from (select fs.code_item, fs.name, tth.tanggal, sum(ttd.qty) as qty_in,
                    ttd.harga_beli, 0 as qty_out, 0 as harga_jual from food_snacks fs 
                    join tr_transaction_d ttd 
                    on fs.code_item = ttd.code_item
                    and ttd.`type` = 'IN'
                    join tr_transaction_h tth 
                    on ttd.nobukti = tth.nobukti
                    where fs.code_item = '$codeItem'
                    and tth.tanggal between '$startDate' and '$endDate'
                    group by fs.code_item, fs.name, tth.tanggal, ttd.harga_beli
                    union all
                    select fs.code_item, fs.name, tth.tanggal, 0 as qty_in,
                    0 as harga_beli, sum(ttd.qty) as qty_out, ttd.harga_jual from food_snacks fs 
                    join tr_transaction_d ttd 
                    on fs.code_item = ttd.code_item
                    and ttd.`type` = 'OUT'
                    join tr_transaction_h tth 
                    on ttd.nobukti = tth.nobukti
                    where fs.code_item = '$codeItem'
                    and tth.tanggal between '$startDate' and '$endDate'
                    group by fs.code_item, fs.name, tth.tanggal, ttd.harga_beli) a
                    group by  code_item, name, tanggal, harga_beli, harga_jual) tb");
    }
}
