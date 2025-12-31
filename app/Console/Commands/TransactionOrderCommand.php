<?php

namespace App\Console\Commands;

use App\Models\Email;
use App\Models\TransactionHeader;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel the order';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::beginTransaction();

        $dataTransaction = TransactionHeader::where('tgl_request', '<=', Carbon::now('Asia/Jakarta')->subDays()->isoFormat("YYYY-MM-DD"))
            ->where('is_receipt', false)
            ->where('pembayaran', 0)
            ->get();

        if ($dataTransaction) {
            foreach ($dataTransaction as $key => $value) {
                if (TransactionHeader::find($value->id)->update([
                    'status'    =>  6
                ])) {
                    $filePath = public_path('assets/invoice/' . $value->nobukti . '.pdf');

                    Email::create([
                        'to'    =>  $value->user->email,
                        'subject'   =>  "Receipt " . $value->nobukti,
                        "attachment"    =>  $filePath,
                        'no_invoice'    =>  $value->nobukti,
                        'is_order'  =>  true,
                    ]);

                    DB::commit();
                }
            }
        }
    }
}
