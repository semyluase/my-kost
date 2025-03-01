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
            ->fromRaw("(select ttd.* from tr_transaction_h tth
                left join tr_transaction_d ttd
                on tth.nobukti = ttd.nobukti
                where tth.tanggal between '$startDate' and '$endDate'
                and is_laundry = true) tb");
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
                    and is_topup = true) tb");
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
}
