<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailInvoice as JobsSendEmailInvoice;
use App\Models\Email;
use App\Models\TransactionRent;
use Illuminate\Console\Command;

class SendEmailInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dataEmail = Email::where('is_send', false)
            ->get();

        if ($dataEmail) {
            foreach ($dataEmail as $key => $value) {
                $dataRent = TransactionRent::with(['member', 'member.user', 'room', 'room.category'])->where('no_invoice', $value->no_invoice)
                    ->first();
                JobsSendEmailInvoice::dispatch($value, $dataRent);
            }
        }
    }
}
