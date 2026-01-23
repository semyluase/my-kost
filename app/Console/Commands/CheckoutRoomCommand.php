<?php

namespace App\Console\Commands;

use App\Models\Deposite;
use App\Models\TransactionRent;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CheckoutRoomCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'room:checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checkout Room';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::beginTransaction();

        $dataRents = TransactionRent::where('end_date', Carbon::now('Asia/Jakarta')->isoFormat("YYYY-MM-DD"))
            ->where('is_checkout_abnormal', false)
            ->where('is_checkout_normal', false)
            ->where('is_upgrade', false)
            ->where('is_downgrade', false)
            ->get();

        if ($dataRents) {
            foreach ($dataRents as $key => $value) {
                if (TransactionRent::find($value->id)->update([
                    'is_checkout_normal'    =>  true,
                ])) {
                    $deposite = Deposite::where('user_id', $value->member->user->id)
                        ->where('room_id', $value->room_id)
                        ->where('is_checkout', false)
                        ->first();

                    if ($deposite) {
                        if (Deposite::find($deposite->id)->update([
                            'is_checkout'   =>  true
                        ])) {
                            DB::commit();
                        }
                    } else {
                        DB::commit();
                    }
                }
            }
        }
    }
}
